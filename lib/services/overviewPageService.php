<?

class OverviewPageService
{
    private $hourlyEnergyDataTbl;
    private $data1 = [];
    private $data2 = [];
    private $labelsTooltip = [];
    private $labelsXAxis = [];
    private $missingValues1 = [];
    private $missingValues2 = [];
    private $emOverZeroTotal1 = 0;
    private $emUnderZeroTotal1 = 0;
    private $pmSavingsTotal1 = 0;
    private $emOverZeroTotal2 = 0;
    private $emUnderZeroTotal2 = 0;
    private $pmSavingsTotal2 = 0;

    public function __construct($pdoConnection)
    {
        $this->hourlyEnergyDataTbl = new HourlyEnergyDataTable($pdoConnection);
    }

    public function prepareYearData($firstYear, $lastYear)
    {
        $startTime1 = date("Y-1-1", strtotime("$firstYear-1-1"))." 00:00:00";
        $endTime1 = date("Y-12-31", strtotime("$firstYear-12-31"))." 23:59:59";
        $startTime2 = date("Y-1-1", strtotime("$lastYear-1-1"))." 00:00:00";
        $endTime2 = date("Y-12-31", strtotime("$lastYear-12-31"))." 23:59:59";

        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRangeForEachYears($firstYear, $lastYear);
        $this->data2 = [];
        for($year = $this->getFirstYear(); $year <= $this->getLastYear(); $year++) {            
            $this->labelsTooltip[] = [$year, $year]; // Doppeltes Array für Tooltips
            $this->labelsXAxis[] = $year;
        }        
    }

    public function prepareMonthData($year1, $year2)
    {
        $startTime1 = date("Y-1-1", strtotime("$year1-1-1"))." 00:00:00";
        $endTime1 = date("Y-12-31", strtotime("$year1-12-31"))."  23:59:59";
        $startTime2 = date("Y-1-1", strtotime("$year2-1-1"))." 00:00:00";
        $endTime2 = date("Y-12-31", strtotime("$year2-12-31"))."  23:59:59";

        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRangeForEachMonth($year1);        
        $this->data2 = $this->prepareDataRangeForEachMonth($year2);
        for($nt = 0; $nt < sizeof($this->data1); $nt++) {
            $dtData1 = $this->data1[$nt]["x-datetime"];
            $dtData2 = $nt < sizeof($this->data2) ? $this->data2[$nt]["x-datetime"] : "";
            $this->labelsTooltip[] = [date("m.Y", strtotime($dtData1)), date("m.Y", strtotime($dtData2))];
            $this->labelsXAxis[] = [date("m.Y", strtotime($dtData1)), date("m.Y", strtotime($dtData2))];
        }        
    }

    public function prepareDayData($startTime1, $endTime1, $startTime2, $endTime2)
    {
        $avg = 86400;  // Sekunden pro Tag
        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRange($startTime1, $endTime1, $avg);
        $this->data2 = $this->prepareDataRange($startTime2, $endTime2, $avg);        
        for($nt = 0; $nt < sizeof($this->data1); $nt++) {
            $dtData1 = $this->data1[$nt]["x-datetime"];
            $dtData2 = $nt < sizeof($this->data2) ? $this->data2[$nt]["x-datetime"] : "";
            $this->labelsTooltip[] = [date("d.m.Y", strtotime($dtData1)), date("d.m.Y", strtotime($dtData2))];
            $this->labelsXAxis[] = [date("d.m.Y", strtotime($dtData1)), date("d.m.Y", strtotime($dtData2))];
        }
        
    }

    public function prepareHourData($startTime1, $endTime1, $startTime2, $endTime2)
    {
        $avg = 3600;  // 1 Stunde
        $this->prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2);

