<?php

class ApiHelper {
    public static function assertApiKeyIsCorrect($apiKeyToCheck)
    {
        $apiKey = isset($apiKeyToCheck) ? $apiKeyToCheck : null;
    
        if ($apiKey == API_KEY) return;
    
        self::dieWithResponseCode(403, 'Forbidden');
    }

    public static function dieWithResponseCode($code, $msg)
    {
        http_response_code($code);
        die($msg);
    }
        
}
?>
