<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class RealTimeEnergyDataUnifier  {
    private $hourlyEnergyDataTbl;
    private $realTimeEnergyDataTbl;
    private $energyPriceTbl;
    private $defaultOutCentPricePerWh;
    private $defaultInCentPricePerWh;

    public function __construct($hourlyEnergyDataTbl, $realTimeEnergyDataTbl, $energyPriceTbl, $defaultOutCentPricePerWh, $defaultInCentPricePerWh)
    {
        $this->hourlyEnergyDataTbl = $hourlyEnergyDataTbl;
        $this->realTimeEnergyDataTbl = $realTimeEnergyDataTbl;
        $this->energyPriceTbl = $energyPriceTbl;
        $this->defaultOutCentPricePerWh = $defaultOutCentPricePerWh;
        $this->defaultOutCentPricePerWh = $defaultInCentPricePerWh;
    }

    public function unifyDataForTimeRange($startDateTime, $endDateTime)
    {
        $count = 0;
        $startDateTime = is_string($startDateTime) ? new DateTime($startDateTime) : $startDateTime;
        $endDateTime = is_string($endDateTime) ? new DateTime($endDateTime) : $endDateTime;
        
        // Normalize the start time for the next 15 minutes
        $minute = (int)$startDateTime->format('i');
        $adjustMinutes = 15 - ($minute % 15);
        if ($adjustMinutes !== 15) { // Wenn bereits auf einem 15-Minuten-Schritt, nichts tun
            $startDateTime->modify('+' . $adjustMinutes . ' minutes');
        }
        // Set seconds explizit on 0 or 59
        $startDateTime->setTime((int)$startDateTime->format('H'), (int)$startDateTime->format('i'), 0);
        $endDateTime->setTime((int)$endDateTime->format('H'), (int)$endDateTime->format('i'), 59);
        
        $count = 0;
        for ($time = clone $startDateTime; $time < $endDateTime; $time->modify('+15 minutes')) {
            $this->unifyData($time);
            $count++;
        }
        
        return $count;
    }

    public function unifyData($startDateTime)
    {
        $quarterHourInterval = 1;
        $curStart = is_string($startDateTime) ? new DateTime($startDateTime) : $startDateTime;
        $curEnd = clone $curStart;
        $minutesToAdd = ($quarterHourInterval * 15) -1;
        $curEnd->modify("+{$minutesToAdd} minutes");
        $priceForTimeRow = $this->energyPriceTbl->getPriceForDateTime($curStart);
        
        $referenceDate = new DateTime('2020-01-01');
        if (($curStart < $referenceDate) || ($curEnd < $referenceDate)) {
            return;
        }

        $outCentPricePerWh = $priceForTimeRow ? $priceForTimeRow->getOutCentPricePerWh() : $this->defaultOutCentPricePerWh;        
        $inCentPricePerWh = $priceForTimeRow ? $priceForTimeRow->getInCentPricePerWh() : $this->defaultInCentPricePerWh;        
        $realTimeData = $this->realTimeEnergyDataTbl->getOverviewData(
            $curStart->format('Y-m-d H:i:00'), 
            $curEnd->format('Y-m-d H:i:59'),
            $quarterHourInterval * 900);
        if (sizeof($realTimeData) > 0) {
            $firstRow = $realTimeData[0];            

            $resultOk = $this->hourlyEnergyDataTbl->InsertOrUpdate(
                $curStart->format('Y-m-d H:i:00'), 
                $curEnd->format('Y-m-d H:i:59'),
                $quarterHourInterval, 
                $outCentPricePerWh,
                $inCentPricePerWh,
                $firstRow
            );
            
            if (! $resultOk) {
                print $this->hourlyEnergyDataTbl->getError();
            }
        }
    }
}
