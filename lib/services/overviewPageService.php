<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class OverviewPageService
{
    private $hourlyEnergyDataTbl;
    private $data1 = null;
    private $data2 = null;
    private $labelsTooltip = [];
    private $labelsXAxis = [];
    private $missingRowSet1;
    private $missingRowSet2;
    private $emOverZeroTotal1;
    private $emUnderZeroTotal1;
    private $pmSavingsTotal1;
    private $emOverZeroTotal2;
    private $emUnderZeroTotal2;
    private $pmSavingsTotal2;

    public function __construct()
    {
        $this->hourlyEnergyDataTbl = HourlyEnergyDataTable::getInstance();
        $this->missingRowSet1 = new MissingRowSet();
        $this->missingRowSet2 = new MissingRowSet();

        $this->emOverZeroTotal1 = new EnergyAndPriceTuple();
        $this->emUnderZeroTotal1 = new EnergyAndPriceTuple();
        $this->pmSavingsTotal1 = new EnergyAndPriceTuple();
        $this->emOverZeroTotal2 = new EnergyAndPriceTuple();
        $this->emUnderZeroTotal2 = new EnergyAndPriceTuple();
        $this->pmSavingsTotal2 = new EnergyAndPriceTuple();
    }

    public function calculateYearData($firstYear, $lastYear)
    {
        $startTime1 = date("Y-1-1", strtotime("$firstYear-1-1"))." 00:00:00";
        $endTime1 = date("Y-12-31", strtotime("$lastYear-12-31"))." 23:59:59";

        $this->prepareGeneralData($startTime1, $endTime1);

        $this->data1 = $this->prepareDataRangeForEachYears($firstYear, $lastYear);
        $this->data2 = null;
        for($year = $this->getFirstYear(); $year <= $this->getLastYear(); $year++) {            
            $this->labelsTooltip[] = [$year, $year]; // Doppeltes Array für Tooltips
            $this->labelsXAxis[] = [$year];
        }        
    }

    public function calculateMonthData($year1, $year2)
    {
        $startTime1 = date("Y-1-1", strtotime("$year1-1-1"))." 00:00:00";
        $endTime1 = date("Y-12-31", strtotime("$year1-12-31"))."  23:59:59";
        $startTime2 = date("Y-1-1", strtotime("$year2-1-1"))." 00:00:00";
        $endTime2 = date("Y-12-31", strtotime("$year2-12-31"))."  23:59:59";

        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRangeForEachMonth($year1);        
        $this->data2 = $this->prepareDataRangeForEachMonth($year2);
        for($nt = 0; $nt < $this->data1->sizeof(); $nt++) {
            $dtData1 = $this->data1->getTimestampForView($nt);
            $dtData2 = $this->data2->getTimestampForView($nt);
            $this->labelsTooltip[] = [date("m.Y", strtotime($dtData1)), date("m.Y", strtotime($dtData2))];
            $this->labelsXAxis[] = [date("m.Y", strtotime($dtData1)), date("m.Y", strtotime($dtData2))];
        }        
    }

    public function calculateDayData($startTime1, $endTime1, $startTime2, $endTime2)
    {
        $avg = 86400;  // Sekunden pro Tag
        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRange($startTime1, $endTime1, $avg);
        $this->data2 = $this->prepareDataRange($startTime2, $endTime2, $avg);        
        for($nt = 0; $nt < $this->data1->sizeof(); $nt++) {
            $dtData1 = $this->data1->getTimestampForView($nt);
            $dtData2 = $this->data2->getTimestampForView($nt);
            $this->labelsTooltip[] = [date("d.m.Y", strtotime($dtData1)), date("d.m.Y", strtotime($dtData2))];
            $this->labelsXAxis[] = [date("d.m.Y", strtotime($dtData1)), date("d.m.Y", strtotime($dtData2))];
        }
        
    }

    public function calculateHourData($startTime1, $endTime1, $startTime2=null, $endTime2=null)
    {
        $avg = 3600;  // 1 Stunde
        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRange($startTime1, $endTime1, $avg);
        $this->data2 = $this->prepareDataRange($startTime2, $endTime2, $avg);        
        for($nt = 0; $nt < $this->data1->sizeof(); $nt++) {
            $dtData1 = $this->data1->getTimestampForView($nt);
            $dtData2 = $this->data2->getTimestampForView($nt);
            $this->labelsTooltip[] = [$dtData1, $dtData2];
            $this->labelsXAxis[] = [date("H:i", strtotime($dtData1))];
        }        
    }

    private function prepareGeneralData($startTime1, $endTime1, $startTime2 = null, $endTime2 = null)
    {
        $powerData = $this->hourlyEnergyDataTbl->getEnergyData($startTime1, $endTime1);
        
        $this->emOverZeroTotal1 = $powerData->getEnergy();
        $this->emUnderZeroTotal1 = $powerData->getEnergyUnderZero();
        $this->pmSavingsTotal1 = $powerData->getSavings();
        $this->missingRowSet1 = $powerData->getMissingRows();

        if ($startTime2 == null) {
            return;
        }
        $powerData = $this->hourlyEnergyDataTbl->getEnergyData($startTime2, $endTime2);
        $this->emOverZeroTotal2 = $powerData->getEnergy();
        $this->emUnderZeroTotal2 = $powerData->getEnergyUnderZero();
        $this->pmSavingsTotal2 = $powerData->getSavings();
        $this->missingRowSet2 = $powerData->getMissingRows();
    }

    private function prepareDataRange($startTime, $endTime, $avg)
    {
        $energyDataSetList = new EnergyDataSetList();
        if ($startTime == null) return $energyDataSetList;
        for ($time = strtotime($startTime); $time <= strtotime($endTime); $time += $avg) {
            $strStart = date('Y-m-d H:i:s', $time);
            $strEnd = date('Y-m-d H:i:s', $time + $avg -1);  # -1 Sekunde für :59 Sekunden
            $powerData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);
            $powerData->setTimestampForView(date('d.m.Y H:i', $time));
            $energyDataSetList->addItem($powerData);
        }
        return $energyDataSetList;
    }

    private function prepareDataRangeForEachMonth($year)
    {
        $energyDataSetList = new EnergyDataSetList();
        for ($month = 1; $month <= 12; $month++) {            
            $strStart = date('Y-m-d H:i:s', strtotime("$year-$month-1"." 00:00:00"));
            $strEnd = date('Y-m-d H:i:s', strtotime("$year-$month-".TimeHelper::getDaysInMonth($month, $year)." 23:59:59"));            
            $powerData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd);
            $powerData->setTimestampForView(date('d.m.Y', strtotime("$year-$month-1"." 00:00:00")));
            $energyDataSetList->addItem($powerData);
        }
        return $energyDataSetList;
    }

    private function prepareDataRangeForEachYears($firstYear, $lastYear)
    {
        $energyDataSetList = new EnergyDataSetList();
        for ($year = $firstYear; $year <= $lastYear; $year++) {            
            $strStart = date('Y-m-d H:i:s', strtotime("$year-1-1"." 00:00:00"));
            $strEnd = date('Y-m-d H:i:s', strtotime("$year-12-31"." 23:59:59"));            
            $powerData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd);
            $powerData->setTimestampForView(date('Y', strtotime("$year-1-1"." 00:00:00")));
            $energyDataSetList->addItem($powerData);
        }
        return $energyDataSetList;
    }

    public function getTableStatistics(): TableStatisticSet { return $this->hourlyEnergyDataTbl->getStatistics(); }
    public function getFirstYear() { return date("Y", strtotime($this->getTableStatistics()->getFirstRowDate())); }
    public function getLastYear() { return date("Y", strtotime($this->getTableStatistics()->getLastRowDate())); }

    public function getData1List() : EnergyDataSetList { return $this->data1; }
    public function getData2List() : EnergyDataSetList { return $this->data2; }

    public function hasData1() { return $this->data1 != null; }
    public function hasData2() { return $this->data2 != null; }

    public function getLabelsTooltip() : array { return $this->labelsTooltip; }
    public function getLabelsXAxis() : array { return $this->labelsXAxis; }

    public function getMissingRowSet1() : MissingRowSet { return $this->missingRowSet1; }
    public function getMissingRowSet2() : MissingRowSet { return $this->missingRowSet2; }

    public function getEMOverZeroTotal1() : EnergyAndPriceTuple { return $this->emOverZeroTotal1; }
    public function getEMOverZeroTotal2() : EnergyAndPriceTuple { return $this->emOverZeroTotal2; }

    public function getEMUnderZeroTotal1() : EnergyAndPriceTuple { return $this->emUnderZeroTotal1; }
    public function getEMUnderZeroTotal2() : EnergyAndPriceTuple { return $this->emUnderZeroTotal2; }

    public function getPMSavingsTotal1() : EnergyAndPriceTuple { return $this->pmSavingsTotal1; }
    public function getPMSavingsTotal2() : EnergyAndPriceTuple { return $this->pmSavingsTotal2; }

    public function getConsumptionTotal1() : EnergyAndPriceTuple
    {
        $result = $this->emOverZeroTotal1;
        $result->add($this->pmSavingsTotal1);
        return $result;
    }

    public function getConsumptionTotal2() : EnergyAndPriceTuple
    {
        $result = $this->emOverZeroTotal2;
        $result->add($this->pmSavingsTotal2);
        return $result;
    }

    public function getAutarkyInPercent1() {
        return self::calculateAutarky($this->getPMSavingsTotal1()->getEnergyInWatt(), $this->getEMOverZeroTotal1()->getEnergyInWatt());
    }

    public function getAutarkyInPercent2() {
        return self::calculateAutarky($this->getPMSavingsTotal2()->getEnergyInWatt(), $this->getEMOverZeroTotal2()->getEnergyInWatt());
    }

    public function getSelfConsumptionInPercent1()
    {
        return self::calculateSelfConsumption($this->getPMSavingsTotal1()->getEnergyInWatt(), $this->getEMUnderZeroTotal1()->getEnergyInWatt());
    }

    public function getSelfConsumptionInPercent2()
    {
        return self::calculateSelfConsumption($this->getPMSavingsTotal2()->getEnergyInWatt(), $this->getEMUnderZeroTotal2()->getEnergyInWatt());
    }

    public static function calculateAutarky($savings, $emOverZero)
    {
        $totalConsumption = $emOverZero + $savings;
        if ($totalConsumption <= 0) {
            return 0;
        }

        $percentAutarky = (1-($emOverZero / $totalConsumption)) * 100;
        return $percentAutarky;
    }

    public static function calculateSelfConsumption($savings, $emUnderZero)
    {
        $totalProduction = $savings + abs($emUnderZero);
        if ($totalProduction <= 0) {
            return 0;
        }
    
        $percentSelfConsumption = ($savings / $totalProduction) * 100;    
        return $percentSelfConsumption;
    }
    

}
