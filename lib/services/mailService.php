<?php

class MailService {
    public static function sendMailAfterDelay(MailEnum $key, $delayInMinutes, $to, $subject, $message) 
    {
        $db = Database::getInstance();
        $kvsTable = new KeyValueStoreTable($db->getPdoConnection());
        $lastUpdated = $kvsTable->getLastUpdatedTime(KeyValueStoreScopeEnum::SendMail, $key->value);
        
        $canSendAgain = ($lastUpdated !== null) && (strtotime($lastUpdated) <= strtotime("-{$delayInMinutes} minutes"));

        if (!$canSendAgain) {
            return;
        }

        self::sendMail($key, $to, $subject, $message);
    }

    public static function sendMail(MailEnum $key, $to, $subject, $message) 
    {
        try {
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";
            $headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";        
            
            mail($to, $subject, $message,$headers);
            
            self::logToKvs($key, "Success", $to);                
        } catch (Exception $ex) {
            self::logToKvs($key, "Failure", $ex->getMessage());            
        }
    }

    private static function logToKvs(MailEnum $key, $value, $notice = "")
    {
        $db = Database::getInstance();
        $kvsTable = new KeyValueStoreTable($db->getPdoConnection());

        $kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::SendMail, $key->value, $value, $notice);
    }

}
