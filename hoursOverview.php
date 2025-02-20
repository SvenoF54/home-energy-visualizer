<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->hoursOverview();
$actualConfig->setFormValues();

$selectedDay1 = StringHelper::formGetDate("day1", strtotime(date("d.m.Y")));
$selectedDay2 = StringHelper::formGetDate("day2", strtotime(date("d.m.Y", strtotime('-1 day')))); 

$startTime1 = date("Y-m-d 00:00:00", strtotime($selectedDay1));
$endTime1 = date("Y-m-d 23:59:59", strtotime($selectedDay1));
$startTime2 = date("Y-m-d 00:00:00", strtotime($selectedDay2));
$endTime2 = date("Y-m-d 23:59:59", strtotime($selectedDay2));
$timeLabelUnit = TimeHelper::prepareTimeUnit($startTime1, $endTime1);

// Prepare DB
$errorMsg = "";
$overviewPageService = new OverviewPageService();
$overviewPageService->calculateHourData($startTime1, $endTime1, $startTime2, $endTime2);

// configure VIEW

    $pageTitle = "Stundenübersicht";
    $jsHeaderFiles = ["/js/utils.js", "js/overview-pages/configureEnergyChart.js", "js/overview-pages/configureAutarkyChart.js", 
                      "js/overview-pages/formFunctionsForHoursOverview.js"];
    $jsFooterFiles = ["/js/overview-pages/documentReady.js", "/js/overview-pages/configureDataTable.js"];
    $cssFiles = ["/css/overviewPage.css"];
    $jsVars = [
        "timestampsTooltip" => json_encode($overviewPageService->getLabelsTooltip()),
        "timestampsXAxis" => json_encode($overviewPageService->getLabelsXAxis()),
        "data1" => json_encode($overviewPageService->getData1List()->convertEnergyToJsArray()),
        "data2" => json_encode($overviewPageService->getData2List()->convertEnergyToJsArray()),
        "autarky1" => json_encode($overviewPageService->getData1List()->convertAutarkyToJsArray()),
        "autarky2" => json_encode($overviewPageService->getData2List()->convertAutarkyToJsArray()),
        "timeLabelUnit" => json_encode($timeLabelUnit),
        "config" => $actualConfig->toJson()
    ];

    // Filter settings
    $tableMainCaptionTimeUnit = "Tag";
    $tableRow1CaptionTimeUnit = TimeHelper::formatDate($selectedDay1);
    $tableRow2CaptionTimeUnit = TimeHelper::formatDate($selectedDay2);
    $energyTableCaption = "Energiewerte für ".TimeHelper::getWeekday($selectedDay1).", ".TimeHelper::formatDate($startTime1);    

    $partialTop = "views/pages/overview/filter-for-hours-overview.phtml";
    $partialBottom = "views/partials/chart-and-table-canvas.phtml";


    include("views/partials/layout.phtml");

?>
