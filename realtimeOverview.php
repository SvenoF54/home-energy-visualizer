<?php
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->realtimeOverview();

// Form values
$avg = StringHelper::formGetInt('average', 2);
$hours = StringHelper::formGetFloat('hours', 1);
$startTime = ($hours != 0) ? date('Y-m-d H:i:00', time() - (3600 * $hours)) : StringHelper::formGetDateTime("from-date", 00);
$endTime = ($hours != 0) ? date('Y-m-d H:i:59', time() - 2) : StringHelper::formGetDateTime("to-date", 59);

$line1 = StringHelper::formGetInt("line1", $actualConfig->getLine1Default());
$line2 = StringHelper::formGetInt("line2", $actualConfig->getLine2Default());
$timeLabelUnit = TimeHelper::prepareTimeUnit($startTime, $endTime);

// prepare DB
$db = Database::getInstance();
$realTimeEnergyDataTbl = new RealTimeEnergyDataTable($db->getPdoConnection());
$hourlyEnergyDataTbl = new HourlyEnergyDataTable($db->getPdoConnection());
$energyPriceTbl = new EnergyPriceTable($db->getPdoConnection());
$priceRow = $energyPriceTbl->getPriceForDateTime($startTime);
$overviewDataRows = $realTimeEnergyDataTbl->getOverviewData($startTime, $endTime, $avg);

$outPricePerWh = $priceRow != null ? $priceRow->getOutCentPricePerWh() : Configuration::getInstance()->getOutCentPricePerWh();
$inPricePerWh = $priceRow != null ? $priceRow->getInCentPricePerWh() : Configuration::getInstance()->getInCentPricePerWh();

$energyData = $realTimeEnergyDataTbl->getEnergyData($startTime, $endTime, $line1, $line2, $outPricePerWh, $inPricePerWh);
$savings = $hourlyEnergyDataTbl->getSavingsData();

$errorMsg = "";

$timestamps = [];
$emPowerRows = [];
$pm1PowerRows = [];
$pm2PowerRows = [];
$pm3PowerRows = [];
$pmTotalPowerRows = [];
if (sizeof($overviewDataRows) > 0) {
    foreach($overviewDataRows as $row) {
        $timestamps[] = $row->getTimestampData();        
        $emPowerRows[] = StringHelper::formatNumberOrNull($row->getEmTotalPower());
        $pm1PowerRows[] = StringHelper::formatNumberOrNull($row->getPm1TotalPower());
        $pm2PowerRows[] = StringHelper::formatNumberOrNull($row->getPm2TotalPower());
        $pm3PowerRows[] = StringHelper::formatNumberOrNull($row->getPm3TotalPower());
        $pmTotalPowerRows[] = StringHelper::formatNumberOrNull($row->getPmTotalPower());
    }
} else {
    $errorMsg = "Keine Daten gefunden.";
}

// configure VIEW

$pageTitle = "Echtzeitdaten";
$jsHeaderFiles = ["/js/utils.js", "js/realtime-page/formFunctions.js", "js/realtime-page/configureEnergyChart.js"];
$jsFooterFiles = ["/js/realtime-page/documentReady.js"];
$cssFiles = ["/css/realtimePage.css"];
$jsVars = [
    "timestamps" => json_encode($timestamps),
    "emPowerRows" => json_encode($emPowerRows),
    "pm1PowerRows" => json_encode($pm1PowerRows),
    "pm2PowerRows" => json_encode($pm2PowerRows),
    "pm3PowerRows" => json_encode($pm3PowerRows),
    "pmTotalPowerRows" => json_encode($pmTotalPowerRows),
    "line1_selected" => $line1,
    "line2_selected" => $line2,
    "timeLabelUnit" => json_encode($timeLabelUnit),
    "refreshIntervalInSec" => $actualConfig->getRefreshIntervalInSec()
];

$partialTop = "views/pages/realtime/filter.phtml";
$partialBottom = "views/partials/chart-canvas.phtml";


include("views/partials/layout.phtml");

?>
