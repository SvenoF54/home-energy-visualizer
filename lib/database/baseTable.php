<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class BaseTable {
    protected $pdo;
    protected $error = "";
    protected $tableName = null;
    
    public function __construct($pdo, $tableName)
    {
        $this->pdo = $pdo;
        $this->tableName = $tableName;        
    }
    
    public function getError()
    {
        return $this->error;
    }    
}
