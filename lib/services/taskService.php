<?php

class TaskService {    
    public static function checkRealtimeEnergyData()
    {
        try {
            $db = Database::getInstance();
            $realTimeEnergyDataTbl = new RealTimeEnergyDataTable($db->getPdoConnection());
            $actualConfig = Configuration::getInstance()->configRealtimeAlert();
            $latestLogData = $realTimeEnergyDataTbl->getLatestLogData();        

            $hasAlerts = false;
            $message = "Von folgenden Geräten wurden in den letzten {$actualConfig->getAlertThresholdInMinutes()} Minuten keine Daten empfangen: \n\n";
            $missingEnergyTypes = array();
            foreach (EnergyTypeEnum::cases() as $energyType) {
                if (! $actualConfig->shouldAlertForEnergyType($energyType)) {
                    continue;
                }
                
                $hasData = $latestLogData->hasEnergyData($energyType, $actualConfig->getAlertThresholdInMinutes());
                if (! $hasData) {
                    $hasAlerts = true;
                    $message .= "- ".$energyType->value . " hat keine aktuellen Daten.\n";
                    $missingEnergyTypes[] = $energyType->value;
                }
            }

            if ($hasAlerts && $actualConfig->getSendAlertMail()) {
                $subject = "Datenverlust von: ".implode(", ", $missingEnergyTypes);
                MailService::sendMailAfterDelay(MailEnum::SendRealtimeEnergyDataLoss, $actualConfig->getSendAlertMailEveryXMinutes(), SYSTEM_EMAIL, $subject, $message);
                self::logToKvs(TaskEnum::CheckRealtimeEnergyData, "Data loss found", $subject);
            } else {
                self::logToKvs(TaskEnum::CheckRealtimeEnergyData, "No data loss");
            }
        } catch(Exception $ex) {
            self::logToKvs(TaskEnum::CheckRealtimeEnergyData, "Failure", $ex->getMessage());
        }
    }

    public static function unifyRealTimeData($monthYear = null)
    {    
        try {
            if (isset($monthYear)) {
                $startTime = new DateTime($monthYear . '-01 00:00:00');
                $endTime = clone $startTime;
                $endTime->modify('last day of ' . $monthYear . ' 23:59:59');
            } else {
                $startTime = new DateTime();
                $startTime->modify('-1 day');
                $endTime = new DateTime();    
            }
        
            $db = Database::getInstance();
            $realTimeEnergyDataTbl = new RealTimeEnergyDataTable($db->getPdoConnection());
            $hourlyEnergyDataTbl = new HourlyEnergyDataInsert($db->getPdoConnection());
            $energyPriceTbl = new EnergyPriceTable($db->getPdoConnection());
        
            $unifier = new RealTimeEnergyDataUnifier($hourlyEnergyDataTbl, $realTimeEnergyDataTbl, $energyPriceTbl, Configuration::getInstance()->getOutCentPricePerWh(), Configuration::getInstance()->getInCentPricePerWh());
            $count = $unifier->unifyDataForTimeRange($startTime, $endTime);
        
            $resultMsg = "Data saved successfully. $count Rows changed or added for timerange ".$startTime->format('d.m.Y H:i:s')." to ".$endTime->format('d.m.Y H:i:s').".";
            self::logToKvs(TaskEnum::UnifyRealtimeEnergyData, "Success", $resultMsg);
            return $resultMsg;        
        } catch(Exception $ex) {
            self::logToKvs(TaskEnum::UnifyRealtimeEnergyData, "Failure", $ex->getMessage());
        }

    }
    
    private static function logToKvs(TaskEnum $key, $value, $notice = "")
    {
        $db = Database::getInstance();
        $kvsTable = new KeyValueStoreTable($db->getPdoConnection());

        $kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Task, $key->value, $value, $notice);
    }
}
