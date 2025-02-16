<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->monthsOverview();

$startTime1 = date("Y-m-d 00:00:00");
$endTime1 = date("Y-m-d 23:59:59");
$timeLabelUnit = TimeHelper::prepareTimeUnit($startTime1, $endTime1);

// Prepare DB
$errorMsg = "";
$overviewPageService = new OverviewPageService();
$overviewPageService->calculateHourData($startTime1, $endTime1);


// configure VIEW

$pageTitle = "Dashboard";
$jsHeaderFiles = ["/js/utils.js", "js/dashboard/configureEnergyChart.js"];
$jsFooterFiles = ["/js/dashboard/documentReady.js"];
$cssFiles = ["/css/dasboardPage.css"];
$jsVars = [
    "timestampsTooltip" => json_encode($overviewPageService->getLabelsTooltip()),
    "timestampsXAxis" => json_encode($overviewPageService->getLabelsXAxis()),
    "data1" => json_encode($overviewPageService->getData1List()->convertToJsChartArray()),
    "timeLabelUnit" => json_encode($timeLabelUnit),
    "config" => $actualConfig->toJson()
];


include("views/partials/layout.phtml");
