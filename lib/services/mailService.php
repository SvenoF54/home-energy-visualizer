<?php

class MailService {
    public static function sendMail($to, $subject, $message) 
    {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8" . "\r\n";
        $headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";        
        
        mail(
            $to, 
            $subject, 
            $message,
            $headers);

    }
}
