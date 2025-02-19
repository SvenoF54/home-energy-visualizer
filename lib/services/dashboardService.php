<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class DashboardService
{
    private $realTimeEnergyDataTbl;
    private $latestRealtimeRow;

    public function __construct()
    {
        $this->realTimeEnergyDataTbl = RealTimeEnergyDataTable::getInstance();
    }

    public function prepareData()
    {
        $startTime = date('Y-m-d H:i:s', strtotime("- 65 seconds"));
        $endTime = date('Y-m-d H:i:s', strtotime("- 5 seconds"));
        $dataRows = $this->realTimeEnergyDataTbl->getOverviewData($startTime, $endTime, 5);

        $this->latestRealtimeRow = end($dataRows);
    }

    public function getDataAsJson()
    {
        return json_encode([
            'em_total' => $this->latestRealtimeRow->getEmTotalPower(),
            'em_over_zero' => $this->latestRealtimeRow->getEmTotalPowerOverZero(),
            'em_under_zero' => $this->latestRealtimeRow->getEmTotalPowerUnderZero(),
            'pm_total' => $this->latestRealtimeRow->getPmTotalPower(),
            'pm1_total' => $this->latestRealtimeRow->getPm1TotalPower(),
            'pm2_total' => $this->latestRealtimeRow->getPm2TotalPower(),
            'pm3_total' => $this->latestRealtimeRow->getPm3TotalPower(),
        ]);
    }



}
