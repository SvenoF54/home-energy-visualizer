<?php
include_once("config/config.php");
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Config::getInstance()->monthsOverview();

// Form values
$line1 = StringHelper::formGetInt("line1", $actualConfig->getLine1Default());
$line2 = StringHelper::formGetInt("line2", $actualConfig->getLine2Default());

$selectedYear1 = StringHelper::formGetInt("year1", date("Y"));
$selectedYear2 = StringHelper::formGetInt("year2", date("Y")-1);

$timeLabelUnit = "month";

// konfigurieren
$errorMsg = "";

$db = Database::getInstance();
$overviewPageService = new OverviewPageService($db->getPdoConnection());
$overviewPageService->calculateMonthData($selectedYear1, $selectedYear2);
$yearList = [];
for($year = $overviewPageService->getFirstYear(); $year <= $overviewPageService->getLastYear(); $year++) {
    $yearList[] = $year;
}

// configure VIEW

    $pageTitle = "Monatsübersicht";
    $jsHeaderFiles = ["/js/utils.js", "js/overview-pages/configureChart.js", "js/overview-pages/formFunctions-yearsSelection.js"];
    $jsFooterFiles = ["/js/overview-pages/documentReady.js"];
    $cssFiles = ["/css/overviewPage.css"];
    $jsVars = [        
        "timestampsTooltip" => json_encode($overviewPageService->getLabelsTooltip()),
        "timestampsXAxis" => json_encode($overviewPageService->getLabelsXAxis()),
        "data1" => json_encode($overviewPageService->getData1()->convertToJsChartArray()),
        "data2" => json_encode($overviewPageService->getData2()->convertToJsChartArray()),
        "line1_selected" => $line1,
        "line2_selected" => $line2,
        "timeLabelUnit" => json_encode($timeLabelUnit)
    ];

    // Filter settings
    $tableMainCaptionTimeUnit = "Jahr";
    $tableRow1CaptionTimeUnit = $selectedYear1;
    $tableRow2CaptionTimeUnit = $selectedYear2;

    $partialTop = "views/pages/overview/filter-for-months-overview.phtml";
    $partialBottom = "views/partials/chart-and-table-canvas.phtml";

    include("views/partials/layout.phtml");

?>
