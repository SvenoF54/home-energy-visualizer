<?php

class EnergyAndPriceTuple {
    private $energyInWatt;
    private $energyPriceInCent;

    public function __construct($energyInWatt = null, $energyPriceInCent = null) {
        $this->energyInWatt = $energyInWatt;
        $this->energyPriceInCent = $energyPriceInCent;
    }

    public function getEnergyInWatt() {
        return $this->energyInWatt;
    }

    public function getEnergyPriceInCent() {
        return $this->energyPriceInCent;
    }

    public function getHourlyEnergyPriceFormatted() {
        return sprintf('%.2f', $this->energyPriceInCent);
    }
}
