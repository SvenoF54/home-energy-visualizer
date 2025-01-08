<?php

class ConfigOverviewPages
{
    private $linePosibilities = [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000];
    private $line1Default = -250;
    private $line2Default = 10000;
    private $chartOrTableOnFirstPageView = ChartOrTableOnFirstPageViewEnum::EnergyChart;
    private $showProductionInTotal = false;
    private $showSelection2OnEnergyChart = false;
    private $showSelection2OnAutarkyChart = false;
    private $pageLengthEnergyTable = 10;

    public function __construct($linePosibilities, $line1Default, $line2Default, $chartOrTableOnFirstPageView, $showProductionInTotal = false)
    {
        $this->linePosibilities = $linePosibilities;
        $this->line1Default = $line1Default;
        $this->line2Default = $line2Default;
        $this->chartOrTableOnFirstPageView = $chartOrTableOnFirstPageView;
        $this->showProductionInTotal = $showProductionInTotal;
    }

    public function toJson()
    {
        return json_encode([
            //'linePosibilities' => $this->linePosibilities,
            'line1Default' => $this->line1Default,
            'line2Default' => $this->line2Default,
            'chartOrTableOnFirstPageView' => $this->chartOrTableOnFirstPageView,
            'showProductionInTotal' => $this->showProductionInTotal,
            'showSelection2OnEnergyChart' => $this->showSelection2OnEnergyChart,
            'showSelection2OnAutarkyChart' => $this->showSelection2OnAutarkyChart,
            'pageLengthEnergyTable' => $this->pageLengthEnergyTable,
        ]);
    }

    public function getLinePossibilities()
    {
        return $this->linePosibilities;
    }
    
    public function setLinePossibilities($linePosibilities)
    {
        $this->linePosibilities = $linePosibilities;
    }
    
    public function getLine1Default()
    {
        return $this->line1Default;
    }
    
    public function setLine1Default($line1Default)
    {
        $this->line1Default = $line1Default;
    }
    
    public function getLine2Default()
    {
        return $this->line2Default;
    }
    
    public function setLine2Default($line2Default)
    {
        $this->line2Default = $line2Default;
    }
    
    public function getChartOrTableOnFirstPageView()
    {
        return $this->chartOrTableOnFirstPageView;
    }
    
    public function setChartOrTableOnFirstPageView($chartOrTableOnFirstPageView)
    {
        $this->chartOrTableOnFirstPageView = $chartOrTableOnFirstPageView;
    }
    
    public function getShowProductionInTotal()
    {
        return $this->showProductionInTotal;
    }
    
    public function setShowProductionInTotal($showProductionInTotal)
    {
        $this->showProductionInTotal = $showProductionInTotal;
    }
    
    public function getShowSelection2InEnergyChart()
    {
        return $this->showSelection2OnEnergyChart;
    }
    
    public function setShowSelection2InEnergyChart($showSelection2OnEnergyChart)
    {
        $this->showSelection2OnEnergyChart = $showSelection2OnEnergyChart;
    }    

    public function getShowSelection2OnAutarkyChart()
    {
        return $this->showSelection2OnAutarkyChart;
    }
    
    public function setShowSelection2OnAutarkyChart($showSelection2OnAutarkyChart)
    {
        $this->showSelection2OnAutarkyChart = $showSelection2OnAutarkyChart;
    }    

    public function getPageLengthEnergyTable()
    {
        return $this->pageLengthEnergyTable;
    }

    public function setPageLengthEnergyTable($pageLengthEnergyTable)
    {
        $this->pageLengthEnergyTable = $pageLengthEnergyTable;
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
    
    public function setLastHoursPossibilities($lastHoursPossibilities)
    {
        $this->lastHoursPossibilities = $lastHoursPossibilities;
    }
    
    public function getAveragePossibilitiesInSec()
    {
        return $this->averagePossibilitiesInSec;
    }
    
    public function setAveragePossibilitiesInSec($averagePossibilitiesInSec)
    {
        $this->averagePossibilitiesInSec = $averagePossibilitiesInSec;
    }
    
    public function getRefreshIntervalInSec()
    {
        return $this->refreshIntervalInSec;
    }
    
    public function setRefreshIntervalInSec($refreshIntervalInSec)
    {
        $this->refreshIntervalInSec = $refreshIntervalInSec;
    }
    

}
