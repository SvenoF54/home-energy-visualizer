<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

include_once("lib/appLibLoader.php");

// Prepare DB
$energyPriceTbl = EnergyPriceTable::getInstance();
$hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
if (isset($_POST) && sizeof($_POST)) {    
    
    if (StringHelper::formGetBool("performDelete") == true) {
        $idPriceRowToDelete = StringHelper::formGetInt('priceRowId', null);
        $customRow = $energyPriceTbl->getCustomRow($idPriceRowToDelete);
        if ($customRow != null) {
            $energyPriceTbl->deleteCustomRow($idPriceRowToDelete);
            $successMsg = "Der Eintrag für den Zeitraum zwischen ".TimeHelper::FormatDate($customRow->getTimestampFrom())
                        ." und ".TimeHelper::FormatDate($customRow->getTimestampTo())." wurde gelöscht."
                        ."<br>Es wurden keine Preisdaten bei den Energiewerten angepasst.";
        } else {
            $errorMsg = "Konnte keine Eintrag finden.";
        }
    } else {    
    
        // Save
        $idPriceRow = StringHelper::formGetInt('priceRowId', null);        
        $timestampFrom =  StringHelper::formGetDate('timestampFrom');
        $timestampTo =  StringHelper::formGetDate('timestampTo');
        $outCentPricePerKwh = StringHelper::formGetFloat('outCentPricePerKwh');
        $inCentPricePerKwh = StringHelper::formGetFloat('inCentPricePerKwh');                
        $doesTimeOverlap = $energyPriceTbl->doesTimeOverlap($idPriceRow, $timestampFrom, $timestampTo);

        if (($timestampFrom == null) || ($timestampTo == null) || ($outCentPricePerKwh == null) || ($inCentPricePerKwh == null)) {
            $errorMsg = "Bitte den Zeitraum und die Preise angeben.";
        } elseif ($doesTimeOverlap) {
            $errorMsg = "Es gibt bereits einen Preiseintrag, welche innerhalb des Zeitraums ".TimeHelper::FormatDate($timestampFrom)." und ".TimeHelper::FormatDate($timestampTo)." gültig ist.";
        } else {
            $energyPriceRow = new EnergyPriceRow($idPriceRow, $timestampFrom, $timestampTo, $outCentPricePerKwh / 1000, $inCentPricePerKwh / 1000, 1);            
            $succes = $idPriceRow != null ? $energyPriceTbl->updateCustomData($energyPriceRow) : $energyPriceTbl->insertCustomData($energyPriceRow);
            if (! $succes) {
                $errorMsg = $energyPriceTbl->getError();                
            } else {
                $updateTimestampFrom = $timestampFrom . " 00:00:00";
                $updateTimestampTo = $timestampTo." 23:59:59";
                $hourlyEnergyDataTbl->updatePricesForTimeRange($updateTimestampFrom, $updateTimestampTo, $outCentPricePerKwh / 1000, $inCentPricePerKwh / 1000);
                $successMsg = "Die Preisdaten wurden gespeichert und die Preise für den Zeitraum zwischen "
                            .TimeHelper::formatDateTime($updateTimestampFrom, true)." und ".TimeHelper::formatDateTime($updateTimestampTo, true)
                            ."<br> auf den Einkaufspreis von ".StringHelper::formatCentCurrency($outCentPricePerKwh)
                            ." und dem Verkaufspreis von ".StringHelper::formatCentCurrency($inCentPricePerKwh)." angepasst.";
                $idPriceRow = null;
                $timestampFrom = "";
                $timestampTo = "";
                $outCentPricePerKwh = "";
                $inCentPricePerKwh = "";
            }
        }
    }
}
$customDataList = $energyPriceTbl->getCustomAndAutomaticPriceRows();



// configure VIEW

$pageTitle = "Eigene Preisdaten";
$jsHeaderFiles = ["/js/utils.js"];
$jsFooterFiles = ["/js/custom-price-values/documentReady.js"];
$cssFiles = [];
$jsVars = [];

$partialTop = "views/pages/custom-price-values/inputform.phtml";
$partialBottom = "views/pages/custom-price-values/values-list.phtml";


include("views/partials/layout.phtml");

?>
