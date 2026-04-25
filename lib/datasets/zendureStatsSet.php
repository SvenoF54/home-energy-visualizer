<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ZendureStatsSet {
    public const STATS_KEY = "statistics";
    private $kvsTable;    

    // Jetzt als Arrays für Phase 1, 2 und 3
    private $akkuPackUpperLimit = [];
    private $lastUpdated = [];
    private $todayDate = [];
    private $today = [];
    private $total = [];

    public function __construct() {        
        $this->kvsTable = KeyValueStoreTable::getInstance();        

        // Initialize 3 phases
        for ($i = 1; $i <= 3; $i++) {
            $this->total[$i] = new ZendureStatsSubSet();        
            $this->today[$i] = new ZendureStatsSubSet();
            $this->todayDate[$i] = null;
            $this->lastUpdated[$i] = date("Y-m-d H:i:s", strtotime("-1 day"));
            $this->akkuPackUpperLimit[$i] = null;
        }
    }

    private function getScopeByPhase($phase) {
        $name = "ZendurePhase" . $phase;
        return constant("KeyValueStoreScopeEnum::$name");
    }

    public function loadData() {
        for ($i = 1; $i <= 3; $i++) {
            $scope = $this->getScopeByPhase($i);

            // Load upper value per phase
            $rowUpperLimit = $this->kvsTable->getRow($scope, "socSet");
            $this->akkuPackUpperLimit[$i] = ($rowUpperLimit != null) ? $rowUpperLimit->getValue() : null;

            // Load stats per phase
            $row = $this->kvsTable->getRow($scope, self::STATS_KEY);
            if (isset($row)) {
                $this->fromJson($i, $row->getJsonData());
                $this->lastUpdated[$i] = $row->getUpdated() ?? date("Y-m-d H:i:s", strtotime("-1 day"));
            }

            // check day change per phase
            if ($this->todayDate[$i] != date("Y-m-d")) {
                $this->todayDate[$i] = date("Y-m-d");
                $this->today[$i] = new ZendureStatsSubSet();
            }
        }
    }

    public function saveData() {
        for ($i = 1; $i <= 3; $i++) {
            $scope = $this->getScopeByPhase($i);
            $json = $this->toJson($i);
            $this->kvsTable->insertOrUpdate($scope, self::STATS_KEY, $json, "Statistik Phase " . $i, $json);
        }
    }

    public function update($key, $value, $phase = 1) {
        if (!isset($this->today[$phase])) return;

        // Check only every minute per phase
        $updateSameMinute = date("Y-m-d H:i", strtotime($this->lastUpdated[$phase])) === date("Y-m-d H:i");                
        if ($updateSameMinute) { return; }

        if ($key == "electricLevel") {                    
            $akkuLoadedComplete = isset($this->akkuPackUpperLimit[$phase]) ? ($value * 10 >= $this->akkuPackUpperLimit[$phase]) : false;
            
            $this->total[$phase]->akkuPackChargedDays += ($this->today[$phase]->akkuPackChargedDays == 0 && $akkuLoadedComplete) ? 1 : 0;
            $this->today[$phase]->akkuPackChargedDays = ($this->today[$phase]->akkuPackChargedDays || $akkuLoadedComplete) ? 1 : 0;
            
            $this->today[$phase]->akkuPackChargedMinutes += $akkuLoadedComplete ? 1 : 0;
            $this->total[$phase]->akkuPackChargedMinutes += $akkuLoadedComplete ? 1 : 0;
        }
    }

    public function toJson($phase) {
        return json_encode([
            'todayDate' => $this->todayDate[$phase],
            'total' => $this->total[$phase]->toArray(),
            'today' => $this->today[$phase]->toArray(),
        ]);
    }

    public function fromJson($phase, $json) {
        if ($json == null) return;
        $data = json_decode($json, true);         
        if ($data === null) return;
            
        $this->total[$phase] = new ZendureStatsSubSet($data["total"] ?? []);        
        $this->today[$phase] = new ZendureStatsSubSet($data["today"] ?? []);
        $this->todayDate[$phase] = $data["todayDate"] ?? null;
    }
}


class ZendureStatsSubSet {
    public $akkuPackChargedMinutes = 0;
    public $akkuPackChargedDays = 0;

    public function __construct(array $data = [])
    {
        $this->fromArray($data);
    }

    public function toArray()
    {
        return [
                'akkuPackChargedDays' => $this->akkuPackChargedDays,
                'akkuPackChargedMinutes' => $this->akkuPackChargedMinutes,
            ];
    }

    public function fromArray($data)
    {
        if ($data === null) { return; }
            
        $this->akkuPackChargedDays = $data['akkuPackChargedDays'] ?? 0;
        $this->akkuPackChargedMinutes = $data['akkuPackChargedMinutes'] ?? 0;
    }

}
