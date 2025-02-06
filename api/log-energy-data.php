<?php
include_once("config/config.php");
include_once("../lib/database/database.php");
include_once("../lib/database/baseTable.php");
include_once("../lib/database/realTimeEnergyDataInsert.php");
include_once("../lib/utils/apiHelper.php");

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
    $device_type = filter_var($data['device_type'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $interval_in_seconds = (int) $data['interval_in_seconds'] ?? null;
    $a_act_power = (float) $data['a_act_power'] ?? null;
    $b_act_power = (float) $data['b_act_power'] ?? null;
    $c_act_power = (float) $data['c_act_power'] ?? null;
    $total_act_power = (float) $data['total_act_power'] ?? null;

    $db = Database::getInstance();
    $table = new RealTimeEnergyDataInsert($db->getPdoConnection());
    if (! $table->logData($timestamp, $device_type, $interval_in_seconds, $total_act_power)) {
        ApiHelper::dieWithResponseCode(500, "Error saving data: " . $table->getError());
    }

    ApiHelper::dieWithResponseCode(200, "Successfully saved. Timestamp: ".date("d.m.Y H:i:s", strtotime($timestamp)).", Device: ".$device_type.", Energy: ".$total_act_power);
}

function checkLogdataStatus()
{

}

?>
