<?php

class MissingRowSet {
    private $emMissingRows;
    private $pm1MissingRows;
    private $pm2MissingRows;
    private $pm3MissingRows;
    private $countRows;

    public function __construct($emMissingRows = null, $pm1MissingRows = null, $pm2MissingRows = null, $pm3MissingRows = null, $countRows = null) {
        $this->emMissingRows = $emMissingRows;
        $this->pm1MissingRows = $pm1MissingRows;
        $this->pm2MissingRows = $pm2MissingRows;
        $this->pm3MissingRows = $pm3MissingRows;
        $this->countRows = $countRows;        
    }

    public function getWorstMissingRowCount() {
        $availableValues = [];
    
        if ($this->isEmAvailable()) {
            $availableValues[] = $this->getEmMissingRows();
        }
    
        if ($this->isPm1Available()) {
            $availableValues[] = $this->getPm1MissingRows();
        }
    
        if ($this->isPm2Available()) {
            $availableValues[] = $this->getPm2MissingRows();
        }
    
        if ($this->isPm3Available()) {
            $availableValues[] = $this->getPm3MissingRows();
        }
    
        return !empty($availableValues) ? max($availableValues) : 0;
    }
    
    public function getWorstMissingRowCountPercent() {
        return $this->calcualtePercent($this->getWorstMissingRowCount());
    }

    public function getEmMissingRows() {
        return $this->emMissingRows;
    }

    public function getEmMissingRowsPercent() {
        return $this->calcualtePercent($this->emMissingRows);
    }

    public function isEmAvailable() {
        return $this->emMissingRows < $this->countRows;
    }

    public function getPm1MissingRows() {
        return $this->pm1MissingRows;
    }

    public function getPm1MissingRowsPercent() {
        return $this->calcualtePercent($this->pm1MissingRows);
    }

    public function isPm1Available() {
        return $this->pm1MissingRows < $this->countRows;
    }

    public function getPm2MissingRows() {
        return $this->pm2MissingRows;
    }

    public function isPm2Available() {
        return $this->pm2MissingRows < $this->countRows;
    }

    public function getPm2MissingRowsPercent() {
        return $this->calcualtePercent($this->pm2MissingRows);
    }

    public function getPm3MissingRows() {
        return $this->pm3MissingRows;
    }

    public function isPm3Available() {
        return $this->pm3MissingRows < $this->countRows;
    }

    public function getPm3MissingRowsPercent() {
        return $this->calcualtePercent($this->pm3MissingRows);
    }

    public function getCountRows() {
        return $this->countRows;
    }

    private function calcualtePercent($missingRows)
    {
        if ($this->countRows == 0) return 100;
        return number_format($missingRows / $this->countRows * 100, 2);
    }
}
