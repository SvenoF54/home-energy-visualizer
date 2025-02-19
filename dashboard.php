<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->monthsOverview();


$errorMsg = "";


// configure VIEW

$pageTitle = "Dashboard";
$jsHeaderFiles = ["/js/utils.js"];
$jsFooterFiles = ["/js/dashboard/documentReady.js"];
$cssFiles = ["/css/dashboardPage.css"]; 
$jsVars = [    
    "config" => $actualConfig->toJson()
];

$partialTop = "views/pages/dashboard/top-area.phtml";

include("views/partials/layout.phtml");
