<?

class SavingsStatisticSet {
    private EnergyAndPriceTuple $savings;
    private EnergyAndPriceTuple $feedIn;
    private EnergyAndPriceTuple $consumption;

    public static function CreateEmptySet() {
        return new SavingsStatisticSet(new EnergyAndPriceTuple(), new EnergyAndPriceTuple(), new EnergyAndPriceTuple());
    }

    public function __construct(
        EnergyAndPriceTuple $savings = null, 
        EnergyAndPriceTuple $feedIn = null, 
        EnergyAndPriceTuple $consumption = null
    ) {
        $this->savings = $savings ?? new EnergyAndPriceTuple();
        $this->feedIn = $feedIn ?? new EnergyAndPriceTuple();
        $this->consumption = $consumption ?? new EnergyAndPriceTuple();
    }

    public function getSavings(): EnergyAndPriceTuple {
        return $this->savings;
    }

    public function setSavings(EnergyAndPriceTuple $savings): void {
        $this->savings = $savings;
    }

    public function getFeedIn(): EnergyAndPriceTuple {
        return $this->feedIn;
    }

    public function setFeedIn(EnergyAndPriceTuple $feedIn): void {
        $this->feedIn = $feedIn;
    }

    public function getConsumption(): EnergyAndPriceTuple {
        return $this->consumption;
    }

    public function setConsumption(EnergyAndPriceTuple $consumption): void {
        $this->consumption = $consumption;
    }
}



class SavingsStatisticDictionary {
    private array $savingsDict = []; 

    public function add(string $key, SavingsStatisticSet $value): void {
        $this->savingsDict[$key] = $value;
    }

    public function get(string $key): SavingsStatisticSet {
        return array_key_exists($key, $this->savingsDict) 
            ? $this->savingsDict[$key] 
            : SavingsStatisticSet::createEmptySet();
    }
}
