<?php
include_once("config/config.php");
include_once("lib/appLibLoader.php");

// Defaults
$actualConfig = Config::getInstance()->customEnergyValuesPage();
$dateOrMonth = $actualConfig->getDefaultMonthOrYear();

// Prepare DB
$db = Database::getInstance();
$hourlyEnergyDataTbl = new HourlyEnergyDataTable($db->getPdoConnection());
if (isset($_POST) && sizeof($_POST)) {    
    if (StringHelper::formGetBool("performDelete") == true) {
        // Delete
        $timestamp = StringHelper::formGetString("timestampDelete");
        $customRow = $hourlyEnergyDataTbl->getCustomRow($timestamp);
        if ($customRow != null) {
            $timestampType = $customRow->getTimestampType();
            $timestampRaw = $customRow->getTimestampFrom();
            $timestampHtml = $timestampType == "month" ? substr(TimeHelper::formatDate($customRow->getTimestampFrom()), 3) : TimeHelper::formatDate($customRow->getTimestampFrom());

            $hourlyEnergyDataTbl->deleteCustomRow($timestamp);
            $successMsg = "Der Eintrag für den $timestampHtml wurde gelöscht.";
        } else {
            $errorMsg = "Konnte keine Eintrag finden.";
        }
    } else {
        // Save
        $dateOrMonth = StringHelper::formGetString('timestampType', 'month');
        $timestamp =  $dateOrMonth == "month" ? StringHelper::formGetMonthYear('timestamp') : StringHelper::formGetDate('timestamp');
        $timestampFrom = TimeHelper::formatForDatabase($timestamp);
        $timestampTo = $dateOrMonth == "month" ? TimeHelper::formatForDatabase(TimeHelper::getEndOfMonth($timestampFrom, true)) : TimeHelper::formatForDatabase(TimeHelper::getEndOfDay($timestampFrom, true));
        $outCentPricePerKwh = StringHelper::formGetFloat('outCentPricePerKwh');
        $inCentPricePerKwh = StringHelper::formGetFloat('inCentPricePerKwh');
        $consumptionKwh = StringHelper::formGetFloat('consumption', 0);
        $feedInKwh = StringHelper::formGetFloat('feedIn', 0);
        $producedPowerKwh = StringHelper::formGetFloat('producedPower', 0);
        $producedPowerPhases = StringHelper::formGetStringArray('phases', array(1, 2, 3));

        if (($timestamp == null) || ($outCentPricePerKwh == null) || ($inCentPricePerKwh == null) || ($consumptionKwh == 0 && $feedInKwh == 0 && $producedPowerKwh == 0)) {
            $errorMsg = "Bitte den Monat, den Preis und mindestens einen Energiewert angeben.";
        } else {
            $customValSet = new CustomEnergyValueSet($timestampFrom, $timestampTo, $outCentPricePerKwh / 1000, $inCentPricePerKwh / 1000);
            $customValSet->setEmPower($consumptionKwh * 1000, -$feedInKwh * 1000);
            $customValSet->setPmPower($producedPowerKwh * 1000, in_array(1, $producedPowerPhases), in_array(2, $producedPowerPhases), in_array(3, $producedPowerPhases));
            
            $succes = $hourlyEnergyDataTbl->saveCustomData($customValSet);
            if (! $succes) {
                $errorMsg = $hourlyEnergyDataTbl->getError();
            } else {
                $successMsg = "Die Daten wurden gespeichert.";
                $consumptionKwh = "";
                $feedInKwh = "";
                $producedPowerKwh = "";
            }
        }
    }
}
$customDataList = $hourlyEnergyDataTbl->getCustomDataList();


// configure VIEW

$pageTitle = "Eigene Stromdaten";
$jsHeaderFiles = ["/js/utils.js"];
$jsFooterFiles = ["/js/custom-energy-values/documentReady.js"];
$cssFiles = [];
$jsVars = [];

$partialTop = "views/pages/custom-energy-values/inputform.phtml";
$partialBottom = "views/pages/custom-energy-values/values-list.phtml";


include("views/partials/layout.phtml");

?>
