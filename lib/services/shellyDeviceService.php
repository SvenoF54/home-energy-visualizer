<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ShellyDeviceService
{
    private const KEY_VOLTAGE = "voltage";
    private const KEY_TEMP = "temp";
    private $kvsTable;
    private $errors = array();

    public function __construct() {
        $this->kvsTable = KeyValueStoreTable::getInstance();
        //$this->config = Configuration::getInstance()->zendure();

        //$this->zendureStatsSet = new ZendureStatsSet();
        //$this->zendureStatsSet->loadData();
    }

    // The $measuredPmxEnergieData is the enrgy which was given from Zendure to the house
    public function prepareDashboardData($measuredPmxEnergieData)
    {
        $resultData = [];
        $shellyKvsRow = $this->kvsTable->getRow(KeyValueStoreScopeEnum::Shelly, "litime");
        if (! isset($shellyKvsRow)) {
            $resultData["isDataloss"] = true; 
            return $resultData;
        }
        $jsonData = json_decode($shellyKvsRow->getJsonData());
        $voltage = $jsonData->{self::KEY_VOLTAGE} ?? 0;
        $minVoltage10Percent = 24.2;
        $maxVoltage100Percent = 27.5;  // Oder 28.23, wenn kein WR an ist und die Ladung in Float übergeht
        $voltagePercent = 0;
        if ($voltage > 0 && $minVoltage10Percent > 0 && $maxVoltage100Percent > 0) {
            $voltagePercent = ($voltage - $minVoltage10Percent) / ($maxVoltage100Percent - $minVoltage10Percent) * 90 + 10;
        }
        //var_dump($jsonData);die;

        $device = ["isDataloss" => false];  
        $device["akkuPackVoltage"] = $voltage;
        $device["akkuPackLevelPercent"] = $voltagePercent;
        $device["akkuPackRemainingEnergy"] = number_format(2000 * ($voltagePercent / 100));
        $device["chargePackPowerCalc"] = 0;
        $device["isChargeActive"] = false;
        $device["isDischargeActive"] = $measuredPmxEnergieData != 0;
        $device["dischargePackPowerCalc"] = (float) $measuredPmxEnergieData;

        $device["temp"] = $jsonData->{self::KEY_TEMP} ?? 0;
        $device["timestamp"] = $shellyKvsRow->getUpdated();

        return $device;
    }

}
