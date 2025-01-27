<?php
include_once("ChartOrTableViewEnum.php");
include_once("ConfigOptionPages.php");
include_once("ConfigOverviewPages.php");
include_once("ConfigRealtimePage.php");


class Configuration
{
    private $overviewPages = [];
    private $configCustomEnergyValuesPage;
    private $configCustomPricesPage;
    private $outCentPricePerWh = 0.03334;   // Preis pro Watt 	33,34 ct/kWh = 33,34/1000 = 0.03334
    private $inCentPricePerWh = 0.082;       // Preis pro Watt 	
    private static $instance;
    
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Configuration();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->configCustomEnergyValuesPage = new ConfigCustomEnergyValuesPage();
        $this->configCustomPricesPage = new ConfigCustomPricesPage();
        $this->overviewPages["realtime"] = new ConfigRealtimePage(
            [100, 150, 175, 200, 250, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 3000, 4000, 5000],
            200, 
            800
        );

        $this->overviewPages["hours"] = new ConfigOverviewPages(
            [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000]
            , -250, 
            2000,
            ChartOrTableViewEnum::EnergyChart,
            true
        );
        $this->overviewPages["days"] = new ConfigOverviewPages(
            [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000]
            , -250, 
            10000,
            ChartOrTableViewEnum::EnergyChart,
            true
        );
        $this->overviewPages["months"] = new ConfigOverviewPages(
            [-15000, -10000, -5000, -2500, 0, 5000, 10000, 20000, 30000, 40000, 50000, 75000, 100000, 150000, 200000, 250000, 300000]
            ,-5000, 
            100000,
            ChartOrTableViewEnum::EnergyChart,
            true
        );
        $this->overviewPages["years"] = new ConfigOverviewPages(
            [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000]
            , -250, 
            10000,
            ChartOrTableViewEnum::EnergyTable,
            true
        );
    }    

    public function getOutCentPricePerWh() { return $this->outCentPricePerWh;}
    public function getInCentPricePerWh() { return $this->inCentPricePerWh;}
    public function realtimeOverview() : ConfigRealtimePage { return $this->overviewPages["realtime"];}
    public function hoursOverview() : ConfigOverviewPages{ return $this->overviewPages["hours"];}
    public function daysOverview() : ConfigOverviewPages{ return $this->overviewPages["days"];}
    public function monthsOverview() : ConfigOverviewPages { return $this->overviewPages["months"];}
    public function yearsOverview() : ConfigOverviewPages { return $this->overviewPages["years"];}
    public function customEnergyValuesPage() : ConfigCustomEnergyValuesPage { return $this->configCustomEnergyValuesPage; }
    public function customPricesPage() : ConfigCustomPricesPage { return $this->configCustomPricesPage; } 

}