        $this->data1 = $this->prepareDataRange($startTime1, $endTime1, $avg);
        $this->data2 = $this->prepareDataRange($startTime2, $endTime2, $avg);        
        for($nt = 0; $nt < sizeof($this->data1); $nt++) {
            $dtData1 = $this->data1[$nt]["x-datetime"];
            $dtData2 = $nt < sizeof($this->data2) ? $this->data2[$nt]["x-datetime"] : "";
            $this->labelsTooltip[] = [$dtData1, $dtData2];
            $this->labelsXAxis[] = [date("H:i", strtotime($dtData1))];
        }        
    }

    private function prepareGeneralData($startTime1, $endTime1, $startTime2, $endTime2)
    {
        $powerData = $this->hourlyEnergyDataTbl->getEnergyData($startTime1, $endTime1);
        
        $this->emOverZeroTotal1 = $powerData->getEnergy();
        $this->emUnderZeroTotal1 = $powerData->getEnergyUnderZero();
        $this->pmSavingsTotal1 = $powerData->getSavings();
        $this->missingValues1 = $powerData->getMissingRows();

        $powerData = $this->hourlyEnergyDataTbl->getEnergyData($startTime2, $endTime2);
        $this->emOverZeroTotal2 = $powerData->getEnergy();
        $this->emUnderZeroTotal2 = $powerData->getEnergyUnderZero();
        $this->pmSavingsTotal2 = $powerData->getSavings();
        $this->missingValues2 = $powerData->getMissingRows();
    }

    private function prepareDataRange($startTime, $endTime, $avg)
    {
        $data = [];
        for ($time = strtotime($startTime); $time <= strtotime($endTime); $time += $avg) {
            $strStart = date('Y-m-d H:i:s', $time);
            $strEnd = date('Y-m-d H:i:s', $time + $avg -1);  # -1 Sekunde für :59 Sekunden
            $powerData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd, $avg);
            $dataRow = [
                "x-datetime" => date('d.m.Y H:i', $time),
                "emOZ" => $powerData->getEnergyOverZero()->getEnergyInWatt(),
                "emOZPrice" => $powerData->getEnergyOverZero()->getEnergyPriceInCent(),
                "emUZ" => $powerData->getEnergyUnderZero()->getEnergyInWatt(),
                "emUZPrice" => $powerData->getEnergyUnderZero()->getEnergyPriceInCent(),
                "pmSvg" => $powerData->getSavings()->getEnergyInWatt(),
                "pmSvgPrice" => $powerData->getSavings()->getEnergyPriceInCent()
            ];

            $data[] = $dataRow;
        }
        return $data;
    }

    private function prepareDataRangeForEachMonth($year)
    {
        $data = [];
        for ($month = 1; $month <= 12; $month++) {            
            $strStart = date('Y-m-d H:i:s', strtotime("$year-$month-1"." 00:00:00"));
            $strEnd = date('Y-m-d H:i:s', strtotime("$year-$month-".TimeHelper::getDaysInMonth($month, $year)." 23:59:59"));            
            $powerData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd);

            $dataRow = [
                "x-datetime" => date('d.m.Y H:i', strtotime("$year-$month-1"." 00:00:00")),
                "emOZ" => $powerData->getEnergyOverZero()->getEnergyInWatt(),
                "emOZPrice" => $powerData->getEnergyOverZero()->getEnergyPriceInCent(),
                "emUZ" => $powerData->getEnergyUnderZero()->getEnergyInWatt(),
                "emUZPrice" => $powerData->getEnergyUnderZero()->getEnergyPriceInCent(),
                "pmSvg" => $powerData->getSavings()->getEnergyInWatt(),
                "pmSvgPrice" => $powerData->getSavings()->getEnergyPriceInCent()
            ];

            $data[] = $dataRow;
        }
        return $data;
    }

    private function prepareDataRangeForEachYears($firstYear, $lastYear)
    {
        $data = [];        
        for ($year = $firstYear; $year <= $lastYear; $year++) {            
            $strStart = date('Y-m-d H:i:s', strtotime("$year-1-1"." 00:00:00"));
            $strEnd = date('Y-m-d H:i:s', strtotime("$year-12-31"." 23:59:59"));            
            $powerData = $this->hourlyEnergyDataTbl->getEnergyData($strStart, $strEnd);

            $dataRow = [
                "x-datetime" => date('Y-m-d H:i:s', strtotime("$year-1-1"." 00:00:00")),
                "emOZ" => $powerData->getEnergyOverZero()->getEnergyInWatt(),
                "emOZPrice" => $powerData->getEnergyOverZero()->getEnergyPriceInCent(),
                "emUZ" => $powerData->getEnergyUnderZero()->getEnergyInWatt(),
                "emUZPrice" => $powerData->getEnergyUnderZero()->getEnergyPriceInCent(),
                "pmSvg" => $powerData->getSavings()->getEnergyInWatt(),
                "pmSvgPrice" => $powerData->getSavings()->getEnergyPriceInCent()
            ];

            $data[] = $dataRow;
        }
        return $data;
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

    public function getData1()
    {
        return $this->data1;
    }

    public function getData2()
    {
        return $this->data2;
    }

    public function getLabelsTooltip()
    {
        return $this->labelsTooltip;
    }

    public function getLabelsXAxis()
    {
        return $this->labelsXAxis;
    }

    public function getMissingValues1()
    {
        return $this->missingValues1;
    }

    public function getMissingValues2()
    {
        return $this->missingValues2;
    }

    public function getEMOverZeroTotal1()
    {
        return $this->emOverZeroTotal1;
    }

    public function getEMUnderZeroTotal1()
    {
        return $this->emUnderZeroTotal1;
    }

    public function getPMSavingsTotal1()
    {
        return $this->pmSavingsTotal1;
    }

    public function getEMOverZeroTotal2()
    {
        return $this->emOverZeroTotal2;
    }

    public function getEMUnderZeroTotal2()
    {
        return $this->emUnderZeroTotal2;
    }

    public function getPMSavingsTotal2()
    {
        return $this->pmSavingsTotal2;
    }
}
