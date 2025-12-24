<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");

$currentMinute = (int) date("i");
$roundedMinute = $currentMinute - ($currentMinute % 5);  // Round to next 5-Minute-Intervall (0, 5, 10, 15, ...)

TaskService::checkRealtimeEnergyData();

if ($currentMinute % 1 === 0) {
    // Every minute
}

if ($currentMinute % 5 === 0) {
    // Every 5 minutes
    TaskService::unifyRealTimeData();
    //ApiHelper::dieWithResponseCode(200, $resultMsg);
}

if ($currentMinute % 10 === 0) {
    // Every 10 minutes
    //ApiHelper::dieWithResponseCode(200, $resultMsg);
}

if ($currentMinute % 30 === 0) {
}

ApiHelper::dieWithResponseCode(200, "Taskrunner finished.");
