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

$db = new Database();
$OverviewPageService = new OverviewPageService($db->getPdoConnection());
$OverviewPageService->prepareYearData($OverviewPageService->getFirstYear(), $OverviewPageService->getLastYear());
$yearList = [];
for($year = $OverviewPageService->getFirstYear(); $year <= $OverviewPageService->getLastYear(); $year++) {
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
        "timestampsTooltip" => json_encode($OverviewPageService->getLabelsTooltip()),
        "timestampsXAxis" => json_encode($OverviewPageService->getLabelsXAxis()),
        "data1" => json_encode($OverviewPageService->getData1()),
        "data2" => json_encode($OverviewPageService->getData2()),
        "line1_selected" => $line1,
        "line2_selected" => $line2,
        "timeLabelUnit" => json_encode($timeLabelUnit)
    ];

    //$partialTop = "views/pages/overview/filter-yearslist.phtml";
    $partialBottom = "views/partials/canvas.phtml";

    include("views/partials/layout.phtml");

?>
