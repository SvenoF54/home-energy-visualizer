<?php
include_once("lib/appLibLoader.php");

// Prepare DB
$db = Database::getInstance();
$kvsTable = new KeyValueStoreTable($db->getPdoConnection());
$kvsRows = $kvsTable->getAllRows();
$realtimeEnergyDataTbl = new RealTimeEnergyDataTable($db->getPdoConnection());
$realtimeEnergyStats = $realtimeEnergyDataTbl->getStatistics();


// configure VIEW

$pageTitle = "Status Übersicht";
$jsHeaderFiles = ["/js/utils.js"];
$jsFooterFiles = [];
$cssFiles = [];

$partialTop = null;
$partialBottom = "views/pages/status-overview/values-list.phtml";


include("views/partials/layout.phtml");

?>
