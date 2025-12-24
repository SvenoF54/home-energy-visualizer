<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigZendure
{
    private $displayName = "Akku";
    private $packCapacityInW = 2000;
    private $keyPackCapacityPercent = "electricLevel"; // MQQT Key for electricLevel
    private $keySolarInput = "solarInputPower";        // MQQT Key for solarInputPower 
    private $keyPackMaxUpperLoadLimit = "socSet";      // MQQT Key for max. Charge in %
    private $keyPackDischarge = "packInputPower";      // MQQT Key for pack discharge current W
    private $keyPackCharge = "outputPackPower";        // MQQT Key for pack charge current in W
    private $keyPackState = "packState";               // MQQT Key for pack state (0: Standby, 1: Charge, 2: Discharge)
    private $calculatePackData = false;                // If the pack data are calculated, usefull if smart mode was used
    private $connectedToPmPort = "";                   // PM Port Phase to which Zendure was connected, i.e. PM3

    
    public function getDisplayName() { return $this->displayName; }
    public function setDisplayName($displayName) { $this->displayName = $displayName; }

    public function getPackCapacityInW() { return $this->packCapacityInW; }
    public function setPackCapacityInW($packCapacityInW) { return $this->packCapacityInW = $packCapacityInW; }

    public function getKeyPackCapacityPercent() { return $this->keyPackCapacityPercent; }
    public function setKeyPackCapacityPercent($keyPackCapacityPercent) { $this->keyPackCapacityPercent = $keyPackCapacityPercent; }

    public function getKeySolarInput() { return $this->keySolarInput; }
    public function setKeySolarInput($keySolarInput) { $this->keySolarInput = $keySolarInput; }

    public function getKeyPackMaxUpperLoadLimit() { return $this->keyPackMaxUpperLoadLimit; }
    public function setKeyPackMaxUpperLoadLimit($keyPackMaxUpperLoadLimit) { $this->keyPackMaxUpperLoadLimit = $keyPackMaxUpperLoadLimit; }

    public function getKeyPackDischarge() { return $this->keyPackDischarge; }
    public function setKeyPackDischarge($keyPackDischarge) { $this->keyPackDischarge = $keyPackDischarge; }

    public function getKeyPackCharge() { return $this->keyPackCharge; }
    public function setKeyPackCharge($keyPackCharge) { $this->keyPackCharge = $keyPackCharge; }

    public function getKeyPackState() { return $this->keyPackState; }
    public function setKeyPackState($keyPackState) { $this->keyPackState = $keyPackState; }

    public function getCalculatePackData() { return $this->calculatePackData; }
    public function setCalculatePackData($calculatePackData) { $this->calculatePackData = $calculatePackData; }

    public function getConnectedToPmPort() { return strtolower($this->connectedToPmPort); }
    public function setConnectedToPmPort($connectedToPmPort) { $this->connectedToPmPort = $connectedToPmPort; }
    
}
