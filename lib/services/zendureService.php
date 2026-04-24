<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>


class ZendureService
{
    private const TIMEOUT_READ_DATA_FOR_DASHBOARD_IN_MINUTES = 10*60;
 
    private $kvsTable;
    private $config;
    private $readDataError;
    private ZendureStatsSet $zendureStatsSet;

    public function __construct() {
        $this->kvsTable = KeyValueStoreTable::getInstance();
        $this->config = Configuration::getInstance()->zendure();
        $this->readDataError = "";

        $this->zendureStatsSet = new ZendureStatsSet();
        $this->zendureStatsSet->loadData();
    }

    public function parseAndSaveData(array $data) {
        try {
            // Extract ZendureData for each System with keys zendure1, zendure2, ...
            $zendureSystems = isset($data['zendureData']) ? $data['zendureData'] : [];

            foreach ($zendureSystems as $zKey => $zValues) {
                // Extract Phase number from key (ie. "zendure1" -> "1")
                $phaseNumber = str_replace('zendure', '', $zKey);
                
                // Define Scope dynamicly, ie. KeyValueStoreScopeEnum::ZendurePhase1 and ensure that it exists 
                $scopeName = "ZendurePhase" . $phaseNumber;
                if (!defined("KeyValueStoreScopeEnum::$scopeName")) {
                    continue; 
                }
                $scope = constant("KeyValueStoreScopeEnum::$scopeName");

                if (is_array($zValues)) {
                    // 1. Save single values per Phase
                    foreach ($this->getZendureKeys() as $key => $notice) {
                        if (isset($zValues[$key])) {
                            $this->kvsTable->insertOrUpdate($scope, $key, $zValues[$key], $notice);
                            // Falls die Stats-Klasse Phasen unterstützt, hier anpassen:
                            $this->zendureStatsSet->update($key, $zValues[$key]);
                        }
                    }

                    // 2. Calculate pack capacity per phase
                    if (isset($zValues['packTypes'])) {
                        $phaseCapacity = 0;
                        $packTypes = explode(",", $zValues['packTypes']);
                        foreach ($packTypes as $type) {
                            $phaseCapacity += $this->convertPackTypeToCapacity($type);
                        }
                        $this->kvsTable->insertOrUpdate($scope, "totalPackCapacity", $phaseCapacity, "Gesamtkapazität Akkus Phase " . $phaseNumber);
                    }
                }
            }

            // Finale Status-Entry
            $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Task, TaskEnum::ReadZendureData->value, StatusEnum::Success->value, "Daten für " . count($zendureSystems) . " Phasen empfangen.");

        } catch (Exception $ex) { 
            $this->readDataError = $ex->getMessage();
        }
        
