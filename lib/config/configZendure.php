<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class ConfigZendure
{
    private $displayName = "Akku";
    private $connectedToPmPort = "";                    // PM Port Phase to which Zendure was connected, i.e. PM3
    private $tempOffset = 1920;                         // Offset, which will be subtracted from internal sensor temperature to get neary outside temp.

    
    public function getDisplayName() { return $this->displayName; }
    public function setDisplayName($displayName) { $this->displayName = $displayName; }

    public function getTempOffset() { return $this->tempOffset; }
    public function setTempOffset($tempOffset) { return $this->tempOffset = $tempOffset; }

    public function getConnectedToPmPort() { return strtolower($this->connectedToPmPort); }
    public function setConnectedToPmPort($connectedToPmPort) { $this->connectedToPmPort = $connectedToPmPort; }
    
}
