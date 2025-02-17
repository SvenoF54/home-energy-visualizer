<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class CustomEnergyValueSet
{
    private $timestampFrom;
    private $timestampTo;
    private $outCentPricePerWh;
    private $inCentPricePerWh;
    private $emTotalPower;
    private $emOverZero;
    private $emUnderZero;
    private $pm1TotalPower;
    private $pm2TotalPower;
    private $pm3TotalPower;

    public function __construct(
        $timestampFrom = null,
        $timestampTo = null,
        $outCentPricePerWh = null,
        $inCentPricePerWh = null,
    ) {
        $this->timestampFrom = $timestampFrom;
        $this->timestampTo = $timestampTo;
        $this->outCentPricePerWh = $outCentPricePerWh;
        $this->inCentPricePerWh = $inCentPricePerWh;
    }

    // Getter
    public function getTimestampFrom()
    {
        return $this->timestampFrom;
    }

    public function getTimestampTo()
    {
        return $this->timestampTo;
    }

    public function getTimestampType()
    {
        $diffDays = TimeHelper::calculateDifferenceInDays($this->timestampFrom, $this->timestampTo);        
        return $diffDays <= 1 ? "date" : "month";
    }

    public function getOutCentPricePerWh()
    {
        return $this->outCentPricePerWh;
    }

    public function getInCentPricePerWh()
    {
        return $this->inCentPricePerWh;
    }

    public function getEmTotalPower()
    {
        return $this->emTotalPower;
    }

    public function getEmOverZero()
    {
        return $this->emOverZero;
    }

    public function getEmUnderZero()
    {
        return $this->emUnderZero;
    }

    public function getPm1TotalPower()
    {
        return $this->pm1TotalPower;
    }

    public function getPm2TotalPower()
    {
        return $this->pm2TotalPower;
    }

    public function getPm3TotalPower()
    {
        return $this->pm3TotalPower;
    }

    // Setter
    public function setTimestampFrom($timestampFrom)
    {
        $this->timestampFrom = $timestampFrom;
    }

    public function setTimestampTo($timestampTo)
    {
        $this->timestampTo = $timestampTo;
    }

    public function setOutCentPricePerWh($outCentPricePerWh)
    {
        $this->outCentPricePerWh = $outCentPricePerWh;
    }

    public function setInCentPricePerWh($inCentPricePerWh)
    {
        $this->inCentPricePerWh = $inCentPricePerWh;
    }

    public function setEmTotalPower($emTotalPower) {
        $this->emTotalPower = $emTotalPower;
    }

    public function setEmOverZero($emOverZero) {
        $this->emOverZero = $emOverZero;
    }

    public function setEmUnderZero($emUnderZero) {
        $this->emUnderZero = $emUnderZero;
    }

    public function setPm1TotalPower($pm1TotalPower) {
        $this->pm1TotalPower = $pm1TotalPower;
    }

    public function setPm2TotalPower($pm2TotalPower) {
        $this->pm2TotalPower = $pm2TotalPower;
    }

    public function setPm3TotalPower($pm3TotalPower) {
        $this->pm3TotalPower = $pm3TotalPower;
    }

    public function setEmPower($emTotalPower, $emUnderZero) {
        $this->emTotalPower = $emTotalPower;
        $this->emOverZero = $emTotalPower > 0 ? $emTotalPower : 0;
        $this->emUnderZero = $emUnderZero;
    }

    public function setPmPower($pmTotal, $divideForPhase1, $divideForPhase2, $divideForPhase3)
    {
        $divide = $divideForPhase1 ? 1 : 0;
        $divide += $divideForPhase2 ? 1 : 0;
        $divide += $divideForPhase3 ? 1 : 0;
        $this->pm1TotalPower = $divideForPhase1 ? $pmTotal / $divide : null;
        $this->pm2TotalPower = $divideForPhase2 ? $pmTotal / $divide : null;
        $this->pm3TotalPower = $divideForPhase3 ? $pmTotal / $divide : null;
    }
}

?>
