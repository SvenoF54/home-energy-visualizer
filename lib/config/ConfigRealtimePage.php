<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigRealtimePage
{
    private $linePosibilities = [-1000, -750, -500, -250, 0, 250, 500, 750, 1000, 1250, 1500, 1750, 2000, 2500, 3000, 3500, 4000, 4500, 5000, 5500, 6000, 6500, 7000, 8000, 9000, 10000, 11000, 12000, 13000, 14000, 15000, 16000, 17000, 18000, 19000, 20000];
    private $line1 = -250;
    private $line2 = 10000;
    private $pastPeriodPossibilities = [0.5, 1, 2, 4, 6, 8, 12, 24, 24*7, 24*14, 24*28];
    private $pastperiod = 1;
    private $averagePossibilitiesInSec = [2, 5, 10, 30, 60, 120, 300, 600, 900, 1800, 3600];
    private $averagePossibility = 5;
    private $refreshIntervalInSec = 10;
    private $configRealtime;

    public function __construct($linePosibilities, $line1, $line2)
    {
        $this->linePosibilities = $linePosibilities;
        $this->line1 = $line1;
        $this->line2 = $line2;
        $this->configRealtime = new ConfigRealtimeViewSettings(true, true, false);
    }

    public function toJson()
    {
        return json_encode([            
            'line1' => $this->line1,
            'line2' => $this->line2,
            'pastperiod' => $this->pastperiod,
            'averagePossibility' => $this->averagePossibility,
            'refreshIntervalInSec' => $this->refreshIntervalInSec,
            'realtime' => $this->configRealtime->toArray()            
        ]);
    }

    public function setFormValues()
    {
        $this->line1 = StringHelper::formGetInt("line1", $this->line1);
        $this->line2 = StringHelper::formGetInt("line2", $this->line2);
        $this->averagePossibility = StringHelper::formGetInt("averagePossibility", $this->averagePossibility);
        $this->pastperiod = StringHelper::formGetInt("pastperiod", $this->pastperiod);

        $this->configRealtime->setFormValues();
    }

    public function getLinePossibilities()
    {
        return $this->linePosibilities;
    }
    
    public function setLinePossibilities($linePosibilities)
    {
        $this->linePosibilities = $linePosibilities;
    }
    
    public function getLine1()
    {
        return $this->line1;
    }
    
    public function setLine1($line1)
    {
        $this->line1 = $line1;
    }
    
    public function getLine2()
    {
        return $this->line2;
    }
    
    public function setLine2($line2)
    {
        $this->line2 = $line2;
    }

    public function getPastPeriodPossibilities()
    {
        return $this->pastPeriodPossibilities;
    }
    
    public function setPastPeriodPossibilities($pastPeriodPossibilities)
    {
        $this->pastPeriodPossibilities = $pastPeriodPossibilities;
    }
    
    public function getPastperiod()
    {
        return $this->pastperiod;
    }
    
    public function setPastperiod($pastperiod)
    {
        $this->pastperiod = $pastperiod;
    }    

    public function getAveragePossibilitiesInSec()
    {
        return $this->averagePossibilitiesInSec;
    }
    
    public function setAveragePossibilitiesInSec($averagePossibilitiesInSec)
    {
        $this->averagePossibilitiesInSec = $averagePossibilitiesInSec;
    }
    
    public function getAveragePossibility() {
        return $this->averagePossibility;
    }

    public function setAveragePossibility($averagePossibility) {
        $this->averagePossibility = $averagePossibility;
    }

    public function getRefreshIntervalInSec()
    {
        return $this->refreshIntervalInSec;
    }
    
    public function setRefreshIntervalInSec($refreshIntervalInSec)
    {
        $this->refreshIntervalInSec = $refreshIntervalInSec;
    }

    public function configRealtime() : ConfigRealtimeViewSettings{
        return $this->configRealtime;
    }
}

class ConfigRealtimeViewSettings {
    private $chartShowEM = false;
    private $chartShowPMTotal = false;
    private $chartShowPM1 = false;
    private $chartShowPM2 = false;    
    private $chartShowPM3 = false;

    public function __construct($showEM, $showPM, $showPMDetails)
    {
        $this->chartShowEM = $showEM;
        $this->chartShowPMTotal = $showPM;
        $this->chartShowPM1 = $showPMDetails;
        $this->chartShowPM2 = $showPMDetails;
        $this->chartShowPM3 = $showPMDetails;
    }

    public function setFormValues()
    {
        $this->chartShowEM = StringHelper::formGetBool("realtime_chartShowEM", $this->chartShowEM);
        $this->chartShowPMTotal = StringHelper::formGetBool("realtime_chartShowPMTotal", $this->chartShowPMTotal);
        $this->chartShowPM1 = StringHelper::formGetBool("realtime_chartShowPM1", $this->chartShowPM1);
        $this->chartShowPM2 = StringHelper::formGetBool("realtime_chartShowPM2", $this->chartShowPM2);
        $this->chartShowPM3 = StringHelper::formGetBool("realtime_chartShowPM3", $this->chartShowPM3);
        
    }

    public function getChartShowEM(): bool {
        return $this->chartShowEM;
    }

    public function setChartShowEM(bool $value): void {
        $this->chartShowEM = $value;
    }

    public function getChartShowPMTotal(): bool {
        return $this->chartShowPMTotal;
    }

    public function setChartShowPMTotal(bool $value): void {
        $this->chartShowPMTotal = $value;
    }

    public function getChartShowPM1(): bool {
        return $this->chartShowPM1;
    }

    public function setChartShowPM1(bool $value): void {
        $this->chartShowPM1 = $value;
    }

    public function getChartShowPM2(): bool {
        return $this->chartShowPM2;
    }

    public function setChartShowPM2(bool $value): void {
        $this->chartShowPM2 = $value;
    }

    public function getChartShowPM3(): bool {
        return $this->chartShowPM3;
    }

    public function setChartShowPM3(bool $value): void {
        $this->chartShowPM3 = $value;
    }

    public function toArray(): array {
        return [
            'chartShowEM' => $this->chartShowEM,
            'chartShowPMTotal' => $this->chartShowPMTotal,
            'chartShowPM1' => $this->chartShowPM1,
            'chartShowPM2' => $this->chartShowPM2,
            'chartShowPM3' => $this->chartShowPM3,
        ];
    }    
}
