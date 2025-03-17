<?php
/* NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer> 
   Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html> */

include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->daysOverview();
$actualConfig->setFormValues();


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

$overviewPageService = new OverviewPageService();
$overviewPageService->calculateDayData($startTime1, $endTime1, $startTime2, $endTime2);

// configure VIEW

    $pageTitle = "Tagesübersicht";
    $jsHeaderFiles = ["js/utils.js", "js/overview-pages/configureEnergyChart.js", "js/overview-pages/configureAutarkyChart.js", 
                      "js/overview-pages/formFunctionsForDayOverview.js"];
    $jsFooterFiles = ["js/overview-pages/documentReady.js", "js/overview-pages/configureDataTable.js"];
    $cssFiles = ["css/overviewPage.css"];
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
    $tableMainCaptionTimeUnit = "Monat";
    $tableRow1CaptionTimeUnit = TimeHelper::formatMonthNameAndYear($startTime1);
    $tableRow2CaptionTimeUnit = TimeHelper::formatMonthNameAndYear($startTime2);
    $energyTableCaption = "Energiewerte für ".TimeHelper::formatMonthNameAndYear($startTime1);


    $partialTop = "views/pages/overview/filter-for-days-overview.phtml";
    $partialBottom = "views/partials/chart-and-table-canvas.phtml";

    include("views/partials/layout.phtml");

?>
