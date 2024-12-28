<?php
include_once("config/config.php");
include_once("../lib/database/database.php");
include_once("../lib/database/baseTable.php");
include_once("../lib/database/baseTimestampTable.php");
include_once("../lib/database/realTimeEnergyDataTable.php");
include_once("../lib/database/realTimeEnergyDataRow.php");
include_once("../lib/database/hourlyEnergyDataInsert.php");
include_once("../lib/database/energyPriceRow.php");
include_once("../lib/database/energyPriceTable.php");
include_once("../lib/database/realTimeEnergyDataUnifier.php");
include_once("../lib/utils/apiHelper.php");

ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");

switch ($_SERVER['REQUEST_METHOD']) {
    case "POST":
    case "GET":        
        $monthYear = isset($_REQUEST["monthYear"]) ? $_REQUEST["monthYear"] : null;
        unifyRealTimeData($monthYear);
        break;
    default:
        ApiHelper::dieWithResponseCode(500, "Method not supported");
}
return;

function unifyRealTimeData($monthYear)
{    
    if (isset($monthYear)) {
        $startTime = new DateTime($monthYear . '-01 00:00:00');
        $endTime = clone $startTime;
        $endTime->modify('last day of ' . $monthYear . ' 23:59:59');
    } else {
        $startTime = new DateTime();
        $startTime->modify('-1 day');
        $endTime = new DateTime();    
    }

    $db = new Database();
    $realTimeEnergyDataTbl = new RealTimeEnergyDataTable($db->getPdoConnection());
    $hourlyEnergyDataTbl = new HourlyEnergyDataInsert($db->getPdoConnection());
    $energyPriceTbl = new EnergyPriceTable($db->getPdoConnection());

    $unifier = new RealTimeEnergyDataUnifier($hourlyEnergyDataTbl, $realTimeEnergyDataTbl, $energyPriceTbl, Config::getInstance()->getOutCentPricePerWh(), Config::getInstance()->getInCentPricePerWh());
    $count = $unifier->unifyDataForTimeRange($startTime, $endTime);

    ApiHelper::dieWithResponseCode(200, "Data saved successfully. $count Rows changed or added for timerange ".$startTime->format('d.m.Y H:i:s')." to ".$endTime->format('d.m.Y H:i:s').".");

}

?>
