<?php
setlocale(LC_TIME, 'de_DE.UTF-8', 'de_DE', 'deu_deu');

include_once("local-config.php");  // Eine local.config.php Datei erstellen, s. local-config.php.sample
define('TITLE', 'PV-Datenvisualisierung');
define('MONTH_LIST', [
    1 => "Januar", 2 => "Februar", 3 => "März", 4 => "April", 5 => "Mai", 6 => "Juni",
    7 => "Juli", 8 => "August", 9 => "September", 10 => "Oktober", 11 => "November", 12 => "Dezember"
]);

class Config
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
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->configCustomEnergyValuesPage = new ConfigCustomEnergyValuesPage();
        $this->configCustomPricesPage = new ConfigCustomPricesPage();
        $this->overviewPages["realtime"] = new ConfigRealtimeOverviewPages(
            [100, 150, 175, 200, 250, 300, 400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500, 2000, 3000, 4000, 5000],
            200, 
            800,
            true            
        );

        $this->overviewPages["hours"] = new ConfigOverviewPages(
            [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000]
            , -250, 
            2000,
            true        
        );
        $this->overviewPages["days"] = new ConfigOverviewPages(
            [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000]
            , -250, 
            10000,
            true
        );
        $this->overviewPages["months"] = new ConfigOverviewPages(
            [-4000, -3000, -2000, -1000, 0, 5000, 10000, 20000, 30000, 40000, 50000, 60000, 70000, 80000, 90000, 100000]
            , -2000, 
            20000,
            true
        );
        $this->overviewPages["years"] = new ConfigOverviewPages(
            [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000]
            , -250, 
            10000,
            false
        );
    }    

    public function getOutCentPricePerWh() { return $this->outCentPricePerWh;}
    public function getInCentPricePerWh() { return $this->inCentPricePerWh;}
    public function realtimeOverview() : ConfigRealtimeOverviewPages { return $this->overviewPages["realtime"];}
    public function hoursOverview() : ConfigOverviewPages{ return $this->overviewPages["hours"];}
    public function daysOverview() : ConfigOverviewPages{ return $this->overviewPages["days"];}
    public function monthsOverview() : ConfigOverviewPages { return $this->overviewPages["months"];}
    public function yearsOverview() : ConfigOverviewPages { return $this->overviewPages["years"];}
    public function customEnergyValuesPage() : ConfigCustomEnergyValuesPage { return $this->configCustomEnergyValuesPage; }
    public function customPricesPage() : ConfigCustomPricesPage { return $this->configCustomPricesPage; } 

}

class ConfigOverviewPages
{
    private $linePosibilities = [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000];
    private $line1Default = -250;
    private $line2Default = 10000;
    private $showDiagramOnFirstPageView = true;

    public function __construct($linePosibilities, $line1Default, $line2Default, $showDiagramOnFirstPageView)
    {
        $this->linePosibilities = $linePosibilities;
        $this->line1Default = $line1Default;
        $this->line2Default = $line2Default;
        $this->showDiagramOnFirstPageView = $showDiagramOnFirstPageView;
    }

    public function getLinePossibilities()
    {
        return $this->linePosibilities;
    }

    public function getLine1Default()
    {
        return $this->line1Default;
    }

    public function getLine2Default()
    {
        return $this->line2Default;
    }

    public function getShowDiagramOnFirstPageView()
    {
        return $this->showDiagramOnFirstPageView;
    }
    
}

class ConfigRealtimeOverviewPages extends ConfigOverviewPages
{
    private $lastHoursPossibilities = [0.5, 1, 2, 4, 6, 8, 12, 24, 24*7, 24*14, 24*28];
    private $averagePossibilitiesInSec = [2, 5, 10, 30, 60, 120, 300, 600, 900, 1800, 3600];
    private $refreshIntervalInSec = 10;
    
    public function getLastHoursPossibilities()
    {
        return $this->lastHoursPossibilities;
    }

    public function getAveragePossibilitiesInSec()
    {
        return $this->averagePossibilitiesInSec;
    }

    public function getRefreshIntervalInSec()
    {
        return $this->refreshIntervalInSec;
    }

}

class ConfigCustomEnergyValuesPage {
    private $defaultMonthOrYear = "month";

    public function getDefaultMonthOrYear()
    {
        return $this->defaultMonthOrYear;
    }
}

class ConfigCustomPricesPage {

}
