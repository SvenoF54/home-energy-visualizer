<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Prepare DB
$kvsTable = KeyValueStoreTable::getInstance();
$kvsRowsPerScope = [];
foreach (KeyValueStoreScopeEnum::cases() as $scope) {
    $kvsRowsPerScope[$scope->value] = $kvsTable->getRowsForScope($scope);
}


// configure VIEW

$pageTitle = "Status Übersicht";
$jsHeaderFiles = ["/js/utils.js"];
$jsFooterFiles = [];
$cssFiles = [];

$partialTop = null;
$partialBottom = "views/pages/status-overview/values-list.phtml";


include("views/partials/layout.phtml");

?>
