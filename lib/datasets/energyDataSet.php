<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class EnergyDataSet {
    private $timestampForView;
    private $timestampFrom;
    private $timestampTo;
    private EnergyAndPriceTuple $energy;
    private EnergyAndPriceTuple $energyOverZero;
    private EnergyAndPriceTuple $energyUnderZero;
    private EnergyAndPriceTuple $energyUnderX1;
    private EnergyAndPriceTuple $energyOverX1;
    private EnergyAndPriceTuple $energyUnderX2;
    private EnergyAndPriceTuple $energyOverX2;
    private EnergyAndPriceTuple $productionPm1;
    private EnergyAndPriceTuple $productionPm2;
    private EnergyAndPriceTuple $productionPm3;
    private EnergyAndPriceTuple $savings;
    private MissingRowSet $missingRows;  
    private $countOriginRows;

    public function __construct($timestampFrom, $timestampTo)
    {
        $this->timestampFrom = $timestampFrom;
        $this->timestampTo = $timestampTo;
        
        $this->energy = new EnergyAndPriceTuple();
        $this->energyUnderZero = new EnergyAndPriceTuple();
        $this->savings = new EnergyAndPriceTuple();
        $this->missingRows = new MissingRowSet();          
    }

    public function convertEnergyToJsArray() : array
    {
        $dataRow = [
            "raw-dt" => $this->timestampFrom,
            "x-dt" => $this->timestampForView,
            "emOZ" => $this->getEnergyOverZero()->getEnergyInWatt(),
            "emOZPrice" => $this->getEnergyOverZero()->getEnergyPriceInCent(),
            "emUZ" => $this->getEnergyUnderZero()->getEnergyInWatt(),
            "emUZPrice" => $this->getEnergyUnderZero()->getEnergyPriceInCent(),
            "pm1" => $this->getProductionPm1()->getEnergyInWatt(),
            "pm1Price" => $this->getProductionPm1()->getEnergyPriceInCent(),
            "pm2" => $this->getProductionPm2()->getEnergyInWatt(),
            "pm2Price" => $this->getProductionPm2()->getEnergyPriceInCent(),
            "pm3" => $this->getProductionPm3()->getEnergyInWatt(),
            "pm3Price" => $this->getProductionPm3()->getEnergyPriceInCent(),
            "pmSvg" => $this->getSavings()->getEnergyInWatt(),
            "pmSvgPrice" => $this->getSavings()->getEnergyPriceInCent()
        ];

        return $dataRow;
    }

    public function convertAutarkyToJsArray() : array
    {        
        $dataRow = [
            "raw-dt" => $this->timestampFrom,
            "x-dt" => $this->timestampForView,
            "autInPct" => $this->getAutarkyInPercent(),
            "slfConInPct" => $this->getSelfConsumptionInPercent(),
            "pmSvg" => $this->getSavings()->getEnergyInWatt(),
        ];

        return $dataRow;
    }

    // Getter methods
    public function getTimestampForView() { return $this->timestampForView; }
    public function getTimestampFrom() { return $this->timestampFrom; }
    public function getTimestampTo() { return $this->timestampTo; }

    public function getEnergy() : EnergyAndPriceTuple { return $this->energy; }
    public function getEnergyOverZero(): EnergyAndPriceTuple { return $this->energyOverZero; }
    public function getEnergyUnderZero() : EnergyAndPriceTuple { return $this->energyUnderZero; }
    public function getEnergyUnderX1() : EnergyAndPriceTuple { return $this->energyUnderX1; }
    public function getEnergyOverX1() : EnergyAndPriceTuple { return $this->energyOverX1; }
    public function getEnergyUnderX2() : EnergyAndPriceTuple { return $this->energyUnderX2; }
    public function getEnergyOverX2() : EnergyAndPriceTuple { return $this->energyOverX2; }

    public function getEnergyBetweenX1AndX2() : EnergyAndPriceTuple {
        $betweenX1AndX2 =  $this->getEnergyOverZero()->getEnergyInWatt() - $this->getEnergyUnderX1()->getEnergyInWatt() - $this->getEnergyOverX2()->getEnergyInWatt(); 
        $betweenX1AndX2Price = $this->getEnergyOverZero()->getEnergyPriceInCent() - $this->getEnergyUnderX1()->getEnergyPriceInCent() - $this->getEnergyOverX2()->getEnergyPriceInCent(); 
        
        return new EnergyAndPriceTuple($betweenX1AndX2, $betweenX1AndX2Price);
    }

    public function getProductionPmTotal() : EnergyAndPriceTuple{
        $result = new EnergyAndPriceTuple();
        $result->add($this->productionPm1);
        $result->add($this->productionPm2);
        $result->add($this->productionPm3);

        return $result;
    }

    public function getProductionPm1() : EnergyAndPriceTuple { return $this->productionPm1; }
    public function getProductionPm2() : EnergyAndPriceTuple { return $this->productionPm2; }
    public function getProductionPm3() : EnergyAndPriceTuple { return $this->productionPm3; }

    public function getSavings() : EnergyAndPriceTuple { return $this->savings; }

    public function getAutarkyInPercent() {
        return OverviewPageService::calculateAutarky($this->getSavings()->getEnergyInWatt(), $this->getEnergyOverZero()->getEnergyInWatt());
    }

    public function getSelfConsumptionInPercent()
    {        
        return OverviewPageService::calculateSelfConsumption($this->getSavings()->getEnergyInWatt(), $this->getEnergyUnderZero()->getEnergyInWatt());
    }

    public function getMissingRows() : MissingRowSet { return $this->missingRows; }
    public function getCountOriginRows() { return $this->countOriginRows; }

    // Setter methods
    public function setTimestampForView($timestampForView)
    {
        $this->timestampForView = $timestampForView;
    }

    public function setTimestamps($timestampFrom, $timestampTo)
    {
        $this->timestampFrom = $timestampFrom;
        $this->timestampTo = $timestampTo;
    }

    public function setEnergy($energy, $energyPriceInCent) { $this->energy = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setEnergyOverZero($energy, $energyPriceInCent) { $this->energyOverZero = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setEnergyUnderZero($energy, $energyPriceInCent) { $this->energyUnderZero = new EnergyAndPriceTuple($energy, $energyPriceInCent); }

    public function setProductionPm1($energy, $energyPriceInCent) { $this->productionPm1 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setProductionPm2($energy, $energyPriceInCent) { $this->productionPm2 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setProductionPm3($energy, $energyPriceInCent) { $this->productionPm3 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }

    public function setEnergyUnderX1($energy, $energyPriceInCent) { $this->energyUnderX1 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setEnergyOverX1($energy, $energyPriceInCent) { $this->energyOverX1 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setEnergyUnderX2($energy, $energyPriceInCent) { $this->energyUnderX2 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setEnergyOverX2($energy, $energyPriceInCent) { $this->energyOverX2 = new EnergyAndPriceTuple($energy, $energyPriceInCent); }

    public function setSavings($energy, $energyPriceInCent) { $this->savings = new EnergyAndPriceTuple($energy, $energyPriceInCent); }
    public function setMissingRows($em, $pm1, $pm2, $pm3, $countRows) { $this->missingRows = new MissingRowSet($em, $pm1, $pm2, $pm3, $countRows); }
    public function setCountOriginRows($value) { $this->countOriginRows = $value; }

}
