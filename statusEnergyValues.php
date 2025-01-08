<?php
include_once("lib/appLibLoader.php");

// Prepare DB
$db = Database::getInstance();
$hourlyEnergyDataTbl = new HourlyEnergyDataTable($db->getPdoConnection());
$hourlyEnergyGaps = $hourlyEnergyDataTbl->getGaps();
$hourlyEnergyGapsGroupped = TableGapSet::groupByMonth($hourlyEnergyGaps);
$hourlyEnergyStats = $hourlyEnergyDataTbl->getStatistics();

$realtimeEnergyDataTbl = new RealTimeEnergyDataTable($db->getPdoConnection());
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
