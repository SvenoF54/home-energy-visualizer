<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->realtimeOverview();
$actualConfig->setFormValues();


// Form values
$startTime = ($actualConfig->getPastperiod() != 0) ? date('Y-m-d H:i:00', time() - (3600 * $actualConfig->getPastperiod())) : StringHelper::formGetDateTime("from-date", 00);
$endTime = ($actualConfig->getPastperiod() != 0) ? date('Y-m-d H:i:59', time() - 2) : StringHelper::formGetDateTime("to-date", 59);

$timeLabelUnit = TimeHelper::prepareTimeUnit($startTime, $endTime);

// prepare DB
$realTimeEnergyDataTbl = RealTimeEnergyDataTable::getInstance();
$hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
$energyPriceTbl = EnergyPriceTable::getInstance();
$priceRow = $energyPriceTbl->getPriceForDateTime($startTime);
$overviewDataRows = $realTimeEnergyDataTbl->getOverviewData($startTime, $endTime, $actualConfig->getAveragePossibility());

$outPricePerWh = $priceRow != null ? $priceRow->getOutCentPricePerWh() : Configuration::getInstance()->getOutCentPricePerWh();
$inPricePerWh = $priceRow != null ? $priceRow->getInCentPricePerWh() : Configuration::getInstance()->getInCentPricePerWh();

$energyData = $realTimeEnergyDataTbl->getEnergyData($startTime, $endTime, $actualConfig->getLine1(), $actualConfig->getLine2(), $outPricePerWh, $inPricePerWh);
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
$jsHeaderFiles = ["js/utils.js", "js/realtime-page/formFunctions.js", "js/realtime-page/configureRealtimeChart.js"];
$jsFooterFiles = ["js/realtime-page/documentReady.js"];
$cssFiles = ["css/realtimePage.css"];
$jsVars = [
    "timestamps" => json_encode($timestamps),
    "emPowerRows" => json_encode($emPowerRows),
    "pm1PowerRows" => json_encode($pm1PowerRows),
    "pm2PowerRows" => json_encode($pm2PowerRows),
    "pm3PowerRows" => json_encode($pm3PowerRows),
    "pmTotalPowerRows" => json_encode($pmTotalPowerRows),
    "timeLabelUnit" => json_encode($timeLabelUnit),
    "refreshIntervalInSec" => $actualConfig->getRefreshIntervalInSec(),
    "config" => $actualConfig->toJson()
];

$partialTop = "views/pages/realtime/filter-for-realtime.phtml";
$partialBottom = "views/partials/chart-canvas.phtml";


include("views/partials/layout.phtml");

?>
