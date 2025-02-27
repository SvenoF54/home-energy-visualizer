<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class RealtimeService
{
    private ConfigDashboardPage $dashboardConfig;
    private ConfigRealtimeAlert $realtimeAlertConfig;
    private $realTimeEnergyDataTbl;
    private $latestDataRows;

    public function __construct()
    {
        $this->realTimeEnergyDataTbl = RealTimeEnergyDataTable::getInstance();
        $this->dashboardConfig = Configuration::getInstance()->configDashboardPage();
        $this->realtimeAlertConfig = Configuration::getInstance()->configRealtimeAlert();
    }

    public function readLatestData() {
        $startTime = date('Y-m-d H:i:s', strtotime("- 65 seconds"));
        $endTime = date('Y-m-d H:i:s', strtotime("- 5 seconds"));
        #$startTime = date('Y-m-d H:i:s', strtotime("- 5 hours -60 seconds"));  // For testing 
        #$endTime = date('Y-m-d H:i:s', strtotime("- 5 hours"));
        $this->latestDataRows = $this->realTimeEnergyDataTbl->getOverviewData($startTime, $endTime, 5);                
    }

    public function isZeroFeedInActive()
    {
        $countZeroFeedIn = 0;
        foreach ($this->latestDataRows as $row) { 
            if (($row->getEmTotalPower() < $this->dashboardConfig->getZeroFeedInRange()) 
                && ($row->getEmTotalPower() > -$this->dashboardConfig->getZeroFeedInRange())) {
                    $countZeroFeedIn++;
            }
        }
        
        $percentZeroFeedInDuringTimeRange = $countZeroFeedIn / count($this->latestDataRows);
        return $percentZeroFeedInDuringTimeRange > 0.9;
    }

    public function getLatestDataRow() : RealTimeEnergyDataRow
    {
        return end($this->latestDataRows);
    }

}
