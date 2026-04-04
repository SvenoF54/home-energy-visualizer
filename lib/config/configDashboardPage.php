<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigDashboardPage
{
    private $zeroFeedInRange = 40;                      // Range in Watt, example -40W to +40W
    private $consumptionIndicatedAs100Percent = 6000;   // Consumption used to calculate the 100% progress bar
    private $maxEnergyProduction = 1800;                // Max Energy production, i.e. from all PV Panels
    private $showZendureOnDashboard = false;            // If Zendure-System is shown
    private $showShellyUniOnDashboard =  false;         // If experimental ShellyUni is shown

    public function toJson()
    {
        return json_encode([            
            'zeroFeedInRange' => $this->zeroFeedInRange,            
        ]);
    }

    public function getZeroFeedInRange() { return $this->zeroFeedInRange; }
    public function setZeroFeedInRange($zeroFeedInRange) { $this->zeroFeedInRange = $zeroFeedInRange;}

    public function getShowZendureOnDashboard() { return $this->showZendureOnDashboard; }
    public function setShowZendureOnDashboard($showZendureOnDashboard) { $this->showZendureOnDashboard = $showZendureOnDashboard; }

    public function getShowShellyUniOnDashboard() { return $this->showShellyUniOnDashboard; }
    public function setShowShellyUniOnDashboard($showShellyUniOnDashboard) { $this->showShellyUniOnDashboard = $showShellyUniOnDashboard; }

    public function getConsumptionIndicatedAs100Percent() { return $this->consumptionIndicatedAs100Percent; }
    public function setConsumptionIndicatedAs100Percent($consumptionIndicatedAs100Percent) { $this->consumptionIndicatedAs100Percent = $consumptionIndicatedAs100Percent; }

    public function getMaxEnergyProduction() { return $this->maxEnergyProduction; }
    public function setMaxEnergyProduction($maxEnergyProduction) { $this->maxEnergyProduction = $maxEnergyProduction; }
}
