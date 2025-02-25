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

    public function __construct()
    {
        $this->realTimeService = new RealtimeService();
        $this->hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
    }

    public function prepareInstantData()
    {
        $this->realTimeService->readLatestData();

        $strStart = date('Y-m-d 00:00:00');
        $strEnd = date('Y-m-d 23:59:59');
        $avg = 86400;  // Sekunden pro Tag
        $this->todayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

        $strStart = date('Y-m-d H:00:00');
        $strEnd = date('Y-m-d H:59:59');
        $avg = 3600;  // Sekunden pro Tag
        $this->currentHourData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

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
        $now["pmPercent"] = ($latestRealTimeRow->getPmTotalPower() / 1000) * 100;
        $now["isZeroFeedInActive"] = $this->realTimeService->isZeroFeedInActive();
        
        $result = [
            "now" => $now,
            "today" => $today,            
            "currenthour" => $currentHour,
        ];        

        return json_encode($result);
    }

    public function getYesterdayData() : EnergyDataSet { return $this->yesterdayData; }
    public function getTodayData() : EnergyDataSet { return $this->todayData; }
    public function getCurrentHourData() : EnergyDataSet { return $this->currentHourData; }


}
