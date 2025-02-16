<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

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
