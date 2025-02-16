<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class MailService {
    public static function sendMailAfterDelay(MailEnum $key, $delayInMinutes, $to, $subject, $message) 
    {
        $db = Database::getInstance();
        $kvsTable = new KeyValueStoreTable($db->getPdoConnection());
        $kvsRow = $kvsTable->getRow(KeyValueStoreScopeEnum::SendMail, $key->value);
        $lastUpdated = $kvsRow->getInsertedOrUpdated();
        
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
            
            self::logToKvs($key, StatusEnum::Success, "Send to: ".$to);
        } catch (Exception $ex) {
            self::logToKvs($key, StatusEnum::Exception, $ex->getMessage());            
        }
    }

    private static function logToKvs(MailEnum $key, StatusEnum $status, $notice = "")
    {
        $db = Database::getInstance();
        $kvsTable = new KeyValueStoreTable($db->getPdoConnection());

        $kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::SendMail, $key->value, $status->value, $notice);
    }

}
