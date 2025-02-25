<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigDashboardPage
{
    private $zeroFeedInRange = 40;  // Range in Watt, example -40W to +40W

    public function toJson()
    {
        return json_encode([            
            'zeroFeedInRange' => $this->zeroFeedInRange,            
        ]);
    }

    public function getZeroFeedInRange() { return $this->zeroFeedInRange; }
    public function setZeroFeedInRange($zeroFeedInRange) { $this->zeroFeedInRange = $zeroFeedInRange;}
}
