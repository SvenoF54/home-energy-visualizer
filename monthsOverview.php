<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Configuration::getInstance()->monthsOverview();
$actualConfig->setFormValues();

$selectedYear1 = StringHelper::formGetInt("year1", date("Y"));
$selectedYear2 = StringHelper::formGetInt("year2", date("Y")-1);

$timeLabelUnit = "month";

// konfigurieren
$errorMsg = "";

$overviewPageService = new OverviewPageService();
$overviewPageService->calculateMonthData($selectedYear1, $selectedYear2);
$yearList = [];
for($year = $overviewPageService->getFirstYear(); $year <= $overviewPageService->getLastYear(); $year++) {
    $yearList[] = $year;
}

// configure VIEW

    $pageTitle = "Monatsübersicht";
    $jsHeaderFiles = ["js/utils.js", "js/overview-pages/configureEnergyChart.js", "js/overview-pages/configureAutarkyChart.js", 
                      "js/overview-pages/formFunctionsForMonthOverview.js"];
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
    $tableMainCaptionTimeUnit = "Jahr";
    $tableRow1CaptionTimeUnit = $selectedYear1;
    $tableRow2CaptionTimeUnit = $selectedYear2;
    $energyTableCaption = "Energiewerte für ".$selectedYear1;


    $partialTop = "views/pages/overview/filter-for-months-overview.phtml";
    $partialBottom = "views/partials/chart-and-table-canvas.phtml";

    include("views/partials/layout.phtml");

?>
