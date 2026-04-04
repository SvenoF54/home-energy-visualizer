<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>


class ZendureService
{
    private const TIMEOUT_MQQT_DATA_FOR_DASHBOARD_IN_MINUTES = 10*60;
 
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
            // Read and store needed inverter data            
            foreach ($this->getZendureKeys() as $key => $notice) {
                if ($data && isset($data[$key])) {
                    $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Zendure, $key, $data[$key], $notice);
                    $this->zendureStatsSet->update($key, $data[$key]);
                }
            }

            // Capacity over all packs in this system
            $totalCapacity = 0;            
            $packTypes = explode(",", $data['packTypes']);
            foreach($packTypes as $type) {
                $totalCapacity += $this->convertPackTypeToCapacity($type);
            }
            $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Zendure, "totalPackCapacity", $totalCapacity, "Gesamtkapazität aller Akkus dieses Zendure-Systems");

            // Write final read zendure data msg
            $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Task, TaskEnum::ReadZendureData->value, StatusEnum::Success->value, "Zenduredaten via Shell-Taskrunner empfangen.");
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
    // Zendure only sends solarInputPower and electricLevel intime. So akku charging will be calculated.
    // The $measuredPmxEnergieData is the enrgy which was given from Zendure to the house
    public function prepareDashboardData($measuredPmxEnergieData)
    {
        $resultData = [];
        
        // Read latest Zendure data from DB
        $zendureKvsData = ["solarInputPower" => 0, "electricLevel" => 0, "outputPackPower" => 0, "packInputPower" => 0, "packState" => 0, "totalPackCapacity" => 0]; 
        $latestLogRow = $this->kvsTable->getRow(KeyValueStoreScopeEnum::Task, TaskEnum::ReadZendureData->value);
        $updated = strtotime($latestLogRow->getUpdated());
        $dataLoss = (time() - $updated) > (static::TIMEOUT_MQQT_DATA_FOR_DASHBOARD_IN_MINUTES);
        if (! $dataLoss) {
            foreach ($this->kvsTable->getRowsForScope(KeyValueStoreScopeEnum::Zendure) as $row) {            
                $zendureKvsData[$row->getStoreKey()] = $row->getValue(); 
            }
        }

        // Prepare result set
        $resultData = ["isDataloss" => $dataLoss];        
        // Current solar energy over all in W
        $resultData["solarInputPower"] = isset($zendureKvsData["solarInputPower"]) ? $zendureKvsData["solarInputPower"] : 0;
        // Current pack capacity over all in %        
        $resultData["akkuPackLevelPercent"] = isset($zendureKvsData["electricLevel"]) ? $zendureKvsData["electricLevel"] : 0;
        $remainingEnergy = isset($zendureKvsData["electricLevel"]) && isset($zendureKvsData["totalPackCapacity"]) ? ($zendureKvsData["totalPackCapacity"] * $zendureKvsData["electricLevel"] / 100) : 0;
        $resultData["akkuPackRemainingEnergy"] = $remainingEnergy;
        
        // Temperature
        $resultData["hyperTmp"] = isset($zendureKvsData["hyperTmp"]) ? (($zendureKvsData["hyperTmp"] / 10) - 273.15) : 0;

        $resultData["chargePower"] = $zendureKvsData["outputPackPower"];
        $resultData["dischargePower"] = $zendureKvsData["packInputPower"];        

        $resultData["batterieChangingPower"] = $resultData["chargePower"] > 0 ? $resultData["chargePower"] : -$resultData["dischargePower"];
        $resultData["batterieChangingPower"] = $resultData["batterieChangingPower"] == 0 ? "-" : $resultData["batterieChangingPower"];
        $resultData["isChargeActive"] = $zendureKvsData["packState"] == 1 && $resultData["chargePower"] > 0;        // Pack charging active
        $resultData["isDischargeActive"] = $zendureKvsData["packState"] == 2 && $resultData["dischargePower"] > 0;  // Pack discharging active

        // Zendure production
        $resultData["productionTotal"] = $resultData["solarInputPower"] + $resultData["dischargePower"];
        $resultData["productionUsedInternal"] = $resultData["chargePower"];

        return $resultData;
    }

}
