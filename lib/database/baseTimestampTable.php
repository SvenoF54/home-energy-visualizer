<?php
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
                COUNT(*) AS totalRows,
                MIN($this->timestampFromRowName) AS firstDate,
                MAX($this->timestampFromRowName) AS lastDate
            FROM $this->tableName;
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
