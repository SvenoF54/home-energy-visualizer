<?php

class HtmlHelper {
    public static function prepareFailureColor($wrongRows, $totalRows) {
        if ($totalRows == 0) return "red";
        if ($wrongRows / $totalRows >= 0.1) return "red";
        if ($wrongRows / $totalRows >= 0.05) return "orange";
        return "success";
    }

    public static function prepareFailureStyle($failurePercent) {
        if ($failurePercent == 0) return "danger";
        if ($failurePercent >= 10) return "danger";
        if ($failurePercent >= 5) return "warning";
        return "success";
    }

    public static function showAsPercent($val1, $val2)
    {
        if ($val1 == 0 || $val2 == 0) return "100%";
        return number_format($val1 / $val2 * 100, 2) . "%";
    }

    public static function formatEnergyInWattAndCurrency(EnergyAndPriceTuple $energyData, $autarkyInPercent = null)
    {
        $result = StringHelper::formatEnergyInWattHour($energyData->getEnergyInWatt());
        $inner = StringHelper::formatCurrency($energyData->getEnergyPriceInCent());
        $inner = $autarkyInPercent != null ? $inner." | ".StringHelper::formatNumber($autarkyInPercent, 2)."%" : $inner;
        $result .= "&nbsp;(".$inner.")";

        return $result;

    }
    
    public static function formatFailureValue($titel, $nullRows, $rowCount)
    {        
        return '<div class="small m-0" style="color: ' . HtmlHelper::prepareFailureColor($nullRows, $rowCount) . ';">' 
            . $titel . '&nbsp;= ' . ($nullRows == 0 ? '/' : StringHelper::formatNumber($nullRows)) 
            . ' (' . HtmlHelper::showAsPercent($nullRows, $rowCount) . ')</div>';

    }

    public static function formatFailureForPopover($title, $nullRows, $failurePercent)
    {
        $result = '<div class="me-3 text-'.HtmlHelper::prepareFailureStyle($failurePercent).'"><strong>'.$title.'</strong></div>';
        $text = ($nullRows == 0 ? '/' : StringHelper::formatNumber($nullRows));
        $result.= '<div class="me-1 text-'.HtmlHelper::prepareFailureStyle($failurePercent).'">'.$text.'</div>';
        $result.= '<div class="text-'.HtmlHelper::prepareFailureStyle($failurePercent).'">(' . $failurePercent . '%)</div>';

        return $result;
    }

    public static function getDisplayStyleVisibleOrNot($visible)
    {
        return 'display:'.($visible  ? "block" : "none");
    }
}
