<?php
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
