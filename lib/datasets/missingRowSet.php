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

    public function getEmMissingRows() {
        return $this->emMissingRows;
    }

    public function isEmAvailable() {
        return $this->emMissingRows < $this->countRows;
    }

    public function getPm1MissingRows() {
        return $this->pm1MissingRows;
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

    public function getPm3MissingRows() {
        return $this->pm3MissingRows;
    }

    public function isPm3Available() {
        return $this->pm3MissingRows < $this->countRows;
    }

    public function getCountRows() {
        return $this->countRows;
    }
}
