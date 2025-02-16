<?php
// NrgHomeVis - Energievisualisierung für zu Hause | Repository: <https://github.com/SvenoF54/home-energy-visualizer>
// Licensed under the GNU GPL v3.0 - see <https://www.gnu.org/licenses/gpl-3.0.en.html>

class TableStatisticSet 
{
    private $totalRows;
    private $firstRowDate;
    private $lastRowDate;

    public function __construct($totalRows = 0, $firstRowDate = null, $lastRowDate = null) 
    {
        $this->totalRows = $totalRows;
        $this->firstRowDate = $firstRowDate;
        $this->lastRowDate = $lastRowDate;
    }

    public function getTotalRows()
    {
        return $this->totalRows;
    }

    public function setTotalRows($totalRows)
    {
        $this->totalRows = $totalRows;
    }

    public function getFirstRowDate()
    {
        return $this->firstRowDate;
    }

    public function setFirstRowDate($firstRowDate)
    {
        $this->firstRowDate = $firstRowDate;
    }

    public function getLastRowDate()
    {
        return $this->lastRowDate;
    }

    public function setLastRowDate($lastRowDate)
    {
        $this->lastRowDate = $lastRowDate;
    }
}
