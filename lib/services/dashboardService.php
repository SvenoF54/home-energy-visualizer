<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class DashboardService
{
    private $realTimeEnergyDataTbl;
    private $hourlyEnergyDataTbl;
    private $latestRealtimeRow;
    private $todayData;
    private $currentHourData;

    public function __construct()
    {
        $this->realTimeEnergyDataTbl = RealTimeEnergyDataTable::getInstance();
        $this->hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
    }

    public function prepareData()
    {
        $startTime = date('Y-m-d H:i:s', strtotime("- 65 seconds"));
        $endTime = date('Y-m-d H:i:s', strtotime("- 5 seconds"));
        $dataRows = $this->realTimeEnergyDataTbl->getOverviewData($startTime, $endTime, 5);
        $this->latestRealtimeRow = end($dataRows);

        $strStart = date('Y-m-d 00:00:00');
        $strEnd = date('Y-m-d 23:59:59');
        $avg = 86400;  // Sekunden pro Tag
        $this->todayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

        $strStart = date('Y-m-d H:00:00');
        $strEnd = date('Y-m-d H:59:59');
        $avg = 3600;  // Sekunden pro Tag
        $this->currentHourData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

    }

    public function getDataAsJson()
    {
        
        $today = $this->todayData->convertEnergyToJsArray() + $this->todayData->convertAutarkyToJsArray();
        $currentHour = $this->currentHourData->convertEnergyToJsArray() + $this->currentHourData->convertAutarkyToJsArray();
        $now = $this->latestRealtimeRow->convertToJsArray();
        
        $now["emPercent"] = ($this->latestRealtimeRow->getEmTotalPower() + 1000) / (10000 + 1000) * 100;
        $now["pmPercent"] = ($this->latestRealtimeRow->getPmTotalPower() / 1000) * 100;
        
        $result = [
            "now" => $now,
            "today" => $today,            
            "currenthour" => $currentHour,
        ];

        return json_encode($result);
    }



}
