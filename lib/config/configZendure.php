<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigZendure
{
    private $appKey;
    private $appSecret;
    private $server = "mqtt-eu.zen-iot.com";
    private $port = 1883;
    private $readTimeInSec = 50; 
    private $displayName = "Akku";
    private $keyAkkuCapacity = "electricLevel";     // MQQT Key for electricLevel
    private $keySolarInput = "solarInputPower";     // MQQT Key for solarInputPower 
    private $keyMaxUpperLoadLimit = "socSet";       // MQQT Key for max. Charge in %
    private $keyPackDischarge = "packInputPower";   // MQQT Key for pack discharge current W
    private $keyPackCharge = "outputPackPower";     // MQQT Key for pack charge current in W
    private $keyPackState = "packState";            // MQQT Key for pack state (0: Standby, 1: Charge, 2: Discharge)
    private $calculatePackData = false;             // If the pack data are calculated, usefull if smart mode was used
    private $connectedToPmPort = "";                // PM Port Phase to which Zendure was connected, i.e. PM3

    public function getAppKey() { return $this->appKey; }
    public function setAppKey($appKey) { $this->appKey = $appKey; }

    public function getAppSecret() { return $this->appSecret; }
    public function setAppSecret($appSecret) { $this->appSecret = $appSecret; }

    public function getServer() { return $this->server; }
    public function setServer($server) { $this->server = $server; }

    public function getPort() { return $this->port; }
    public function setPort($port) { $this->port = $port; }

    public function getReadTimeInSec() { return $this->readTimeInSec; }
    public function setReadTimeInSec($readTimeInSec) { $this->readTimeInSec = $readTimeInSec; }

    public function getDisplayName() { return $this->displayName; }
    public function setDisplayName($displayName) { $this->displayName = $displayName; }

    public function getKeyAkkuCapacity() { return $this->keyAkkuCapacity; }
    public function setKeyAkkuCapacity($keyAkkuCapacity) { $this->keyAkkuCapacity = $keyAkkuCapacity; }

    public function getKeySolarInput() { return $this->keySolarInput; }
    public function setKeySolarInput($keySolarInput) { $this->keySolarInput = $keySolarInput; }

    public function getKeyMaxUpperLoadLimit() { return $this->keyMaxUpperLoadLimit; }
    public function setKeyMaxUpperLoadLimit($keyMaxUpperLoadLimit) { $this->keyMaxUpperLoadLimit = $keyMaxUpperLoadLimit; }

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
