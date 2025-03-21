<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class RealTimeEnergyDataRow
{
    private $timestampData;
    private $intervalInSeconds;
    private $emTotalPower;
    private $emTotalPowerOverZero;
    private $emTotalPowerUnderZero;
    private $pm1TotalPower;
    private $pm2TotalPower;
    private $pm3TotalPower;
    private $emMissingRows = null;
    private $pm1MissingRows = null;
    private $pm2MissingRows = null;
    private $pm3MissingRows = null;
    private $countRows = null;

    public function __construct(
        $timestampData = null, 
        $intervalInSeconds = 2, 
        $emTotalPower = 0, 
        $emTotalPowerOverZero = 0,
        $emTotalPowerUnderZero = 0,
        $pm1TotalPower = null, 
        $pm2TotalPower = null, 
        $pm3TotalPower = null,        
        $emMissingRows = null, 
        $pm1MissingRows = null, 
        $pm2MissingRows = null, 
        $pm3MissingRows = null,
        $countRows = null
    ) {
        $this->timestampData = ($timestampData != null) ? $timestampData : date("Y-m-d H:i:s");
        $this->intervalInSeconds = $intervalInSeconds;
        $this->emTotalPower = $emTotalPower;
        $this->emTotalPowerOverZero = $emTotalPowerOverZero;
        $this->emTotalPowerUnderZero = $emTotalPowerUnderZero;
        
        $this->pm1TotalPower = $pm1TotalPower;
        $this->pm2TotalPower = $pm2TotalPower;
        $this->pm3TotalPower = $pm3TotalPower;

        $this->emMissingRows = $emMissingRows;
        $this->pm1MissingRows = $pm1MissingRows;
        $this->pm2MissingRows = $pm2MissingRows;
        $this->pm3MissingRows = $pm3MissingRows;

        $this->countRows = $countRows;
    }

    public static function createFromRow(array $row): self
    {
        return new self(
            $row["timestamp"], 
            $row["interval_in_seconds"],
            $row["em_total_power"],
            $row["em_total_power_over_zero"],
            $row["em_total_power_under_zero"],
            $row["pm1_total_power"],
            $row["pm2_total_power"],
            $row["pm3_total_power"],
            $row["em_missing_rows"],
            $row["pm1_missing_rows"],
            $row["pm2_missing_rows"],
            $row["pm3_missing_rows"],
            $row["count_rows"]
        );
    }

    public function convertToJsArray() : array
    {
        $autarky = OverviewPageService::calculateAutarky($this->getSavingsPower(), $this->getEmTotalPowerOverZero());
        $selfConsumptionRate = OverviewPageService::calculateAutarky($this->getSavingsPower(), $this->getEmTotalPowerOverZero());        
        return [
            'em' => $this->getEmTotalPower(),
            'emOZ' => $this->getEmTotalPowerOverZero(),
            'emUZ' => $this->getEmTotalPowerUnderZero(),
            'slfCon' => $this->getSelfConsumptionPower(),
            'pm' => $this->getPmTotalPower(),
            'pm1' => $this->getPm1TotalPower(),
            'pm2' => $this->getPm2TotalPower(),
            'pm3' => $this->getPm3TotalPower(),
            'pmSvg' => $this->getSavingsPower(),
            'autInPct' => $autarky,
            'slfConInPct' => $selfConsumptionRate
        ];
    }

    public function getTimestampData() { return $this->timestampData; }
    public function getIntervalInSeconds() { return $this->intervalInSeconds; }

    public function getEmTotalPower() { return $this->emTotalPower; }
    public function getEmTotalPowerOverZero() { return $this->emTotalPowerOverZero; }    
    public function getEmTotalPowerUnderZero() { return $this->emTotalPowerUnderZero; }    
    public function getSelfConsumptionPower() { return $this->emTotalPowerOverZero + $this->getSavingsPower(); }    

    public function getPmTotalPower() { return $this->pm1TotalPower + $this->pm2TotalPower + $this->pm3TotalPower; }
    public function getPm1TotalPower() { return $this->pm1TotalPower; }
    public function getPm2TotalPower() { return $this->pm2TotalPower;}
    public function getPm3TotalPower() { return $this->pm3TotalPower; }        
    public function getSavingsPower() { return $this->getPmTotalPower() + $this->getEmTotalPowerUnderZero(); }
    public function getPmXTotalPower($pmNumber) { 
        if (strtoLower($pmNumber) == "pm1") return $this->pm1TotalPower; 
        if (strtoLower($pmNumber) == "pm2") return $this->pm2TotalPower; 
        if (strtoLower($pmNumber) == "pm3") return $this->pm3TotalPower; 
        return 0;
    }    

    public function getEmMissingRows() {return $this->emMissingRows; }
    public function getPm1MissingRows() { return $this->pm1MissingRows; }
    public function getPm2MissingRows() { return $this->pm2MissingRows; }
    public function getPm3MissingRows() { return $this->pm3MissingRows; }    

    public function getCountRows() { return $this->countRows; }    

}