        return true;
    }

    public function getError() {
        return $this->readDataError;
    }

    private function getZendureKeys()
    {
        $keys = [
            "solarInputPower"           => "Aktuelle Solarleistung über alle Eingänge in W",
            "electricLevel"             => "Ladestand über alle Batterien in %", 
            "socSet"                    => "(Obere) Ladegrenze in % * 10",
            "packInputPower"            => "Aktuelle Entladeleistung der Batterien in W",
            "outputPackPower"           => "Aktuelle Ladeleistung der Batterien in W",
            "packState"                 => "Status über alle Batterien (0: Standby, 1: Laden, 2: Entladen)",
            "hyperTmp"                  => "Temperatur Wechselrichter (hyper_temp / 10 - 273.15)",
            "packTypes"                 => "Liste vorhandener Packtypes (300=AB2000X)"
        ];

        return $keys;
    }

    private function convertPackTypeToCapacity($packType) {
        $capacities = [
            100 => ['wh' => 810,  'kwh' => 0.81,  'ah' => 42,  'model' => 'AB1000'],
            200 => ['wh' => 960,  'kwh' => 0.96,  'ah' => 50,  'model' => 'SolarFlow 800'],
            300 => ['wh' => 1920, 'kwh' => 1.92,  'ah' => 100, 'model' => 'SolarFlow 800 Pro'],
            400 => ['wh' => 1920, 'kwh' => 1.92,  'ah' => 100, 'model' => 'AB2000/AB2000S'],
            500 => ['wh' => 3072, 'kwh' => 3.07,  'ah' => 160, 'model' => 'AB3000X'],
            600 => ['wh' => 2560, 'kwh' => 2.56,  'ah' => 134, 'model' => 'Hyper 2000']
        ];
    
        return isset($capacities[$packType]) ? $capacities[$packType]['wh'] : null;
    }

    public function getActivePhases()
    {
        $activePhases = [];
        $oneWeekInSeconds = 604800; // 7 Tage * 24h * 3600s
        
        for ($phase = 1; $phase <= 3; $phase++) {            
            $scope = constant("KeyValueStoreScopeEnum::ZendurePhase$phase");
            
            // Wir prüfen den "statistics" Key als Indikator für eine aktive Phase
            $row = $this->kvsTable->getRow($scope, "solarInputPower");
            
            if ($row !== null) {
                $updated = strtotime($row->getUpdated());
                // Active if data are newwer than 7 days
                $activePhases[$phase] = (time() - $updated) < $oneWeekInSeconds;
            } else {
                // No actual data available
                $activePhases[$phase] = false;
            }
        }
        
        return $activePhases;
    }


    public function prepareDashboardData()
    {
        $resultData = [];
        $resultData["systemProductionTotal"] = 0; // Aggregierter Wert
        
        // Globale Dataloss-Check
        $latestLogRow = $this->kvsTable->getRow(KeyValueStoreScopeEnum::Task, TaskEnum::ReadZendureData->value);
        $updated = $latestLogRow ? strtotime($latestLogRow->getUpdated()) : 0;
        $dataLoss = (time() - $updated) > (static::TIMEOUT_READ_DATA_FOR_DASHBOARD_IN_MINUTES * 60);
        $resultData["isDataloss"] = $dataLoss;

        if ($dataLoss) {
            return $resultData;
        }
        for ($phase = 1; $phase <= 3; $phase++) {
            $scope = constant("KeyValueStoreScopeEnum::ZendurePhase$phase");

            $zendureKvsData = ["solarInputPower" => 0, "electricLevel" => 0, "outputPackPower" => 0, "packInputPower" => 0, "packState" => 0, "totalPackCapacity" => 0, "hyperTmp" => 0]; 
            $rows = $this->kvsTable->getRowsForScope($scope);
            
            // If phase has no data continue
            if (empty($rows)) continue;

            foreach ($rows as $row) {            
                $zendureKvsData[$row->getStoreKey()] = $row->getValue(); 
            }

            // Prepare data for current phase
            $phaseData = [];
            $phaseData["solarInputPower"] = (int)$zendureKvsData["solarInputPower"];
            $phaseData["akkuPackLevelPercent"] = (int)$zendureKvsData["electricLevel"];
            
            $remaining = ((int)$zendureKvsData["totalPackCapacity"] * (int)$zendureKvsData["electricLevel"] / 100);
            $phaseData["akkuPackRemainingEnergy"] = $remaining;
            
            $phaseData["hyperTmp"] = $zendureKvsData["hyperTmp"] > 0 ? (($zendureKvsData["hyperTmp"] / 10) - 273.15) : 0;

            $phaseData["chargePower"] = (int)$zendureKvsData["outputPackPower"];
            $phaseData["dischargePower"] = (int)$zendureKvsData["packInputPower"];

            $phaseData["batterieChangingPower"] = $phaseData["chargePower"] > 0 ? $phaseData["chargePower"] : -$phaseData["dischargePower"];
            if ($phaseData["batterieChangingPower"] == 0) $phaseData["batterieChangingPower"] = "-";

            $phaseData["isChargeActive"] = $zendureKvsData["packState"] == 1 && $phaseData["chargePower"] > 0;
            $phaseData["isDischargeActive"] = $zendureKvsData["packState"] == 2 && $phaseData["dischargePower"] > 0;

            $phaseData["productionTotal"] = $phaseData["solarInputPower"] + $phaseData["dischargePower"];

            // Add each phase to result array
            $resultData["phase" . $phase] = $phaseData;

            // Aggregate total system production
            $resultData["systemProductionTotal"] += $phaseData["productionTotal"];
        }

        return $resultData;
    }

}
