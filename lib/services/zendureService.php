<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>


# https://github.com/bluerhinos/phpMQTT/tree/master

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
            $inverterData = $data["properties"];
        
            foreach ($this->getZendureKeys() as $key => $notice) {
                if ($inverterData && isset($inverterData[$key])) {
                    $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Zendure, $key, $inverterData[$key], $notice);
                    $this->zendureStatsSet->update($key, $inverterData[$key]);
                }
            }        
            $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Task, TaskEnum::ReadZendureData->value, StatusEnum::Success->value, "Zenduredaten via Shell-Taskrunner empfangen.");
        } catch (Exception $ex) {
            $this->readDataError = $ex->getMessage();
        }
        
        return true;
    }

    public function getError() {
        return $this->readDataError;
    }

    public function getZendureKeys()
    {
        $keys = [
            $this->config->getKeySolarInput()           => "Aktuelle Solarleistung über alle Eingänge in W",
            $this->config->getKeyPackCapacityPercent()  => "Ladestand über alle Batterien in %", 
            $this->config->getKeyPackMaxUpperLoadLimit()=> "(Obere) Ladegrenze in % * 10",
            $this->config->getKeyPackDischarge()        => "Aktuelle Entladeleistung der Batterien in W",
            $this->config->getKeyPackCharge()           => "Aktuelle Ladeleistung der Batterien in W",
            $this->config->getKeyPackState()            => "Status über alle Batterien (0: Standby, 1: Laden, 2: Entladen)"
        ];

        return $keys;

        // Possible interesting keys, but most of their values are not send very often
        $keys = [
            "electricLevel"    => "Ladestand über alle Batterien in %", 

            "solarInputPower"  => "Aktuelle Solarleistung über alle Eingänge in W", 
            "solarPower1"      => "Aktuelle Solarleistung Eingang PV 1 in W", 
            "solarPower2"      => "Aktuelle Solarleistung Eingang PV 2 in W", 

            "outputHomePower"  => "Aktuelle Leistungsabgabe ans 'Haus' in W nur im Terminmmodus",
            "gridInputPower"   => "Leistungsbezug aus dem Netz in W", 
            "inverseMaxPower"  => "Maximal zulässige Abgabeleistung ans 'Haus' / gestzl. Obergrenze in W", 
            "outputLimit"      => "Maximale Leistungsabgabe ans 'Haus' (Obergrenze) in W",

            "packInputPower"   => "Aktuelle Entladeleistung der Batterien in W", 
            "outputPackPower"  => "Aktuelle Ladeleistung der Batterien in W", 
            "remainOutTime"    => "Verbleibende Zeit bis Batterien entladen in min. ", 
            "remainInputTime"  => "Verbleibende Zeit bis Batterien geladen in min. ", 
            "packState"        => "Status über alle Batterien (0: Standby, 1: Laden, 2: Entladen)",
            "socSet"           => "(Obere) Ladegrenze in % * 10", 

            "sn"               => "Seriennummer"
           ];

        return $keys;
    }

    // Zendure only sends solarInputPower and electricLevel intime. So akku charging will be calculated.
    // The $measuredPmxEnergieData is the enrgy which was given from Zendure to the house
    public function prepareDashboardData($measuredPmxEnergieData)
    {
        $keySolarInput = $this->config->getKeySolarInput();
        $keyAkkuCapacity = $this->config->getKeyPackCapacityPercent();
        $keyPackCharge = $this->config->getKeyPackCharge();
        $keyPackDischarge = $this->config->getKeyPackDischarge();
        $keyPackState = $this->config->getKeyPackState();
        $resultData = [];
        
        // Read latest Zendure data from DB
        $zendureKvsData = [$keySolarInput => 0, $keyAkkuCapacity => 0, $keyPackCharge => 0, $keyPackDischarge => 0, $keyPackState => 0];
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
        $resultData["solarInputPower"] = isset($zendureKvsData[$keySolarInput]) ? $zendureKvsData[$keySolarInput] : 0;
        // Current pack capacity over all in %        
        $resultData["akkuPackLevelPercent"] = isset($zendureKvsData[$keyAkkuCapacity]) ? $zendureKvsData[$keyAkkuCapacity] : 0;
        $remainingEnergy = isset($zendureKvsData[$keyAkkuCapacity]) ? ($this->config->getPackCapacityInW() * $zendureKvsData[$keyAkkuCapacity] / 100) : 0;
        $resultData["akkuPackRemainingEnergy"] = $remainingEnergy;
        
        // Pack charge calculation, only if zero feed in is active because zendure data are not send very often
        if ($this->config->getCalculatePackData()) {
            $currentAkkuCharge = $resultData["solarInputPower"] - $measuredPmxEnergieData;              // Subtract measured PM data, which is requested from the house
            $resultData["chargePackPowerCalc"] = $currentAkkuCharge > 0 ? $currentAkkuCharge : 0;       // Caclulated current pack charging W
            $resultData["dischargePackPowerCalc"] = $currentAkkuCharge < 0 ? $currentAkkuCharge : 0;    // Caclulated current pack discharging W
        } else {
            $resultData["chargePackPowerCalc"] = $zendureKvsData[$keyPackCharge];
            $resultData["dischargePackPowerCalc"] = -$zendureKvsData[$keyPackDischarge];
        }
        $resultData["isChargeActive"] = $zendureKvsData[$keyPackState] == 1 && $resultData["chargePackPowerCalc"] > 0;          // Pack charging active
        $resultData["isDischargeActive"] = $zendureKvsData[$keyPackState] == 2 && $resultData["dischargePackPowerCalc"] < 0;    // Pack discharging active

        // Zendure production
        $resultData["productionTotal"] = $resultData["solarInputPower"] + $resultData["dischargePackPowerCalc"];
        $resultData["productionUsedInternal"] = $resultData["chargePackPowerCalc"];

        return $resultData;
    }

}
