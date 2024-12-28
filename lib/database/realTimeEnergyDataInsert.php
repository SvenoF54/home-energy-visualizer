<?php

class RealTimeEnergyDataInsert extends BaseTable {
    const maxRetries = 3;
    
    public function __construct($pdo) {
        parent::__construct($pdo, "real_time_energy_data");
    }


    public function logData($timestamp, $device_type, $interval_in_seconds, $total_act_power): bool
    {
        $this->error = "";
    
        $emPower = null;
        $pm1Power = null;
        $pm2Power = null;
        $pm3Power = null;    
        switch ($device_type) {
            case "EM":
                $emPower = $total_act_power;
                break;
            case "PM1":
                $pm1Power = $total_act_power;
                break;
            case "PM2":
                $pm2Power = $total_act_power;
                break;
            case "PM3":
                $pm3Power = $total_act_power;
                break;
            default:
                $this->error = "Ungültiger Gerätetyp: $device_type";
                return false;
        }
    
        $sql = "
            INSERT INTO {$this->tableName} 
            (timestamp, interval_in_seconds, em_total_power, pm1_total_power, pm2_total_power, pm3_total_power)
            VALUES (:timestamp, :interval_in_seconds, :emPower, :pm1Power, :pm2Power, :pm3Power)
            ON DUPLICATE KEY UPDATE
                em_total_power = IFNULL(VALUES(em_total_power), em_total_power),
                pm1_total_power = IFNULL(VALUES(pm1_total_power), pm1_total_power),
                pm2_total_power = IFNULL(VALUES(pm2_total_power), pm2_total_power),
                pm3_total_power = IFNULL(VALUES(pm3_total_power), pm3_total_power);
        ";
    
        return $this->saveDataWithRetry($sql, $timestamp, $interval_in_seconds, $emPower, $pm1Power, $pm2Power, $pm3Power);
    }
    
    private function saveDataWithRetry($sql, $timestamp, $interval_in_seconds, 
                                       $emPower, $pm1Power, $pm2Power, $pm3Power) 
    {
        $maxRetries = 5;
        $attempt = 0;

        while ($attempt < $maxRetries) {
            try {
                $stmt = $this->pdo->prepare($sql);

                $stmt->bindValue(':timestamp', $timestamp, PDO::PARAM_STR);
                $stmt->bindValue(':interval_in_seconds', $interval_in_seconds, PDO::PARAM_INT);
                $stmt->bindValue(':emPower', $emPower, PDO::PARAM_STR);
                $stmt->bindValue(':pm1Power', $pm1Power, PDO::PARAM_STR);
                $stmt->bindValue(':pm2Power', $pm2Power, PDO::PARAM_STR);
                $stmt->bindValue(':pm3Power', $pm3Power, PDO::PARAM_STR);

                $stmt->execute();
                return true; // Erfolg, kein Retry erforderlich
            } catch (PDOException $e) {
                $attempt++;
                $errorCode = $e->getCode();

                if ($errorCode == '40001' || $errorCode == 'HY000') { // Deadlock or Timeout
                    if ($attempt >= $maxRetries) {
                        $this->error = "Fehler nach $maxRetries Versuchen: " . $e->getMessage();
                        return false;
                    }
                    usleep(200000); // 100 ms
                } else {
                    // Not retryable error
                    $this->error = "Datenbankfehler: " . $e->getMessage();
                    return false;
                }
            }
        }

        $this->error = "Fehler nach $maxRetries Versuchen.";
        return false;
    }
    
}   
