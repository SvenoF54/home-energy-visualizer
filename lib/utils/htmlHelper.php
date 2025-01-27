<?php

class HtmlHelper {
    public static function prepareFailureColor($wrongRows, $totalRows) {
        if ($totalRows == 0 && $wrongRows == 0) return "success";
        if ($totalRows == 0) return "red";
        if ($wrongRows / $totalRows >= 0.1) return "red";
        if ($wrongRows / $totalRows >= 0.05) return "orange";
        return "success";
    }

    public static function prepareFailureStyle($failurePercent) {
        if ($failurePercent == 0) return "success";
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
        $inner = $autarkyInPercent != null ? $inner." | ".StringHelper::formatNumber($autarkyInPercent, 0)."%" : $inner;
        $result .= " (".$inner.")";

        return $result;

    }
    
    public static function formatFailureValue($titel, $nullRows, $rowCount)
    {        
        return '<div class="small m-0" style="color: ' . HtmlHelper::prepareFailureColor($nullRows, $rowCount) . ';">' 
            . $titel . '&nbsp;= ' . ($nullRows == 0 ? '/' : StringHelper::formatIntNumber($nullRows)) 
            . ' (' . HtmlHelper::showAsPercent($nullRows, $rowCount) . ')</div>';

    }

    public static function formatFailureForPopover($title, $nullRows, $failurePercent)
    {
        $result = '<div class="small me-3 text-'.HtmlHelper::prepareFailureStyle($failurePercent).'"><strong>'.$title.'</strong></div>';
        $text = ($nullRows == 0 ? '/' : StringHelper::formatIntNumber($nullRows));
        $result.= '<div class="small me-1 text-'.HtmlHelper::prepareFailureStyle($failurePercent).'">'.$text.'</div>';
        $result.= '<div class="small text-'.HtmlHelper::prepareFailureStyle($failurePercent).'">(' . $failurePercent . '%)</div>';        

        return $result;
    }

    public static function renderPopoverFailureTemplate(MissingRowSet $missingRowSet, $htmlId)
    {
        ?>

        <div id="<?=$htmlId?>" class="hidden-html">            
            <div class="popover-heading"><strong>Fehlende Werte</strong></div>
            
            <div class="popover-body">
                <?php if ($missingRowSet->isEmAvailable()) { ?>
                    <div class="d-flex justify-content-between mb-2">
                        <?=HtmlHelper::formatFailureForPopover("EM", $missingRowSet->getEmMissingRows(), $missingRowSet->getEmMissingRowsPercent()) ?>
                    </div>
                <?php } ?>
                <?php if ($missingRowSet->isPm1Available()) { ?>
                    <div class="d-flex justify-content-between mb-2">
                        <?=HtmlHelper::formatFailureForPopover("PM1", $missingRowSet->getPm1MissingRows(), $missingRowSet->getPm1MissingRowsPercent()) ?>
                    </div>
                <?php } ?>
                <?php if ($missingRowSet->isPm2Available()) { ?>
                    <div class="d-flex justify-content-between mb-2">
                        <?=HtmlHelper::formatFailureForPopover("PM2", $missingRowSet->getPm2MissingRows(), $missingRowSet->getPm2MissingRowsPercent()) ?>
                    </div>
                <?php } ?>
                <?php if ($missingRowSet->isPm3Available()) { ?>
                    <div class="d-flex justify-content-between mb-2">
                        <?=HtmlHelper::formatFailureForPopover("PM3", $missingRowSet->getPm3MissingRows(), $missingRowSet->getPm3MissingRowsPercent()) ?>
                    </div>
                <?php } ?>
                    <div class="d-flex justify-content-between mb-2">
                        <div class="me-3 text-success"><strong>Zeilen<br/>gesamt</strong></div>
                        <div class="me-1 text-success"><?=($missingRowSet->getCountRows() == 0 ? '/' : StringHelper::formatIntNumber($missingRowSet->getCountRows()))?></div>
                    </div>                
            </div>
        </div>



        <?php        
    }

    public static function getDisplayStyleVisibleOrNot($visible)
    {
        return 'display:'.($visible  ? "block" : "none");
    }
}
