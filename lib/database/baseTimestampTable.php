<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class BaseTimestampTable extends BaseTable {
    protected $timestampFromRowName = "";
    protected $timestampToRowName = "";
    private $statisticSet = null;
    
    public function __construct($pdo, $tableName, $timestampFromRowName, $timestampToRowName = null)
    {
        parent::__construct($pdo, $tableName);
        $this->timestampFromRowName = $timestampFromRowName;
        $this->timestampToRowName = $timestampToRowName;
    }

    public function getStatistics(): TableStatisticSet
    {
        if ($this->statisticSet !== null) {
            return $this->statisticSet;
        }
    
        $sql = "
            SELECT 
                -- Gesamtdaten
                (SELECT COUNT(*) FROM $this->tableName) AS totalRows,
                (SELECT MIN($this->timestampFromRowName) FROM $this->tableName) AS firstDate,
                (SELECT MAX($this->timestampFromRowName) FROM $this->tableName) AS lastDate,

                -- PM-Daten
                (SELECT COUNT(*) FROM $this->tableName
                WHERE pm1_total_power IS NOT NULL OR pm2_total_power IS NOT NULL OR pm3_total_power IS NOT NULL) AS totalPmRows,
                (SELECT MIN($this->timestampFromRowName) FROM $this->tableName 
                WHERE pm1_total_power IS NOT NULL OR pm2_total_power IS NOT NULL OR pm3_total_power IS NOT NULL) AS firstPmDate,
                (SELECT MAX($this->timestampFromRowName) FROM $this->tableName 
                WHERE pm1_total_power IS NOT NULL OR pm2_total_power IS NOT NULL OR pm3_total_power IS NOT NULL) AS lastPmDate;
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);    
        if (!$data) {
            $this->statisticSet = new TableStatisticSet(0, null, null);
        } else {
            $this->statisticSet = new TableStatisticSet(
                (int) $data['totalRows'], 
                $data['firstDate'], 
                $data['lastDate']
            );
            $this->statisticSet->setPmFirstRowDate($data['firstPmDate']);
            $this->statisticSet->setPmLastRowDate($data['lastPmDate']);
            $this->statisticSet->setPmTotalRows((int) $data['totalPmRows']);
        }
    
        return $this->statisticSet;
    }

    public function getGaps(): array
    {
        $sql = "
            WITH RankedWindows AS (
                SELECT 
                    timestamp_from, 
                    timestamp_to,
                    LEAD(timestamp_from) OVER (ORDER BY timestamp_from) AS nextTimestampFrom
                FROM 
                    $this->tableName
            )
            SELECT 
                timestamp_to AS gapStart, 
                nextTimestampFrom AS gapEnd,
                TIMESTAMPDIFF(SECOND, timestamp_to, nextTimestampFrom) - 1 AS gapDurationInSeconds
            FROM 
                RankedWindows
            WHERE 
                timestamp_to < nextTimestampFrom
                AND TIMESTAMPDIFF(SECOND, timestamp_to, nextTimestampFrom) > 1; -- Ignore <= 1 Sekunde
        ";
    
        $stmt = $this->pdo->query($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error executing query: " . implode(" ", $this->pdo->errorInfo()));
        }
    
        $gaps = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $gaps[] = TableGapSet::createFromArray($row);
        }
    
        return $gaps;
    }            
}
