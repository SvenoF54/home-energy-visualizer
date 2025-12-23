<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class DashboardService
{
    private $realTimeService;
    private $hourlyEnergyDataTbl;
    private ConfigDashboardPage $config;
    private EnergyDataSet $todayData;
    private EnergyDataSet $yesterdayData;
    private RealTimeEnergyDataRow $realTimeData;
    private $zendureData;    
    private $shellyData;

    public function __construct()
    {
        $this->realTimeService = new RealtimeService();
        $this->hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
        $this->config = Configuration::getInstance()->dashboardPage();
    }

    public function prepareInstantData()
    {
        // Realtime data
        $this->realTimeService->readLatestData();
        $this->realTimeData = $this->realTimeService->getLatestDataRow();

        // Today data
        $strStart = date('Y-m-d 00:00:00');
        $strEnd = date('Y-m-d 23:59:59');
        $avg = 86400;  // Sekunden pro Tag
        $this->todayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);

        // Yesterday Data
        $strStart = date('Y-m-d 00:00:00', strtotime("- 1 day"));
        $strEnd = date('Y-m-d 23:59:59', strtotime("- 1 day"));
        $avg = 86400;  // Sekunden pro Tag
        $this->yesterdayData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);        
        
        // Optional PlugIn Data
        if ($this->config->getShowZendureOnDashboard()) {
            // Prepare Zendure Dashboard data
            $zendureConfig = Configuration::getInstance()->zendure();
            $zendureService = new ZendureService();
            $pmxPower = $this->realTimeData->getPmXTotalPower($zendureConfig->getConnectedToPmPort());
            $this->zendureData = $zendureService->prepareDashboardData($pmxPower);
        }
        //if ($this->config->getShowXY())
        $shellyService = new ShellyDeviceService();
        $pmxPower = $this->realTimeData->getPmXTotalPower("PM1");
        $this->shellyData = $shellyService->prepareDashboardData($pmxPower);
    }

    public function prepareStaticData()
    {
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
        $yesterday = $this->yesterdayData->convertEnergyToJsArray() + $this->yesterdayData->convertAutarkyToJsArray();
        
        $latestRealTimeRow = $this->realTimeService->getLatestDataRow();
        $totalProduction = $latestRealTimeRow->getPmTotalPower() 
                           + (isset($this->zendureData["productionUsedInternal"]) ? $this->zendureData["productionUsedInternal"] : 0);
        $now = $latestRealTimeRow->convertToJsArray();     
        $now["emPercent"] = abs($latestRealTimeRow->getEmTotalPower() / $this->config->getConsumptionIndicatedAs100Percent() * 100);
        $now["pmPercent"] = ($latestRealTimeRow->getPmTotalPower() / $this->config->getMaxEnergyProduction()) * 100;
        $now["production"] = $totalProduction;
        $now["productionPercent"] = ($now["production"] / 2000) * 100;
        $now["isZeroFeedInActive"] = $this->realTimeService->isZeroFeedInActive();
        
        $result = [
            "now" => $now,
            "today" => $today,         
            "yesterday" => $yesterday,   
            "zendurePack" => $this->zendureData,
            "shellyPack" => $this->shellyData
        ];        

        return json_encode($result);
    }

    public function getYesterdayData() : EnergyDataSet { return $this->yesterdayData; }
    public function getTodayData() : EnergyDataSet { return $this->todayData; }
}
