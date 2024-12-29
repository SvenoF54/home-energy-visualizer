<?php

class RealTimeEnergyDataTable extends BaseTimestampTable {
    public const SECONDS_PER_ROW = 2;

    public function __construct($pdo) {
        parent::__construct($pdo, "real_time_energy_data", "timestamp");
    }


    public function getOverviewData($startTime, $endTime, $avg=2) : array 
    {        
        $timezone = new DateTimeZone("Europe/Berlin");
        $date = new DateTime("now", $timezone);        
        $offsetInSeconds = $timezone->getOffset($date);        
        //$offsetHours = floor($offsetInSeconds / 3600);
        $offsetHours = 0;

        // Calcualtes the AVG, maybe MIN + MAX make seens sometimes
        $sql = "
            SELECT 
                DATE_FORMAT(
                    CONVERT_TZ(
                        FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(timestamp) / $avg) * $avg),
                        '+00:00', -- UTC (0 Stunden Offset)
                        '+0$offsetHours:00' -- Offset in Stunden
                    ), 
                '%Y-%m-%d %H:%i:%s') AS timestamp_avg,

                AVG(interval_in_seconds) AS interval_in_seconds_avg,

                ROUND(AVG(em_total_power), 2) AS em_total_power_avg,

                ROUND(AVG(IF(em_total_power > 0, em_total_power, NULL)), 2) AS em_total_power_over_zero_avg,
                ROUND(AVG(IF(em_total_power < 0, em_total_power, NULL)), 2) AS em_total_power_under_zero_avg,

                ROUND(AVG(pm1_total_power), 2) AS pm1_total_power_avg,
                ROUND(AVG(pm2_total_power), 2) AS pm2_total_power_avg,
                ROUND(AVG(pm3_total_power), 2) AS pm3_total_power_avg,

                ROUND(AVG(
                    
                    COALESCE(pm1_total_power, 0) 
                    + COALESCE(pm2_total_power, 0) 
                    + COALESCE(pm3_total_power, 0) 
                    - IF (em_total_power > 0, COALESCE(em_total_power, 0), 0)
                ), 2) AS sum_savings,

                -- Count missing values
                SUM(IF(em_total_power IS NULL, 1, 0)) AS em_missing_rows,
                SUM(IF(pm1_total_power IS NULL, 1, 0)) AS pm1_missing_rows,
                SUM(IF(pm2_total_power IS NULL, 1, 0)) AS pm2_missing_rows,
                SUM(IF(pm3_total_power IS NULL, 1, 0)) AS pm3_missing_rows,

                COUNT(*) AS count_rows        
            FROM 
                $this->tableName
            WHERE
                timestamp >= :startTime
                AND timestamp <= :endTime
            GROUP BY 
                timestamp_avg
            ORDER BY 
                timestamp_avg;        
        ";

        $resultRows = [];    
        try {
            $stmt = $this->pdo->prepare($sql);    
            $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
            if (!$stmt->execute()) {
                throw new Exception("Fehler bei der Ausführung der Abfrage: " . implode(", ", $stmt->errorInfo()));
            }
    
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row["timestamp_avg"] === null) continue;
    
                $resultRows[] = new RealTimeEnergyDataRow(
                    $row["timestamp_avg"], 
                    $row["interval_in_seconds_avg"],
                    $row["em_total_power_avg"],
                    $row["em_total_power_over_zero_avg"],
                    $row["em_total_power_under_zero_avg"],
                    $row["pm1_total_power_avg"],
                    $row["pm2_total_power_avg"],
                    $row["pm3_total_power_avg"],
                    $row["em_missing_rows"],
                    $row["pm1_missing_rows"],
                    $row["pm2_missing_rows"],
                    $row["pm3_missing_rows"],
                    $row["count_rows"]
                );
            }
    
            return $resultRows;
        } catch (PDOException $e) {
            $this->error = "PDOException: " . $e->getMessage();
            var_dump($this->error); die;
            return $resultRows;
        }    
        return $resultRows;
    }

    public function getEnergyData($startTime, $endTime, $line1, $line2, $outPricePerWh, $inPricePerWh): EnergyDataSet
    {
        $factor = self::SECONDS_PER_ROW / 3600;
        $sql = "
            SELECT 
                ROUND(SUM(CASE 
                        WHEN em_total_power >= 0 THEN em_total_power
                        ELSE 0 
                    END) * :factor, 0) AS sum_em_over_0,
                ROUND(SUM(CASE 
                        WHEN em_total_power <= 0 THEN em_total_power
                        ELSE 0 
                    END) * :factor, 0) AS sum_em_under_0,
    
                ROUND(SUM(CASE 
                        WHEN em_total_power >= :line1 THEN em_total_power - :line1
                        ELSE 0 
                    END) * :factor, 0) AS sum_em_over_x1,
                ROUND(SUM(CASE 
                        WHEN em_total_power <= :line1 THEN em_total_power - :line1
                        ELSE 0 
                    END) * :factor, 0) AS sum_em_under_x1,
    
                ROUND(SUM(CASE 
                        WHEN em_total_power >= :line2 THEN em_total_power - :line2
                        ELSE 0 
                    END) * :factor, 0) AS sum_em_over_x2,
                ROUND(SUM(CASE 
                        WHEN em_total_power <= :line2 THEN em_total_power - :line2
                        ELSE 0 
                    END) * :factor, 0) AS sum_em_under_x2,
    
                ROUND(
                    SUM(
                        COALESCE(pm1_total_power, 0) 
                        + COALESCE(pm2_total_power, 0) 
                        + COALESCE(pm3_total_power, 0) 
                        + IF(em_total_power < 0, COALESCE(em_total_power, 0), 0)
                    ) * :factor,
                    2
                ) AS sum_savings,
    
                -- Fehlende Werte zählen
                SUM(IF(em_total_power IS NULL, 1, 0)) AS em_missing_rows,
                SUM(IF(pm1_total_power IS NULL, 1, 0)) AS pm1_missing_rows,
                SUM(IF(pm2_total_power IS NULL, 1, 0)) AS pm2_missing_rows,
                SUM(IF(pm3_total_power IS NULL, 1, 0)) AS pm3_missing_rows,
                COUNT(*) AS count_rows
    
            FROM 
                {$this->tableName}
            WHERE                
                timestamp >= :startTime
                AND timestamp <= :endTime
        ";
    
        $energyDataSet = new EnergyDataSet($startTime, $endTime);
     
        try {
            $stmt = $this->pdo->prepare($sql);
    
            $stmt->bindValue(':factor', $factor, PDO::PARAM_STR);
            $stmt->bindValue(':line1', $line1, PDO::PARAM_STR);
            $stmt->bindValue(':line2', $line2, PDO::PARAM_STR);
            $stmt->bindValue(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindValue(':endTime', $endTime, PDO::PARAM_STR);
    
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($row) {    
                $energyDataSet->setEnergyOverZero(round($row["sum_em_over_0"], 0), $row["sum_em_over_0"] * $outPricePerWh);
                $energyDataSet->setEnergyUnderZero(round($row["sum_em_under_0"], 0), $row["sum_em_under_0"] * $inPricePerWh);
                $energyDataSet->setEnergyUnderX1(round($row["sum_em_under_x1"], 0), $row["sum_em_under_x1"] * $inPricePerWh);
                $energyDataSet->setEnergyOverX1(round($row["sum_em_over_x1"], 0), $row["sum_em_over_x1"] * $outPricePerWh);
                $energyDataSet->setEnergyUnderX2(round($row["sum_em_under_x2"], 0), $row["sum_em_under_x2"] * $inPricePerWh);
                $energyDataSet->setEnergyOverX2(round($row["sum_em_over_x2"], 0), $row["sum_em_over_x2"] * $outPricePerWh);
                $energyDataSet->setSavings(round($row["sum_savings"], 0), $row["sum_savings"] * $outPricePerWh);
                $energyDataSet->setMissingRows($row["em_missing_rows"], $row["pm1_missing_rows"], $row["pm2_missing_rows"], $row["pm3_missing_rows"], $row["count_rows"]);
            }
        } catch (PDOException $e) {
            $this->error = "PDOException: " . $e->getMessage();
        }
    
        return $energyDataSet;
    }    
}