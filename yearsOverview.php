<?php
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->yearsOverview();
$actualConfig->setFormValues();

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
    $jsHeaderFiles = ["/js/utils.js", "js/overview-pages/configureEnergyChart.js", "js/overview-pages/configureAutarkyChart.js"];
    $jsFooterFiles = ["/js/overview-pages/documentReady.js", "/js/overview-pages/configureDataTable.js"];
    $cssFiles = ["/css/overviewPage.css"];
    $jsVars = [        
        "timestampsTooltip" => json_encode($overviewPageService->getLabelsTooltip()),
        "timestampsXAxis" => json_encode($overviewPageService->getLabelsXAxis()),
        "data1" => json_encode($overviewPageService->getData1List()->convertToJsChartArray()),
        "data2" => json_encode([]),
        "autarky1" => json_encode($overviewPageService->getData1List()->calculateAutarkyForJsChartArray()),
        "autarky2" => json_encode([]),
        "timeLabelUnit" => json_encode($timeLabelUnit),
        "config" => $actualConfig->toJson()
    ];

    // Filter settings
    $tableMainCaptionTimeUnit = "Erfasste Jahre";
    $tableRow1CaptionTimeUnit = "(".$overviewPageService->getFirstYear()." bis ".$overviewPageService->getLastYear().")";
    $energyTableCaption = "Energiewerte für ".$overviewPageService->getFirstYear()." bis ".$overviewPageService->getLastYear();


    $partialTop = "views/pages/overview/filter-for-years-overview.phtml";
    $partialBottom = "views/partials/chart-and-table-canvas.phtml";

    include("views/partials/layout.phtml");

?>
