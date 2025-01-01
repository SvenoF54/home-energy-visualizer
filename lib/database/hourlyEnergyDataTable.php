<?

class HourlyEnergyDataTable extends BaseTimestampTable {        
    public function __construct($pdo) {
        parent::__construct($pdo, "hourly_energy_data", "timestamp_from", "timestamp_to");
    }

    public function getEnergyData($startTime, $endTime) : EnergyDataSet 
    {
        # if power value is only a port of a hour (means value_for_x_quarter_hours < 4) then devide the value for the part of hour
        # otherwise take the whole value, because it's already for full hours
        $sql = "
            SELECT 
                SUM(IF(value_for_x_quarter_hours < 4, em_total_power / 4 * value_for_x_quarter_hours, em_total_power)) AS sum_power,
                SUM(
                    IF(value_for_x_quarter_hours < 4, em_total_power / 4 * value_for_x_quarter_hours, em_total_power)
                    * out_cent_price_per_wh) AS sum_price,                                                


                SUM(IF(value_for_x_quarter_hours < 4, em_total_over_zero / 4 * value_for_x_quarter_hours, em_total_over_zero)) AS sum_power_over_zero,
                SUM(
                    IF(value_for_x_quarter_hours < 4, em_total_over_zero / 4 * value_for_x_quarter_hours, em_total_over_zero)
                    * out_cent_price_per_wh) AS sum_price_over_zero,                                                


                SUM(IF(value_for_x_quarter_hours < 4, em_total_under_zero / 4 * value_for_x_quarter_hours, em_total_under_zero)) AS sum_power_under_zero,
                SUM(
                    IF(value_for_x_quarter_hours < 4, em_total_under_zero / 4 * value_for_x_quarter_hours, em_total_under_zero)
                    * in_cent_price_per_wh) AS sum_price_under_zero,                                                

                SUM(IF(value_for_x_quarter_hours < 4, pm1_total_power / 4 * value_for_x_quarter_hours, pm1_total_power)) AS sum_pm1_total_power,
                SUM(
                    IF(value_for_x_quarter_hours < 4, pm1_total_power / 4 * value_for_x_quarter_hours, pm1_total_power)
                    * in_cent_price_per_wh) AS sum_price_pm1,                                                

                SUM(IF(value_for_x_quarter_hours < 4, pm2_total_power / 4 * value_for_x_quarter_hours, pm2_total_power)) AS sum_pm2_total_power,
                SUM(
                    IF(value_for_x_quarter_hours < 4, pm2_total_power / 4 * value_for_x_quarter_hours, pm2_total_power)
                    * in_cent_price_per_wh) AS sum_price_pm2,                                                

                SUM(IF(value_for_x_quarter_hours < 4, pm3_total_power / 4 * value_for_x_quarter_hours, pm3_total_power)) AS sum_pm3_total_power,
                SUM(
                    IF(value_for_x_quarter_hours < 4, pm1_total_power / 4 * value_for_x_quarter_hours, pm3_total_power)
                    * in_cent_price_per_wh) AS sum_price_pm3,                                                
                    
                SUM(
                    (
                        CASE 
                            WHEN value_for_x_quarter_hours < 4 
                            THEN 
                                (COALESCE(pm1_total_power, 0) 
                                + COALESCE(pm2_total_power, 0) 
                                + COALESCE(pm3_total_power, 0) 
                                + COALESCE(em_total_under_zero, 0)) 
                                / 4 * value_for_x_quarter_hours
                            ELSE 
                                (COALESCE(pm1_total_power, 0) 
                                + COALESCE(pm2_total_power, 0) 
                                + COALESCE(pm3_total_power, 0) 
                                + COALESCE(em_total_under_zero, 0))
                        END
                    ) 
                ) AS sum_savings,

                SUM(
                    (
                        CASE 
                            WHEN value_for_x_quarter_hours < 4 
                            THEN 
                                (COALESCE(pm1_total_power, 0) 
                                + COALESCE(pm2_total_power, 0) 
                                + COALESCE(pm3_total_power, 0) 
                                + COALESCE(em_total_under_zero, 0)) 
                                / 4 * value_for_x_quarter_hours
                            ELSE 
                                (COALESCE(pm1_total_power, 0) 
                                + COALESCE(pm2_total_power, 0) 
                                + COALESCE(pm3_total_power, 0) 
                                + COALESCE(em_total_under_zero, 0))
                        END
                    ) * out_cent_price_per_wh
                ) AS sum_price_savings,


                SUM(em_missing_rows) AS sum_em_missing_rows,
                SUM(pm1_missing_rows) AS sum_pm1_missing_rows,
                SUM(pm2_missing_rows) AS sum_pm2_missing_rows,
                SUM(pm3_missing_rows) AS sum_pm3_missing_rows,
                SUM(count_rows) AS sum_count_origin_rows,
                SUM(value_for_x_quarter_hours) AS sum_quarter_hours,
                COUNT(*) AS count_rows
            FROM 
                $this->tableName
            WHERE                
                timestamp_from >= :startTime
                AND timestamp_from <= :endTime
        ";

        try {
            $stmt = $this->pdo->prepare($sql);    
            $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
    
            if (!$stmt->execute()) {
                throw new Exception("Fehler bei der Ausführung der Abfrage: " . implode(", ", $stmt->errorInfo()));
            }
    
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                return new EnergyDataSet($startTime, $endTime);
            }
    
            $result = new EnergyDataSet($startTime, $endTime);
            $result->setEnergy(round($row["sum_power"], 0), round($row["sum_price"], 2));
            $result->setEnergyOverZero(round($row["sum_power_over_zero"], 0), round($row["sum_price_over_zero"], 2));
            $result->setEnergyUnderZero(round($row["sum_power_under_zero"], 0), round($row["sum_price_under_zero"], 2));
            $result->setGenerationPm1(round($row["sum_pm1_total_power"], 0), round($row["sum_price_pm1"], 2));
            $result->setGenerationPm2(round($row["sum_pm2_total_power"], 0), round($row["sum_price_pm2"], 2));
            $result->setGenerationPm3(round($row["sum_pm3_total_power"], 0), round($row["sum_price_pm3"], 2));
            $result->setSavings(round($row["sum_savings"], 0), round($row["sum_price_savings"], 2));
    
            $result->setMissingRows(
                $row["sum_em_missing_rows"], 
                $row["sum_pm1_missing_rows"], 
                $row["sum_pm2_missing_rows"], 
                $row["sum_pm3_missing_rows"], 
                $row["sum_count_origin_rows"]
            );
            $result->setCountOriginRows($row["sum_count_origin_rows"]);
    
            return $result;
    
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return new EnergyDataSet($startTime, $endTime);
        }
    }    


    public function getSavingsData() : SavingsStatisticDictionary
    {
        $savings = new SavingsStatisticDictionary();
        $timPeriods = [TimePeriod::Today, TimePeriod::ThisWeek, TimePeriod::ThisMonth, TimePeriod::ThisYear];
        foreach($timPeriods as $timePeriod) {
            $powerData = $this->getEnergyData($timePeriod->getStartDate(), $timePeriod->getEndDate());
            $statsSet = new SavingsStatisticSet($powerData->getSavings(), $powerData->getEnergyUnderZero(), $powerData->getEnergyOverZero());
            $savings->add($timePeriod->value, $statsSet);
        }

        return $savings;
    }
    
    public function updatePricesForTimeRange($timestampFrom, $timestampTo, $outCentPricePerWh, $inCentPricePerWh)
    {
        $this->error = "";     
        $sql = "UPDATE $this->tableName 
                SET out_cent_price_per_wh = :outCentPricePerWh, in_cent_price_per_wh = :inCentPricePerWh
                WHERE timestamp_from >= :timestampFrom AND timestamp_from <= :timestampTo";
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':outCentPricePerWh', $outCentPricePerWh, PDO::PARAM_STR);
            $stmt->bindParam(':inCentPricePerWh', $inCentPricePerWh, PDO::PARAM_STR);
            $stmt->bindParam(':timestampFrom', $timestampFrom, PDO::PARAM_STR);
            $stmt->bindParam(':timestampTo', $timestampTo, PDO::PARAM_STR);
    
            if (!$stmt->execute()) {
                throw new Exception("Fehler beim Ausführen des Statements: " . implode(", ", $stmt->errorInfo()));
            }
    
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
        
    public function saveCustomData(CustomEnergyValueSet $customValSet)
    {
        $this->error = "";
        $quarterHourInterval = TimeHelper::getQuarterHoursBetween($customValSet->getTimestampFrom(), $customValSet->getTimestampTo());
        $sql = "INSERT INTO $this->tableName 
                (timestamp_from, timestamp_to, value_for_x_quarter_hours, out_cent_price_per_wh, in_cent_price_per_wh,
                em_total_power, em_total_over_zero, em_total_under_zero,
                pm1_total_power, pm2_total_power, pm3_total_power,
                pm1_missing_rows, pm2_missing_rows, pm3_missing_rows,
                count_rows, custom_value)
            VALUES (:timestampFrom, :timestampTo, :quarterHourInterval, :outCentPricePerWh, :inCentPricePerWh,
                    :emTotalPower, :emTotalOverZero, :emTotalUnderZero,
                    :pm1TotalPower, :pm2TotalPower, :pm3TotalPower,
                    :pm1MissingRows, :pm2MissingRows, :pm3MissingRows,
                    :countRows, :customValue)
            ON DUPLICATE KEY UPDATE
                timestamp_to = VALUES(timestampTo),
                value_for_x_quarter_hours = VALUES(value_for_x_quarter_hours),
                out_cent_price_per_wh = VALUES(out_cent_price_per_wh),
                in_cent_price_per_wh = VALUES(in_cent_price_per_wh),
                em_total_power = VALUES(em_total_power),
                em_total_over_zero = VALUES(em_total_over_zero),
                em_total_under_zero = VALUES(em_total_under_zero),
                pm1_total_power = VALUES(pm1_total_power),
                pm2_total_power = VALUES(pm2_total_power),
                pm3_total_power = VALUES(pm3_total_power),
                pm1_missing_rows = VALUES(pm1_missing_rows),
                pm2_missing_rows = VALUES(pm2_missing_rows),
                pm3_missing_rows = VALUES(pm3_missing_rows),
                count_rows = VALUES(count_rows),
                custom_value = VALUES(custom_value)";
    
        try {
            $stmt = $this->pdo->prepare($sql);
    
            $countRows = 1;
            $customValue = 1;
            $pm1MissingRows = $customValSet->getPm1TotalPower() === null ? $countRows : 0;
            $pm2MissingRows = $customValSet->getPm2TotalPower() === null ? $countRows : 0;
            $pm3MissingRows = $customValSet->getPm3TotalPower() === null ? $countRows : 0;
    
            $stmt->bindParam(':timestampFrom', $customValSet->getTimestampFrom(), PDO::PARAM_STR);
            $stmt->bindParam(':timestampTo', $customValSet->getTimestampTo(), PDO::PARAM_STR);
            $stmt->bindParam(':quarterHourInterval', $quarterHourInterval, PDO::PARAM_INT);
            $stmt->bindParam(':outCentPricePerWh', $customValSet->getOutCentPricePerWh(), PDO::PARAM_STR);
            $stmt->bindParam(':inCentPricePerWh', $customValSet->getInCentPricePerWh(), PDO::PARAM_STR);
            $stmt->bindParam(':emTotalPower', $customValSet->getEmTotalPower(), PDO::PARAM_STR);
            $stmt->bindParam(':emTotalOverZero', $customValSet->getEmOverZero(), PDO::PARAM_STR);
            $stmt->bindParam(':emTotalUnderZero', $customValSet->getEmUnderZero(), PDO::PARAM_STR);
            $stmt->bindParam(':pm1TotalPower', $customValSet->getPm1TotalPower(), PDO::PARAM_STR);
            $stmt->bindParam(':pm2TotalPower', $customValSet->getPm2TotalPower(), PDO::PARAM_STR);
            $stmt->bindParam(':pm3TotalPower', $customValSet->getPm3TotalPower(), PDO::PARAM_STR);
            $stmt->bindParam(':pm1MissingRows', $pm1MissingRows, PDO::PARAM_INT);
            $stmt->bindParam(':pm2MissingRows', $pm2MissingRows, PDO::PARAM_INT);
            $stmt->bindParam(':pm3MissingRows', $pm3MissingRows, PDO::PARAM_INT);
            $stmt->bindParam(':countRows', $countRows, PDO::PARAM_INT);
            $stmt->bindParam(':customValue', $customValue, PDO::PARAM_INT);
    
            if (!$stmt->execute()) {
                throw new Exception("Fehler beim Speichern der Daten: " . implode(", ", $stmt->errorInfo()));
            }
    
            return true;
        } catch (Exception $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }
    
    
    public function getCustomRow($timestamp)
    {
        $sql = "SELECT * FROM $this->tableName WHERE timestamp_from = :timestamp AND custom_value = 1 ORDER BY timestamp DESC";        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':timestamp', $timestamp, PDO::PARAM_STR);
            
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                return null;
            }
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $customData = $this->createCustomEnergySet($row);
    
            return $customData;
            
        } catch (PDOException $e) {
            $this->error = "Fehler beim Lesen der Daten: " . $e->getMessage();
            return null;
        }
    }
    
    public function deleteCustomRow($timestampFrom)
    {
        $sql = "DELETE FROM $this->tableName WHERE timestamp_from = :timestampFrom AND custom_value = 1";        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':timestampFrom', $timestampFrom, PDO::PARAM_STR);
            
            $stmt->execute();
            if ($stmt->rowCount() == 0) {
                $this->error = "Keine Daten zum Löschen gefunden.";
                return false;
            }
            
            return true;            
        } catch (PDOException $e) {
            $this->error = "Fehler beim Löschen der Daten: " . $e->getMessage();
            return false;
        }
    }
    
    public function getCustomDataList()
    {
        $sql = "SELECT * FROM $this->tableName WHERE custom_value = 1 ORDER BY timestamp_from DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
    
        if ($stmt->rowCount() == 0) {
            return array();
        }
    
        $customDataList = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $customData = $this->createCustomEnergySet($row);
            $customDataList[] = $customData;
        }
    
        return $customDataList;
    }
    
    private function createCustomEnergySet($row)
    {
        $customData = new CustomEnergyValueSet($row["timestamp_from"], $row["timestamp_to"], $row["out_cent_price_per_wh"], $row["in_cent_price_per_wh"]);
        $customData->setEmTotalPower($row["em_total_power"]);
        $customData->setEmOverZero($row["em_total_over_zero"]);
        $customData->setEmUnderZero($row["em_total_under_zero"]);

        $customData->setPm1TotalPower($row["pm1_total_power"]);
        $customData->setPm2TotalPower($row["pm2_total_power"]);
        $customData->setPm3TotalPower($row["pm3_total_power"]);            

        return $customData;
    }
}
