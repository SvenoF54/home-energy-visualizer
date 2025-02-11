<?php

class EnergyPriceRow
{
    private $id;
    private $timestampFrom; 
    private $timestampTo;   
    private $outCentPricePerWh; 
    private $inCentPricePerWh;  
    private $customValue;

    public function __construct(
        $id = null,
        $timestampFrom = null,
        $timestampTo = null,
        $outCentPricePerWh = 0,
        $inCentPricePerWh = 0,
        $customValue
    ) {
        $this->id = $id;
        $this->timestampFrom = $timestampFrom;
        $this->timestampTo = $timestampTo;
        $this->outCentPricePerWh = $outCentPricePerWh;
        $this->inCentPricePerWh = $inCentPricePerWh;
        $this->customValue = $customValue;
    }

    // Method to load data from associative array (e.g., from a database query)
    public static function createFromArray(array $data)
    {
        return new self(
            $data['id'] ?? null,
            $data['timestamp_from'] ?? null,
            $data['timestamp_to'] ?? null,
            $data['out_cent_price_per_wh'] ?? 0,
            $data['in_cent_price_per_wh'] ?? 0,
            $data['custom_value'] ?? 0
        );
    }

    // Method to convert the object back to an array for database operations
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'timestamp_from' => $this->timestampFrom->format('Y-m-d H:i:s'),
            'timestamp_to' => $this->timestampTo->format('Y-m-d H:i:s'),
            'out_cent_price_per_wh' => $this->outCentPricePerWh,
            'in_cent_price_per_wh' => $this->inCentPricePerWh,
            'custom_value' => $this->customValue,
        ];
    }

    // Getters and Setters
    
    public function setId($id) { $this->id = $id; }
    public function setTimestampFrom($timestampFrom) { $this->timestampFrom = $timestampFrom; }
    public function setTimestampTo($timestampTo) { $this->timestampTo = $timestampTo; }
    public function setOutCentPricePerKwh($outCentPricePerWh) { $this->outCentPricePerWh = $outCentPricePerWh; }
    public function setInCentPricePerWh($inCentPricePerWh) { $this->inCentPricePerWh = $inCentPricePerWh; }
    public function setCustomValue($customValue) { $this->customValue = $customValue; }

    public function getId() { return $this->id; }
    public function getTimestampFrom() { return $this->timestampFrom; }
    public function getTimestampFromDate() { return substr($this->timestampFrom, 0, 10); }
    public function getTimestampTo() { return $this->timestampTo; }
    public function getTimestampToDate() { return substr($this->timestampTo, 0, 10); }
    public function getOutCentPricePerWh() { return $this->outCentPricePerWh; }
    public function getInCentPricePerWh() { return $this->inCentPricePerWh; }
    public function getCustomValue() { return $this->customValue; }
    public function isCustomValue() { return $this->customValue == 1; }
}

?>
