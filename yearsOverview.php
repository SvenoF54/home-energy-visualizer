<?php
include_once("config/config.php");
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Config::getInstance()->yearsOverview();

// Form values
$line1 = StringHelper::formGetInt("line1", $actualConfig->getLine1Default());
$line2 = StringHelper::formGetInt("line2", $actualConfig->getLine2Default());

$timeLabelUnit = "year";

// prepare DB
$errorMsg = "";

$db = Database::getInstance();
$overviewPageService = new OverviewPageService($db->getPdoConnection());
$overviewPageService->calculateYearData($overviewPageService->getFirstYear(), $overviewPageService->getLastYear());
$yearList = [];
for($year = $overviewPageService->getFirstYear(); $year <= $overviewPageService->getLastYear(); $year++) {
    $yearList[] = $year;
    $timestampsTooltip[] = [$year, $year]; // Doppeltes Array für Tooltips
    $timestampsXAxis[] = $year;
}

// configure VIEW

    $pageTitle = "Jahresübersicht";
    $jsHeaderFiles = ["/js/utils.js", "js/overview-pages/configureChart.js"];
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
    $tableMainCaptionTimeUnit = "Erfasste Jahre";
    $tableRow1CaptionTimeUnit = "(Summe über alles)";

    $partialTop = "views/pages/overview/filter-for-years-overview.phtml";
    $partialBottom = "views/partials/chart-and-table-canvas.phtml";

    include("views/partials/layout.phtml");

?>
