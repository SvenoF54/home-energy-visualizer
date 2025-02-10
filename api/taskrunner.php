<?php
include_once("lib/appLibLoader.php");

ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");

$currentMinute = (int) date("i");
$roundedMinute = $currentMinute - ($currentMinute % 5);  // Round to next 5-Minute-Intervall (0, 5, 10, 15, ...)

TaskService::checkRealtimeEnergyData();

if ($currentMinute % 5 === 0) {
    // Every 5 minutes
    TaskService::unifyRealTimeData();
    TaskService::checkRealtimeEnergyData();
    //ApiHelper::dieWithResponseCode(200, $resultMsg);
}

if ($currentMinute % 10 === 0) {
    // Every 10 minutes
    //ApiHelper::dieWithResponseCode(200, $resultMsg);
}

if ($currentMinute % 30 === 0) {
}
