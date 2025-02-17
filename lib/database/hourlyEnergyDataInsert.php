<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class HourlyEnergyDataInsert extends BaseTable {
    
    public function __construct($pdo) {
        parent::__construct($pdo, "hourly_energy_data");
    }
    
    public function insertOrUpdate($timestampFrom, $timestampTo, $quarterHourInterval, $outPricePerWattHour, $inPricePerWattHour,
                                    RealTimeEnergyDataRow $realTimeRow) 
    {
        $this->error = "";

        // SQL (ON DUPLICATE KEY UPDATE)
        $sql = "INSERT INTO $this->tableName 
                    (timestamp_from, timestamp_to, value_for_x_quarter_hours, out_cent_price_per_wh, in_cent_price_per_wh, 
                    em_total_power, em_total_over_zero, em_total_under_zero,
                    pm1_total_power, pm2_total_power, pm3_total_power,
                    em_missing_rows, pm1_missing_rows, pm2_missing_rows, pm3_missing_rows,
                    count_rows)
                VALUES (:timestampFrom, :timestampTo, :quarterHourInterval, :outPricePerWattHour, :inPricePerWattHour, 
                    :emTotalPower, :emTotalPowerOverZero, :emTotalPowerUnderZero,
                    :pm1TotalPower, :pm2TotalPower, :pm3TotalPower,
                    :emMissingRows, :pm1MissingRows, :pm2MissingRows, :pm3MissingRows,
                    :countRows)
                ON DUPLICATE KEY UPDATE
                    timestamp_to = VALUES(timestamp_to),
                    value_for_x_quarter_hours = VALUES(value_for_x_quarter_hours),
                    out_cent_price_per_wh = VALUES(out_cent_price_per_wh),
                    in_cent_price_per_wh = VALUES(in_cent_price_per_wh),
                    em_total_power = VALUES(em_total_power),
                    em_total_over_zero = VALUES(em_total_over_zero),
                    em_total_under_zero = VALUES(em_total_under_zero),
                    pm1_total_power = VALUES(pm1_total_power),
                    pm2_total_power = VALUES(pm2_total_power),
                    pm3_total_power = VALUES(pm3_total_power),
                    em_missing_rows = VALUES(em_missing_rows),
                    pm1_missing_rows = VALUES(pm1_missing_rows),
                    pm2_missing_rows = VALUES(pm2_missing_rows),
                    pm3_missing_rows = VALUES(pm3_missing_rows),
                    count_rows = VALUES(count_rows)";

        try {
            $stmt = $this->pdo->prepare($sql);
            if (!$stmt) {
                $this->error = "Fehler beim Vorbereiten des Statements: " . implode(":", $this->pdo->errorInfo());
                return false;
            }

            $stmt->bindValue(':timestampFrom', $timestampFrom, PDO::PARAM_STR);
            $stmt->bindValue(':timestampTo', $timestampTo, PDO::PARAM_STR);
            $stmt->bindValue(':quarterHourInterval', $quarterHourInterval, PDO::PARAM_STR);  // For decimal use string, bacused the db saves them as exakt value not as float
            $stmt->bindValue(':outPricePerWattHour', $outPricePerWattHour, PDO::PARAM_STR);
            $stmt->bindValue(':inPricePerWattHour', $inPricePerWattHour, PDO::PARAM_STR);

            $stmt->bindValue(':emTotalPower', $realTimeRow->getEmTotalPower(), PDO::PARAM_STR);
            $stmt->bindValue(':emTotalPowerOverZero', $realTimeRow->getEmTotalPowerOverZero(), PDO::PARAM_STR);
            $stmt->bindValue(':emTotalPowerUnderZero', $realTimeRow->getEmTotalPowerUnderZero(), PDO::PARAM_STR);
            $stmt->bindValue(':pm1TotalPower', $realTimeRow->getPm1TotalPower(), PDO::PARAM_STR);
            $stmt->bindValue(':pm2TotalPower', $realTimeRow->getPm2TotalPower(), PDO::PARAM_STR);
            $stmt->bindValue(':pm3TotalPower', $realTimeRow->getPm3TotalPower(), PDO::PARAM_STR);
            $stmt->bindValue(':emMissingRows', $realTimeRow->getEmMissingRows(), PDO::PARAM_INT);
            $stmt->bindValue(':pm1MissingRows', $realTimeRow->getPm1MissingRows(), PDO::PARAM_INT);
            $stmt->bindValue(':pm2MissingRows', $realTimeRow->getPm2MissingRows(), PDO::PARAM_INT);
            $stmt->bindValue(':pm3MissingRows', $realTimeRow->getPm3MissingRows(), PDO::PARAM_INT);
            $stmt->bindValue(':countRows', $realTimeRow->getCountRows(), PDO::PARAM_INT);

            if (!$stmt->execute()) {
                $this->error = "Fehler beim Speichern der Daten: " . implode(":", $stmt->errorInfo());
                return false;
            }

            $stmt->closeCursor();
            return ($this->error == "");
        } catch (PDOException $e) {            
            $this->error = "PDOException: " . $e->getMessage();
            return false;
        }        
    }

}
?>
