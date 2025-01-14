<?php

class HtmlHelper {
    public static function prepareFailureColor($wrongRows, $totalRows) {
        if ($totalRows == 0) return "red";
        if ($wrongRows / $totalRows >= 0.1) return "red";
        if ($wrongRows / $totalRows >= 0.05) return "orange";
        return "green";
    }

    public static function showAsPercent($val1, $val2)
    {
        if ($val1 == 0 || $val2 == 0) return "100%";
        return number_format($val1 / $val2 * 100, 2) . "%";
    }

    public static function formatEnergyInWattAndCurrency(EnergyAndPriceTuple $energyData, $strong = false)
    {
        if ($strong) {
            return "<strong>".StringHelper::formatEnergyInWattHour($energyData->getEnergyInWatt())."</strong>&nbsp;(".StringHelper::formatCurrency($energyData->getEnergyPriceInCent()).")";
        }
        return StringHelper::formatEnergyInWattHour($energyData->getEnergyInWatt())."&nbsp;(".StringHelper::formatCurrency($energyData->getEnergyPriceInCent()).")";

    }
    
    public static function formatFailureValue($titel, $nullRows, $rowCount)
    {
        return '<div class="small m-0" style="color: ' . HtmlHelper::prepareFailureColor($nullRows, $rowCount) . ';">' 
            . $titel . '&nbsp;= ' . ($nullRows == 0 ? '/' : StringHelper::formatNumber($nullRows)) 
            . ' (' . HtmlHelper::showAsPercent($nullRows, $rowCount) . ')</div>';

    }

    public static function getDisplayStyleVisibleOrNot($visible)
    {
        return 'display:'.($visible  ? "block" : "none");
    }
}
