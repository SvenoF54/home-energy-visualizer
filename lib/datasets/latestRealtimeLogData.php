<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class LatestRealtimeLogData
{
    public $latestEnergyData = array();

    public function __construct($em, $pm1, $pm2, $pm3)
    {
        $this->latestEnergyData = [
            EnergyTypeEnum::EM->value => $em,
            EnergyTypeEnum::PM1->value => $pm1,
            EnergyTypeEnum::PM2->value => $pm2,
            EnergyTypeEnum::PM3->value => $pm3
        ];
    }

    public function hasEnergyData(EnergyTypeEnum $energyType, $timespanMinutes) {
        if ($this->latestEnergyData[$energyType->value] == null) {
            return true;
        }

        return strtotime($this->latestEnergyData[$energyType->value]) > (time() - $timespanMinutes * 60);
    }
}
