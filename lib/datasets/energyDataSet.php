<?php

class EnergyDataSet {
    private $timestampFrom;
    private $timestampTo;
    private EnergyAndPriceTuple $energy;
    private EnergyAndPriceTuple $energyOverZero;
    private EnergyAndPriceTuple $energyUnderZero;
    private EnergyAndPriceTuple $energyUnderX1;
    private EnergyAndPriceTuple $energyOverX1;
    private EnergyAndPriceTuple $energyUnderX2;
    private EnergyAndPriceTuple $energyOverX2;
    private EnergyAndPriceTuple $generationPm1;
    private EnergyAndPriceTuple $generationPm2;
    private EnergyAndPriceTuple $generationPm3;
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

    // Getter methods
    public function getTimestampFrom()
    {
        return $this->timestampFrom;
    }

    public function getTimestampTo()
    {
        return $this->timestampTo;
    }

    public function getEnergy() : EnergyAndPriceTuple {
        return $this->energy;
    }

    public function getEnergyOverZero(): EnergyAndPriceTuple {
        return $this->energyOverZero;
    }

    public function getEnergyUnderZero() : EnergyAndPriceTuple {
        return $this->energyUnderZero;
    }

    public function getEnergyUnderX1() : EnergyAndPriceTuple {
        return $this->energyUnderX1;
    }

    public function getEnergyOverX1() : EnergyAndPriceTuple {
        return $this->energyOverX1;
    }

    public function getEnergyUnderX2() : EnergyAndPriceTuple {
        return $this->energyUnderX2;
    }

    public function getEnergyOverX2() : EnergyAndPriceTuple {
        return $this->energyOverX2;
    }

    public function getEnergyBetweenX1AndX2() : EnergyAndPriceTuple {
        $betweenX1AndX2 =  $this->getEnergyOverZero()->getEnergyInWatt() - $this->getEnergyUnderX1()->getEnergyInWatt() - $this->getEnergyOverX2()->getEnergyInWatt(); 
        return new EnergyAndPriceTuple($betweenX1AndX2, $this->getEnergyOverZero()->getEnergyPriceInCent());
    }

    public function getGenerationPm1() : EnergyAndPriceTuple{
        return $this->generationPm1;
    }

    public function getGenerationPm2() : EnergyAndPriceTuple{
        return $this->generationPm2;
    }

    public function getGenerationPm3() : EnergyAndPriceTuple{
        return $this->generationPm3;
    }

    public function getSavings() : EnergyAndPriceTuple{
        return $this->savings;
    }

    public function getMissingRows() : MissingRowSet{
        return $this->missingRows;
    }

    public function getCountOriginRows() {
        return $this->countOriginRows;
    }

    // Setter methods
    public function setTimestamps($timestampFrom, $timestampTo)
    {
        $this->timestampFrom = $timestampFrom;
        $this->timestampTo = $timestampTo;
    }

    public function setEnergy($energy, $energyPriceInCent) {
        $this->energy = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setEnergyOverZero($energy, $energyPriceInCent) {
        $this->energyOverZero = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setEnergyUnderZero($energy, $energyPriceInCent) {
        $this->energyUnderZero = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setGenerationPm1($energy, $energyPriceInCent) {
        $this->generationPm1 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setGenerationPm2($energy, $energyPriceInCent) {
        $this->generationPm2 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setGenerationPm3($energy, $energyPriceInCent) {
        $this->generationPm3 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setEnergyUnderX1($energy, $energyPriceInCent) {
        $this->energyUnderX1 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setEnergyOverX1($energy, $energyPriceInCent) {
        $this->energyOverX1 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setEnergyUnderX2($energy, $energyPriceInCent) {
        $this->energyUnderX2 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setEnergyOverX2($energy, $energyPriceInCent) {
        $this->energyOverX2 = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setSavings($energy, $energyPriceInCent) {
        $this->savings = new EnergyAndPriceTuple($energy, $energyPriceInCent);
    }

    public function setMissingRows($em, $pm1, $pm2, $pm3, $countRows) {
        $this->missingRows = new MissingRowSet($em, $pm1, $pm2, $pm3, $countRows);
    }

    public function setCountOriginRows($value) {
        $this->countOriginRows = $value;
    }

}
