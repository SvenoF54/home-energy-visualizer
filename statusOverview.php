<?php
include_once("lib/appLibLoader.php");

// Prepare DB
$kvsTable = KeyValueStoreTable::getInstance();
$kvsRowsPerScope = [];
foreach (KeyValueStoreScopeEnum::cases() as $scope) {
    $kvsRowsPerScope[$scope->value] = $kvsTable->getRowsForScope($scope);
}
$realtimeEnergyDataTbl = RealTimeEnergyDataTable::getInstance();
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
