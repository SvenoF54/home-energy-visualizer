<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigOverviewPages
{
    private $linePosibilities = [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000];
    private $line1 = -250;
    private $line2 = 10000;
    private $chartOrTableView = ChartOrTableViewEnum::EnergyChart;
    private $configEnergy1;
    private $configEnergy2;

    public function __construct($linePosibilities, $line1, $line2, $chartOrTableView)
    {
        $this->linePosibilities = $linePosibilities;
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->chartOrTableView = $chartOrTableView;
        $this->configEnergy1 = new ConfigEnergyViewSettings(1, true);
        $this->configEnergy2 = new ConfigEnergyViewSettings(2, false);
    }

    public function toJson()
    {
        return json_encode([
            //'linePosibilities' => $this->linePosibilities,
            'line1' => $this->line1,
            'line2' => $this->line2,
            'chartOrTableView' => $this->chartOrTableView->value,
            'energy1' => $this->configEnergy1->toArray(),
            'energy2' => $this->configEnergy2->toArray(),            
        ]);
    }

    public function setFormValues()
    {
        $this->line1 = StringHelper::formGetInt("line1", $this->line1);
        $this->line2 = StringHelper::formGetInt("line2", $this->line2);
        $tmpChartOrTable = StringHelper::formGetString("chartOrTableView", $this->getChartOrTableView()->value);
        $this->chartOrTableView = ChartOrTableViewEnum::tryFrom($tmpChartOrTable);                

        $this->configEnergy1->setFormValues();
        $this->configEnergy2->setFormValues();
    }

    public function getLinePossibilities() { return $this->linePosibilities; }    
    public function setLinePossibilities($linePosibilities) { $this->linePosibilities = $linePosibilities; }
    
    public function getLine1() { return $this->line1; }
    public function setLine1($line1) { $this->line1 = $line1; }
    
    public function getLine2() { return $this->line2; }    
    public function setLine2($line2) { $this->line2 = $line2; }
    
    public function getChartOrTableView() { return $this->chartOrTableView; }
    public function getChartOrTableViewAsString() { return $this->chartOrTableView->value; }

    public function showEnergyChartView() { return ChartOrTableViewEnum::isEnergyChart($this->chartOrTableView); }
    public function showAutarkyChartView() { return ChartOrTableViewEnum::isAutarkyChart($this->chartOrTableView); }

    public function showEnergyTableView() { return ChartOrTableViewEnum::isEnergyTable($this->chartOrTableView); }
    public function setChartOrTableView($chartOrTableView) { $this->chartOrTableView = $chartOrTableView; }
    
    public function configEnergy1() : ConfigEnergyViewSettings{ return $this->configEnergy1; }
    public function configEnergy2() : ConfigEnergyViewSettings { return $this->configEnergy2; }

}

class ConfigEnergyViewSettings {
    private $chartNumber;
    private $chartShowEnergyOverZero = false;
    private $chartShowEnergyOverZeroPlusSavings = false;
    private $chartShowFeedIn = false;
    private $chartShowSavings = false;    
    private $chartShowAutarkyRate = false;
    private $chartShowSelfConsumptionRate = false;
    private $tableShowProductionInTotal = true;
    private $tablePageLength = 10;

    public function __construct($chartNumber, $showMetrics)
    {
        $this->chartNumber = $chartNumber;
        $this->chartShowEnergyOverZero = $showMetrics;
        $this->chartShowEnergyOverZeroPlusSavings = $showMetrics;
        $this->chartShowFeedIn = $showMetrics;
        $this->chartShowSavings = $showMetrics;
        $this->chartShowAutarkyRate = $showMetrics;
    }

    public function setFormValues()
    {
        $this->chartShowEnergyOverZero = StringHelper::formGetBool("energy".$this->chartNumber."_chartShowEnergyOverZero", $this->chartShowEnergyOverZero);
        $this->chartShowEnergyOverZeroPlusSavings = StringHelper::formGetBool("energy".$this->chartNumber."_chartShowEnergyOverZeroPlusSavings", $this->chartShowEnergyOverZeroPlusSavings);
        $this->chartShowFeedIn = StringHelper::formGetBool("energy".$this->chartNumber."_chartShowFeedIn", $this->chartShowFeedIn);
        $this->chartShowSavings = StringHelper::formGetBool("energy".$this->chartNumber."_chartShowSavings", $this->chartShowSavings);
        $this->chartShowAutarkyRate = StringHelper::formGetBool("energy".$this->chartNumber."_chartShowAutarky", $this->chartShowAutarkyRate);
        $this->chartShowSelfConsumptionRate = StringHelper::formGetBool("energy".$this->chartNumber."_chartShowSelfConsumptionRate", $this->chartShowSelfConsumptionRate);
        $this->tableShowProductionInTotal = StringHelper::formGetBool("energy".$this->chartNumber."_tableShowProductionInTotal", $this->tableShowProductionInTotal);
        $this->tablePageLength = StringHelper::formGetInt("energy".$this->chartNumber."_tablePageLength", $this->tablePageLength);
        
    }

    public function getChartShowEnergyOverZero() { return $this->chartShowEnergyOverZero; }
    public function setChartShowEnergyOverZero($chartShowEnergyOverZero) { $this->chartShowEnergyOverZero = $chartShowEnergyOverZero; }
    public function getChartShowEnergyOverZeroPlusSavings() { return $this->chartShowEnergyOverZeroPlusSavings; }
    public function setChartShowEnergyOverZeroPlusSavings($chartShowEnergyOverZeroPlusSavings) { $this->chartShowEnergyOverZeroPlusSavings = $chartShowEnergyOverZeroPlusSavings; }

    public function getChartShowFeedIn() { return $this->chartShowFeedIn; }
    public function setChartShowFeedIn($chartShowFeedIn) { $this->chartShowFeedIn = $chartShowFeedIn; }

    public function getChartShowSavings() { return $this->chartShowSavings; }
    public function setChartShowSavings($chartShowSavings) { $this->chartShowSavings = $chartShowSavings; }

    public function getChartShowAutarkyRate() { return $this->chartShowAutarkyRate; } 
    public function getChartShowSelfConsumptionRate() { return $this->chartShowSelfConsumptionRate; }    
    public function setChartShowAutarkyAndSelfConsumptionRate($chartShowAutarkyRate, $chartShowSelfConsumptionRate) { 
        $this->chartShowAutarkyRate = $chartShowAutarkyRate; 
        $this->chartShowSelfConsumptionRate = $chartShowSelfConsumptionRate;
    }    

    public function getTableShowProductionInTotal() { return $this->tableShowProductionInTotal; }
    public function setTableShowProductionInTotal($tableShowProductionInTotal) { $this->tableShowProductionInTotal = $tableShowProductionInTotal; }

    public function getTablePageLength() { return $this->tablePageLength; }
    public function setTablePageLength($tablePageLength) { $this->tablePageLength = $tablePageLength; }    
    

    public function toArray() {
        return [
            'chartShowEnergyOverZero' => $this->chartShowEnergyOverZero,
            'chartShowEnergyOverZeroPlusSavings' => $this->chartShowEnergyOverZeroPlusSavings,
            'chartShowFeedIn' => $this->chartShowFeedIn,
            'chartShowSavings' => $this->chartShowSavings,
            'chartShowAutarkyRate' => $this->chartShowAutarkyRate,
            'chartShowSelfConsumptionRate' => $this->chartShowSelfConsumptionRate,
            'tableShowProductionInTotal' => $this->tableShowProductionInTotal,
            'tablePageLength' => $this->tablePageLength,
        ];
    }
}
