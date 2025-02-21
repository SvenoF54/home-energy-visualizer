<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class DashboardService
{
    private $realTimeEnergyDataTbl;
    private $hourlyEnergyDataTbl;
    private $latestRealtimeRow;
    private $nowZeroFeedInActive;
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
        //$startTime = date('Y-m-d H:i:s', strtotime("- 5 hours -60 seconds"));
        //$endTime = date('Y-m-d H:i:s', strtotime("- 5 hours"));
        $dataRows = $this->realTimeEnergyDataTbl->getOverviewData($startTime, $endTime, 5);                
        $this->latestRealtimeRow = end($dataRows);
        $this->nowZeroFeedInActive = $this->isZeroFeedInActive($dataRows);

        $strStart = date('Y-m-d 00:00:00');
        $strEnd = date('Y-m-d 23:59:59');
        $avg = 86400;  // Sekunden pro Tag
        $this->todayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

        $strStart = date('Y-m-d H:00:00');
        $strEnd = date('Y-m-d H:59:59');
        $avg = 3600;  // Sekunden pro Tag
        $this->currentHourData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

    }

    private function isZeroFeedInActive($dataRows)
    {
        $nowZeroFeedInActive = true;
        foreach ($dataRows as $row) { 
            $nowZeroFeedInActive = $nowZeroFeedInActive && ($row->getEmTotalPower() < 40) && ($row->getEmTotalPower() > -40);
        }
        
        return $nowZeroFeedInActive;
    }

    public function getDataAsJson()
    {
        
        $today = $this->todayData->convertEnergyToJsArray() + $this->todayData->convertAutarkyToJsArray();
        $currentHour = $this->currentHourData->convertEnergyToJsArray() + $this->currentHourData->convertAutarkyToJsArray();
        
        $now = $this->latestRealtimeRow->convertToJsArray();        
        $now["emPercent"] = abs($this->latestRealtimeRow->getEmTotalPower() / 6000 * 100);
        $now["pmPercent"] = ($this->latestRealtimeRow->getPmTotalPower() / 1000) * 100;
        $now["isZeroFeedInActive"] = $this->nowZeroFeedInActive;
        
        $result = [
            "now" => $now,
            "today" => $today,            
            "currenthour" => $currentHour,
        ];        

        return json_encode($result);
    }



}
