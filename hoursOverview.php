<?php
include_once("config/config.php");
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Config::getInstance()->hoursOverview();

// Form values
$line1 = StringHelper::formGetInt("line1", $actualConfig->getLine1Default());
$line2 = StringHelper::formGetInt("line2", $actualConfig->getLine2Default());

$selectedDay1 = StringHelper::formGetDate("day1", strtotime(date("d.m.Y")));
$selectedDay2 = StringHelper::formGetDate("day2", strtotime(date("d.m.Y", strtotime('-1 day')))); 

$startTime1 = date("Y-m-d", strtotime($selectedDay1))." 00:00:00";
$endTime1 = date("Y-m-d", strtotime($selectedDay1))."  23:59:59";
$startTime2 = date("Y-m-d", strtotime($selectedDay2))." 00:00:00";
$endTime2 = date("Y-m-d", strtotime($selectedDay2))."  23:59:59";
$timeLabelUnit = TimeHelper::prepareTimeUnit($startTime1, $endTime1);


// Prepare DB
$errorMsg = "";
$db = new Database();
$overviewPageService = new OverviewPageService($db->getPdoConnection());
$overviewPageService->prepareHourData($startTime1, $endTime1, $startTime2, $endTime2);

// configure VIEW

    $pageTitle = "Stundenübersicht";
    $jsHeaderFiles = ["/js/utils.js", "js/overview-pages/configureChart.js", "js/overview-pages/formFunctions-daysSelection.js"];
    $jsFooterFiles = ["/js/overview-pages/documentReady.js"];
    $cssFiles = ["/css/overviewPage.css"];
    $jsVars = [
        "timestampsTooltip" => json_encode($overviewPageService->getLabelsTooltip()),
        "timestampsXAxis" => json_encode($overviewPageService->getLabelsXAxis()),
        "data1" => json_encode($overviewPageService->getData1()),
        "data2" => json_encode($overviewPageService->getData2()),
        "line1_selected" => $line1,
        "line2_selected" => $line2,
        "timeLabelUnit" => json_encode($timeLabelUnit)
    ];

    // Filter settings
    $tableMainCaptionTimeUnit = "Tag";
    $tableRow1CaptionTimeUnit = TimeHelper::formatDate($selectedDay1);
    $tableRow2CaptionTimeUnit = TimeHelper::formatDate($selectedDay2);

    $partialTop = "views/pages/overview/filter-daysselector.phtml";
    $partialBottom = "views/partials/canvas.phtml";


    include("views/partials/layout.phtml");

?>
