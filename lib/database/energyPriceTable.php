<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class EnergyPriceTable extends BaseTimestampTable
{
    public static function getInstance() : EnergyPriceTable
    {
        $db = Database::getInstance();
        return new EnergyPriceTable($db->getPdoConnection());
    }

    public function __construct($pdo) {
        parent::__construct($pdo, "energy_price", "timestamp_from", "timestamp_to");
    }

    public function insertCustomData(EnergyPriceRow $energyPriceRow)
    {
        $this->error = "";
        $sql = "INSERT INTO $this->tableName 
                    (timestamp_from, timestamp_to, out_cent_price_per_wh, in_cent_price_per_wh, custom_value)
                VALUES (:timestamp_from, :timestamp_to, :out_cent_price_per_wh, :in_cent_price_per_wh, 1)";
    
        try {
            $stmt = $this->pdo->prepare($sql);

            $timestampTo = $energyPriceRow->getTimestampToDate() . " 23:59:59";    
            $stmt->bindParam(':timestamp_from', $energyPriceRow->getTimestampFrom());
            $stmt->bindParam(':timestamp_to', $timestampTo);
            $stmt->bindParam(':out_cent_price_per_wh', $energyPriceRow->getOutCentPricePerWh());
            $stmt->bindParam(':in_cent_price_per_wh', $energyPriceRow->getInCentPricePerWh());
    
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            $this->error = "Fehler beim Speichern der Daten: " . $e->getMessage();
            return false;
        }
    }
    
    public function updateCustomData(EnergyPriceRow $energyPriceRow)
    {
        $this->error = "";
        $sql = "UPDATE $this->tableName 
                SET timestamp_from = :timestamp_from, 
                    timestamp_to = :timestamp_to, 
                    out_cent_price_per_wh = :out_cent_price_per_wh, 
                    in_cent_price_per_wh = :in_cent_price_per_wh
                WHERE id = :id";
    
        try {
            $stmt = $this->pdo->prepare($sql);

            $timestampTo = $energyPriceRow->getTimestampToDate() . " 23:59:59";
            $stmt->execute([
                ':timestamp_from' => $energyPriceRow->getTimestampFrom(),
                ':timestamp_to' => $timestampTo,
                ':out_cent_price_per_wh' => $energyPriceRow->getOutCentPricePerWh(),
                ':in_cent_price_per_wh' => $energyPriceRow->getInCentPricePerWh(),
                ':id' => $energyPriceRow->getId()
            ]);
    
            return true;
        } catch (PDOException $e) {
            $this->error = "Fehler beim Speichern der Daten: " . $e->getMessage();
            return false;
        }
    }

    public function deleteCustomRow($id)
    {
        $this->error = "";
        $sql = "DELETE FROM $this->tableName WHERE id = :id AND custom_value = 1";
    
        try {
            $stmt = $this->pdo->prepare($sql);    
            $stmt->execute([
                ':id' => $id
            ]);
    
            return true;
        } catch (PDOException $e) {
            $this->error = "Fehler beim Löschen der Daten: " . $e->getMessage();
            return false;
        }
    }
        
    public function getPriceForDateTime($dateTime)
    {
        $dateTime = is_string($dateTime) ? new DateTime($dateTime) : $dateTime;
        $query = "
            SELECT *
            FROM $this->tableName
            WHERE :datetime BETWEEN timestamp_from AND timestamp_to
            LIMIT 1
        ";
    
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                ':datetime' => $dateTime->format('Y-m-d H:i:s')
            ]);
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);    
            if ($row) {
                return EnergyPriceRow::createFromArray($row);
            }
    
            return null;
        } catch (PDOException $e) {
            throw new Exception("Fehler bei der Abfrage: " . $e->getMessage());
        }
    }
    
    public function getCustomRow($id)
    {
        $query = "
            SELECT *
            FROM $this->tableName
            WHERE ID = :id
        ";
    
        try {
            $stmt = $this->pdo->prepare($query);            
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);            
            $stmt->execute();
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return EnergyPriceRow::createFromArray($row);
            }
    
            return null;
        } catch (PDOException $e) {
            throw new Exception("Fehler bei der Abfrage: " . $e->getMessage());
        }
    }
    

    public function getCustomAndAutomaticPriceRows()
    {
        $query = "
            (
                -- Hole alle Zeilen mit custom_value = 1
                SELECT id, timestamp_from, timestamp_to, out_cent_price_per_wh, in_cent_price_per_wh, custom_value
                FROM $this->tableName
                WHERE custom_value = 1
            )
            UNION
            (
                -- Hole die äußeren Zeiträume mit custom_value = 0
                SELECT id, timestamp_from, timestamp_to, out_cent_price_per_wh, in_cent_price_per_wh, custom_value
                FROM $this->tableName
                WHERE custom_value = 0
                AND (
                    timestamp_to < (SELECT MIN(timestamp_from) FROM $this->tableName WHERE custom_value = 1)
                    OR timestamp_from > (SELECT MAX(timestamp_to) FROM $this->tableName WHERE custom_value = 1)
                )
            )
            ORDER BY timestamp_from ASC;
        ";
    
        try {
            $stmt = $this->pdo->prepare($query);
            if (!$stmt->execute()) {
                throw new Exception("Fehler bei der Ausführung der Abfrage: " . implode(", ", $stmt->errorInfo()));
            }
    
            $rows = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = EnergyPriceRow::createFromArray($row);
            }
    
            return $rows;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
            
    public function doesTimeOverlap($idPriceRow, $timestampFrom, $timestampTo)
    {
        $query = "
            SELECT COUNT(*) as overlap_count
            FROM $this->tableName
            WHERE 
                id != :idPriceRow AND
                (timestamp_from < :timestampTo AND timestamp_to > :timestampFrom)
        ";
    
        try {
            $stmt = $this->pdo->prepare($query);
            $stmt->bindParam(':idPriceRow', $idPriceRow, PDO::PARAM_INT);
            $stmt->bindParam(':timestampFrom', $timestampFrom, PDO::PARAM_STR);
            $stmt->bindParam(':timestampTo', $timestampTo, PDO::PARAM_STR);
    
            if (!$stmt->execute()) {
                throw new Exception("Fehler bei der Ausführung der Abfrage: " . implode(", ", $stmt->errorInfo()));
            }
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['overlap_count'] > 0;
    
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
        
}
