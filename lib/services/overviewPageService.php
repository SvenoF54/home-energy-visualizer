<?php

class OverviewPageService
{
    private $hourlyEnergyDataTbl;
    private $data1 = null;
    private $data2 = null;
    private $labelsTooltip = [];
    private $labelsXAxis = [];
    private $missingValues1;
    private $missingValues2;
    private $emOverZeroTotal1;
    private $emUnderZeroTotal1;
    private $pmSavingsTotal1;
    private $emOverZeroTotal2;
    private $emUnderZeroTotal2;
    private $pmSavingsTotal2;

    public function __construct($pdoConnection)
    {
        $this->hourlyEnergyDataTbl = new HourlyEnergyDataTable($pdoConnection);
        $this->missingValues1 = new MissingRowSet();
        $this->missingValues2 = new MissingRowSet();

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
        $this->data2 = [];
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
        $this->missingValues1 = $powerData->getMissingRows();

        if ($startTime2 == null) {
            return;
        }
        $powerData = $this->hourlyEnergyDataTbl->getEnergyData($startTime2, $endTime2);
        $this->emOverZeroTotal2 = $powerData->getEnergy();
        $this->emUnderZeroTotal2 = $powerData->getEnergyUnderZero();
        $this->pmSavingsTotal2 = $powerData->getSavings();
        $this->missingValues2 = $powerData->getMissingRows();
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
            $powerData->setTimestampForView(date('d.m.Y H:i', strtotime("$year-$month-1"." 00:00:00")));
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
            $powerData->setTimestampForView(date('Y-m-d H:i:s', strtotime("$year-1-1"." 00:00:00")));
            $energyDataSetList->addItem($powerData);
        }
        return $energyDataSetList;
    }

    public function getTableStatistics(): TableStatisticSet
    {
        return $this->hourlyEnergyDataTbl->getStatistics();
    }

    public function getFirstYear()
    {        
        return date("Y", strtotime($this->getTableStatistics()->getFirstRowDate()));
    }

    public function getLastYear()
    {
        return date("Y", strtotime($this->getTableStatistics()->getLastRowDate()));
    }

    public function getData1List() : EnergyDataSetList
    {
        return $this->data1;
    }

    public function getData2List() : EnergyDataSetList
    {
        return $this->data2;
    }

    public function hasData1() {
        return sizeof($this->data1) > 0;
    }

    public function hasData2() {
        return sizeof($this->data2) > 0;
    }

    public function getLabelsTooltip() : array
    {
        return $this->labelsTooltip;
    }

    public function getLabelsXAxis() : array
    {
        return $this->labelsXAxis;
    }

    public function getMissingValues1() : MissingRowSet
    {
        return $this->missingValues1;
    }

    public function getMissingValues2() : MissingRowSet
    {
        return $this->missingValues2;
    }

    public function getEMOverZeroTotal1() : EnergyAndPriceTuple
    {
        return $this->emOverZeroTotal1;
    }

    public function getEMUnderZeroTotal1() : EnergyAndPriceTuple
    {
        return $this->emUnderZeroTotal1;
    }

    public function getPMSavingsTotal1() : EnergyAndPriceTuple
    {
        return $this->pmSavingsTotal1;
    }

    public function getConsumptionTotal1() : EnergyAndPriceTuple
    {
        $result = $this->emOverZeroTotal1;
        $result->add($this->pmSavingsTotal1);
        return $result;
    }

    public function getAutarkyInPercent1() {
        $totalConsumption = $this->getEMOverZeroTotal1()->getEnergyInWatt() + $this->getPMSavingsTotal1()->getEnergyInWatt();
        $percentAutarky = 0;
        if ($totalConsumption > 0) {
            $percentAutarky = (1-($this->getEMOverZeroTotal1()->getEnergyInWatt() / $totalConsumption)) * 100;
        }

        return $percentAutarky;
    }

    public function getEMOverZeroTotal2() : EnergyAndPriceTuple
    {
        return $this->emOverZeroTotal2;
    }

    public function getEMUnderZeroTotal2() : EnergyAndPriceTuple
    {
        return $this->emUnderZeroTotal2;
    }

    public function getPMSavingsTotal2() : EnergyAndPriceTuple
    {
        return $this->pmSavingsTotal2;
    }

    public function getConsumptionTotal2() : EnergyAndPriceTuple
    {
        $result = $this->emOverZeroTotal2;
        $result->add($this->pmSavingsTotal2);
        return $result;
    }

    public function getAutarkyInPercent2() {
        $totalConsumption = $this->getEMOverZeroTotal2()->getEnergyInWatt() + $this->getPMSavingsTotal2()->getEnergyInWatt();
        $percentAutarky = 0;
        if ($totalConsumption > 0) {
            $percentAutarky = (1-($this->getEMOverZeroTotal2()->getEnergyInWatt() / $totalConsumption)) * 100;
        }

        return $percentAutarky;
    }

}
