<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Prepare DB
$hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
$hourlyEnergyGaps = $hourlyEnergyDataTbl->getGaps();
$hourlyEnergyGapsGroupped = TableGapSet::groupByMonth($hourlyEnergyGaps);
$hourlyEnergyStats = $hourlyEnergyDataTbl->getStatistics();

$realtimeEnergyDataTbl = RealTimeEnergyDataTable::getInstance();
$realtimeEnergyStats = $realtimeEnergyDataTbl->getStatistics();

// configure VIEW

$pageTitle = "Status Übersichtsseiten";
$jsHeaderFiles = ["/js/utils.js", "/js/status-energy-values/documentReady.js"];
$jsFooterFiles = [];
$cssFiles = [];
$jsVars = [
    "API_KEY" => json_encode(API_KEY)
];

$partialTop = null;
$partialBottom = "views/pages/status-energy-values/values-list.phtml";


include("views/partials/layout.phtml");

?>
