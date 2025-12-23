<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("config/config.php");
include_once("../lib/database/database.php");
include_once("../lib/database/baseTable.php");
include_once("../lib/database/keyValueStoreTable.php");
include_once("../lib/utils/apiHelper.php");
include_once("../lib/utils/stringHelper.php");
include_once("../lib/services/shellyDeviceService.php");

#ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");

switch ($_REQUEST['device']) {
    case "shelly_uni":
        // Shelly-Uni
        // http://example.com/api/log-onetime-data.php?device=shelly_uni&name=pool
        // {"device":"shelly_uni","name":"pool","temp":"12.38","id":"shellyuni-C45BBEE20EB5","sensor_addr":"28dede43d46130fa"}  
        $data = ["device" => "shelly_uni"];
        $data["id"] = StringHelper::formGetString("id");        
        $data["adc"] = StringHelper::formGetFloat("adc");
        $data["temperature"] = StringHelper::formGetFloat("temp");
        //writeDataToTable($data);
        writeDataToTable($_REQUEST);
        break;
    default:
        ApiHelper::dieWithResponseCode(500, "Method not supported");
}
return;

// TODOS: anderer ScriptName, ein Eintrag pro id

function writeDataToTable(array $data) {
    $decodedData = json_encode($data);      
    $kvsTable = KeyValueStoreTable::getInstance();
    if (! isset($data["id"])) {
        return;             
    }
    $kvsTable->insertOrUpdate(KeyValueStoreScopeEnum::Shelly, $data["id"], StatusEnum::Success->value, $decodedData, $decodedData);

    ApiHelper::dieWithResponseCode(200, "Successfully saved. Timestamp: ".date("d.m.Y H:i:s", strtotime($data["timestamp"])).", Device: ".$data["device"]);
}


?>
