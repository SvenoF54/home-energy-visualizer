<?php
include_once("config/config.php");
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Config::getInstance()->daysOverview();

// Form values
$line1 = StringHelper::formGetInt("line1", $actualConfig->getLine1Default());
$line2 = StringHelper::formGetInt("line2", $actualConfig->getLine2Default());

$selectedMonth1 = StringHelper::formGetInt("month1", date("m"));
$selectedYear1 = StringHelper::formGetInt("year1", date("Y"));
$selectedFirstDayOfMonth1 = date_create("{$selectedYear1}-{$selectedMonth1}-01 00:00:00")->getTimestamp();

$selectedMonth1Minus1Month = date_sub(date_create("{$selectedYear1}-{$selectedMonth1}-01 00:00:00"), date_interval_create_from_date_string("1 month"));
$selectedMonth2 = StringHelper::formGetInt("month2", date("m", $selectedMonth1Minus1Month->getTimestamp())); 
$selectedYear2 = StringHelper::formGetInt("year2", date("Y"));
$selectedFirstDayOfMonth2 = date_create("{$selectedYear2}-{$selectedMonth2}-01 00:00:00")->getTimestamp();

$startTime1 = date("Y-m-1", $selectedFirstDayOfMonth1)." 00:00:00";
$endTime1 = date("Y-m-t", $selectedFirstDayOfMonth1)."  23:59:59";
$startTime2 = date("Y-m-1", $selectedFirstDayOfMonth2)." 00:00:00";
$endTime2 = date("Y-m-t", $selectedFirstDayOfMonth2)."  23:59:59";
$timeLabelUnit = TimeHelper::prepareTimeUnit($startTime1, $endTime1);

// Prepare DB
$errorMsg = "";

$db = new Database();
$overviewPageService = new OverviewPageService($db->getPdoConnection());
$overviewPageService->prepareDayData($startTime1, $endTime1, $startTime2, $endTime2);

// configure VIEW

    $pageTitle = "Tagesübersicht";
    $jsHeaderFiles = ["/js/utils.js", "js/overview-pages/configureChart.js", "js/overview-pages/formFunctions-monthsSelection.js"];
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
    $tableMainCaptionTimeUnit = "Monat";
    $tableRow1CaptionTimeUnit = MONTH_LIST[$selectedMonth1]." ".$selectedYear1;
    $tableRow2CaptionTimeUnit = MONTH_LIST[$selectedMonth2]." ".$selectedYear2;

    $partialTop = "views/pages/overview/filter-monthslist.phtml";
    $partialBottom = "views/partials/canvas.phtml";

    include("views/partials/layout.phtml");

?>
