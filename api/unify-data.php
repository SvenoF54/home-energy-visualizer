<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");

switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
    case "GET":        
        $monthYear = isset($_REQUEST["monthYear"]) ? $_REQUEST["monthYear"] : null;

        $resultMsg = TaskService::unifyRealTimeData($monthYear);
        ApiHelper::dieWithResponseCode(200, $resultMsg);
        break;
    default:
        ApiHelper::dieWithResponseCode(500, "Method not supported");
}

?>
