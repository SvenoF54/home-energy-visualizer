<?php

class EnergyAndPriceTuple {
    private $energyInWatt;
    private $energyPriceInCent;

    public function __construct($energyInWatt = null, $energyPriceInCent = null) {
        $this->energyInWatt = $energyInWatt;
        $this->energyPriceInCent = $energyPriceInCent;
    }

    public function add(EnergyAndPriceTuple $energyAndPriceTuple)
    {
        $this->energyInWatt += $energyAndPriceTuple->getEnergyInWatt();
        $this->energyPriceInCent += $energyAndPriceTuple->getEnergyPriceInCent();
    }

    public function getEnergyInWatt() {
        return $this->energyInWatt;
    }

    public function getEnergyPriceInCent() {
        return $this->energyPriceInCent;
    }

    public function getEnergyInWattFormated() {
        return StringHelper::formatEnergyInWattHour($this->energyInWatt);
    }

    public function getEnergyPriceFormatted() {
        return StringHelper::formatCurrency($this->energyPriceInCent);
    }
}
