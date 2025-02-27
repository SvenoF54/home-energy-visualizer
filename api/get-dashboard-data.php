<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

#ApiHelper::assertApiKeyIsCorrect(isset($_REQUEST["apikey"]) ? $_REQUEST["apikey"] : "");

$actualConfig = Configuration::getInstance()->monthsOverview();

$dashboardService = new DashboardService();
$dashboardService->prepareInstantData();
$jsonData = $dashboardService->getInstantDataAsJson();

ApiHelper::dieWithJsonResponse($jsonData);
