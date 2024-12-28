<?php
class Database {
    private $pdo;

    public function __construct() {
        $this->connect();
    }

    public function connect()
    {
        $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
        $this->pdo = new PDO($dsn, DB_USER, DB_PASSWORD);    
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);    
    }
  
    public function getPdoConnection() {
        return $this->pdo;
    }
}
?>
