<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class DashboardService
{
    private $realTimeService;
    private $hourlyEnergyDataTbl;
    private EnergyDataSet $todayData;
    private EnergyDataSet $yesterdayData;
    private EnergyDataSet $currentHourData;
    private RealTimeEnergyDataRow $realTimeData;
    private $zendureData;    

    public function __construct()
    {
        $this->realTimeService = new RealtimeService();
        $this->hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
    }

    public function prepareInstantData()
    {
        $this->realTimeService->readLatestData();
        $this->realTimeData = $this->realTimeService->getLatestDataRow();

        $strStart = date('Y-m-d 00:00:00');
        $strEnd = date('Y-m-d 23:59:59');
        $avg = 86400;  // Sekunden pro Tag
        $this->todayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

        $strStart = date('Y-m-d H:00:00');
        $strEnd = date('Y-m-d H:59:59');
        $avg = 3600;  // Sekunden pro Tag
        $this->currentHourData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

        $this->prepareZendureData();
    }

    /// Zendure liefert als einzigen regelmäßigen Wert die solarInputPower, d.h. die anderen Werte müssen entsprechend berechnet werden.
    private function prepareZendureData()
    {
        // Prepare data
        $kvsTable = KeyValueStoreTable::getInstance();
        $zendureKvsData = ["solarInputPower" => 0, "electricLevelPercent" => 0]; // Todo keys vorfüllen
        foreach ($kvsTable->getRowsForScope(KeyValueStoreScopeEnum::Zendure) as $row) {
            $zendureKvsData[$row->getStoreKey()] = $row->getValue();
        }

        // Prepare result set
        $this->zendureData = [];
        // Aktuelle Solarleistung über alle Eingänge in W
        $this->zendureData["solarInputPower"] = isset($zendureKvsData["solarInputPower"]) ? $zendureKvsData["solarInputPower"] : 0;
        // Ladestand über alle Batterien in %        
        $this->zendureData["electricLevelPercent"] = isset($zendureKvsData["electricLevel"]) ? $zendureKvsData["electricLevel"] : 0;
        
        // Pack charge calculation
        $currentAkkuCharge = $this->zendureData["solarInputPower"] - $this->realTimeData->getPm3TotalPower();        
        $this->zendureData["chargePackPowerCalc"] = $currentAkkuCharge > 0 ? $currentAkkuCharge : 0;        // Aktuelle Ladeleistung der Batterien in W
        $this->zendureData["isZendureChargeActive"] = $currentAkkuCharge > 0;                         // Ladung aktiv
        $this->zendureData["dischargePackPowerCalc"] = $currentAkkuCharge < 0 ? -$currentAkkuCharge : 0;    // Aktuelle Entladeleistung der Batterien in W
        $this->zendureData["isZendureDischargeActive"] = $currentAkkuCharge < 0;                      // Entladung aktiv

        // Zendure production
        $this->zendureData["production"] = $this->zendureData["solarInputPower"] + $this->zendureData["dischargePackPowerCalc"];
    }

    public function prepareStaticData()
    {
        $strStart = date('Y-m-d 00:00:00', strtotime("- 1 day"));
        $strEnd = date('Y-m-d 23:59:59', strtotime("- 1 day"));
        $avg = 86400;  // Sekunden pro Tag
        $this->yesterdayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);        
    }

    public function getStaticDataAsJson()
    {
        $this->prepareInstantData();
        $yesterday = $this->yesterdayData->convertEnergyToJsArray() + $this->yesterdayData->convertAutarkyToJsArray();
        $result = [
            "yesterday" => $yesterday,
        ];
        
        return json_encode($result);
    }

    public function getInstantDataAsJson()
    {        
        $today = $this->todayData->convertEnergyToJsArray() + $this->todayData->convertAutarkyToJsArray();
        $currentHour = $this->currentHourData->convertEnergyToJsArray() + $this->currentHourData->convertAutarkyToJsArray();
        
        $latestRealTimeRow = $this->realTimeService->getLatestDataRow();
        $now = $latestRealTimeRow->convertToJsArray();        
        $now["emPercent"] = abs($latestRealTimeRow->getEmTotalPower() / 6000 * 100);
        $now["pmPercent"] = ($latestRealTimeRow->getPmTotalPower() / 2000) * 100;
        $now["production"] = $now["pm1"] + $this->zendureData["production"];
        $now["productionPercent"] = ($now["production"] / 2000) * 100;
        $now["isZeroFeedInActive"] = $this->realTimeService->isZeroFeedInActive();
        
        $result = [
            "now" => $now,
            "today" => $today,            
            "currenthour" => $currentHour,
            "zendure" => $this->zendureData
        ];        

        return json_encode($result);
    }

    public function getYesterdayData() : EnergyDataSet { return $this->yesterdayData; }
    public function getTodayData() : EnergyDataSet { return $this->todayData; }
    public function getCurrentHourData() : EnergyDataSet { return $this->currentHourData; }


}
