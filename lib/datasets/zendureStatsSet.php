<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ZendureStatsSet {
    public const STATS_KEY = "statistics";
    private $akkuPackUpperLimit = null;
    private $kvsTable;
    private $config;

    private $lastUpdated = null;
    private $todayDate = null;
    private $today = null;
    private $total = null;

    public function __construct() {        
        $this->kvsTable = KeyValueStoreTable::getInstance();
        $this->config = Configuration::getInstance()->zendure();
    }

    public function toJson()
    {
        return json_encode([
            'todayDate' => $this->todayDate,
            'total' => $this->total->toArray(),
            'today' => $this->today->toArray(),
        ]);
    }

    public function fromJson($json)
    {
        if ($json == null) { return; }
        $data = json_decode($json, true);         
        if ($data === null) { return; }
            
        $this->total = new ZendureStatsSubSet($data["total"]);        
        $this->today = new ZendureStatsSubSet($data["today"]);
        $this->todayDate = $data["todayDate"] ?? null;
    }
    
    public function loadData()
    {
        $rowUpperLimit = $this->kvsTable->getRow(KeyValueStoreScopeEnum::Zendure, $this->config->getKeyPackMaxUpperLoadLimit());
        $this->akkuPackUpperLimit = ($rowUpperLimit != null) ? $rowUpperLimit->getValue() : null;

        $row = $this->kvsTable->getRow(KeyValueStoreScopeEnum::Zendure, self::STATS_KEY);
        $this->fromJson($row->getJsonData());

        $this->lastUpdated = ($row->getUpdated() != null) ? $row->getUpdated() : date("Y-m-d H:i:s", strtotime("-1 day"));        
        if ($this->todayDate != date("Y-m-d")) {
            // Day changed
            $this->todayDate = date("Y-m-d");
            $this->today = new ZendureStatsSubSet();
        }
    }

    public function saveData()
    {
        $json = $this->toJson();
        $this->kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Zendure, self::STATS_KEY, $json, "", $json);
    }

    public function update($key, $value) 
    {
        // Check only every minute
        $updateSameMinute = date("Y-m-d H:i", strtotime($this->lastUpdated)) === date("Y-m-d H:i");                
        if ($updateSameMinute) { return; }

        if ($key == $this->config->getKeyAkkuCapacity()) {                    
            // Check akku loaded complete
            $akkuLoadedComplete = ($value * 10 >= $this->akkuPackUpperLimit);            
            
            $this->total->akkuPackChargedDays += ($this->today->akkuPackChargedDays == 0 && $akkuLoadedComplete) ? 1 : 0;
            $this->today->akkuPackChargedDays = ($this->today->akkuPackChargedDays || $akkuLoadedComplete) ? 1 : 0;
            
            $this->today->akkuPackChargedMinutes += $akkuLoadedComplete ? 1 : 0;
            $this->total->akkuPackChargedMinutes += $akkuLoadedComplete ? 1 : 0;
        }

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
