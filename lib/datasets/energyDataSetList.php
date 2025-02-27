<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class EnergyDataSetList 
{
    private $items = [];

    public function addItem(EnergyDataSet $item)
    {
        $this->items[] = $item;
    }

    public function sizeof()
    {
        return sizeof($this->items);
    }

    public function getTimestampForView($position)
    {
        return $position < sizeof($this->items) ? $this->items[$position]->getTimestampForView() : "";
    }

    public function hasItem($position)
    {
        return array_key_exists($position, $this->items);
    }

    public function getItem($position) : EnergyDataSet
    {
        return $this->hasItem($position) ? $this->items[$position] : null;
    }

    public function convertEnergyToJsArray() : array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->convertEnergyToJsArray();
        }

        return $result;
    }

    public function convertAutarkyToJsArray() : array
    {
        $result = [];
        foreach ($this->items as $item) {
            $result[] = $item->convertAutarkyToJsArray();
        }

        return $result;
    }

    public function getEnergyOverZeroSum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getEnergyOverZero());
        }

        return $result;
    }

    public function getEnergyUnderZeroSum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getEnergyUnderZero());
        }

        return $result;
    }

    public function getSavingsSum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getSavings());
        }

        return $result;
    }

    public function getAutarkyInPercent()
    {
        $totalConsumption = $this->getEnergyOverZeroSum()->getEnergyInWatt() + $this->getSavingsSum()->getEnergyInWatt();
        $percentAutarky = 0;
        if ($totalConsumption > 0) {
            $percentAutarky = (1-($this->getEnergyOverZeroSum()->getEnergyInWatt() / $totalConsumption)) * 100;
        }

        return $percentAutarky;
    }

    public function getProductionPmTotalSum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getProductionPm1());
            $result->add($item->getProductionPm2());
            $result->add($item->getProductionPm3());
        }

        return $result;
    }

    public function getProductionPm1Sum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getProductionPm1());
        }

        return $result;
    }

    public function getProductionPm2Sum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getProductionPm2());
        }

        return $result;
    }

    public function getProductionPm3Sum() : EnergyAndPriceTuple
    {
        $result = new EnergyAndPriceTuple(0, 0);        
        foreach ($this->items as $item) {
            $result->add($item->getProductionPm3());
        }

        return $result;
    }

}
