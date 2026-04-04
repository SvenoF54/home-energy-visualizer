<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");


switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
        // read POST as JSON
        $input = file_get_contents("php://input");
        $data = json_decode($input, true);
        writeDataToTable($data);
        break;
    default:
        ApiHelper::dieWithResponseCode(500, "Method not supported");
}
return;

function writeDataToTable($data) {
    $timestamp = filter_var($data['timestamp'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $zendure = new ZendureService();
    
    if (! $zendure->parseAndSaveData($data["zendureData"])) {
        ApiHelper::dieWithResponseCode(500, "Error saving Zendure-Data: " . $zendure->getError());
    }

    ApiHelper::dieWithResponseCode(200, "Successfully saved Zendure-Data. Timestamp: ".date("d.m.Y H:i:s", strtotime($timestamp)));
}
