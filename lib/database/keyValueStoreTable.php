<?php
class KeyValueStoreTable extends BaseTable {
    
    public function __construct($pdo) {
        parent::__construct($pdo, "key_value_store");
    }

    public function insertOrUpdate(KeyValueStoreScopeEnum $scope, $key, $value, $notice = "")
    {
        try {
            $sql = "
                INSERT INTO {$this->tableName} 
                (`scope`, `store_key`, `value`, `notice`, `inserted`, `updated`)
                VALUES (:scope, :storeKey, :value, :notice, :now, null)
                ON DUPLICATE KEY UPDATE
                    `scope` = VALUES(`scope`),
                    `store_key` = VALUES(`store_key`),
                    `value` = VALUES(`value`),
                    `notice` = VALUES(`notice`),
                    `updated` = :now
            ";
        
            $stmt = $this->pdo->prepare($sql);
        
            $stmt->bindValue(':scope', $scope->value, PDO::PARAM_STR);
            $stmt->bindValue(':storeKey', $key, PDO::PARAM_STR);
            $stmt->bindValue(':value', $value, PDO::PARAM_STR);
            $stmt->bindValue(':notice', $notice, PDO::PARAM_STR);
            $stmt->bindValue(':now', date("Y-m-d H:i:s"), PDO::PARAM_STR);
        
            $stmt->execute();
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            var_dump($this->error);
            return false;
        }
    }
    
    public function getLastUpdatedTime(KeyValueStoreScopeEnum $scope, $key)
    {
        try {
            $sql = "SELECT * FROM {$this->tableName} WHERE scope = :scope AND store_key = :key";
            $stmt = $this->pdo->prepare($sql);
        
            $stmt->bindValue(':scope', $scope->value, PDO::PARAM_STR);
            $stmt->bindValue(':key', $key, PDO::PARAM_STR);

            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return null;
            }

            return !empty($row["updated"]) ? $row["updated"] : $row["inserted"];

        } catch (PDOException $e) {
            $this->error = "Fehler beim Lesen der Daten: " . $e->getMessage();
            return null;
        }

    }

    public function getAllRows(): array {
        $sql = "SELECT * FROM {$this->tableName}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return array_map(fn($row) => KeyValueStoreRow::createFromRow($row), $rows);
    }    
}
