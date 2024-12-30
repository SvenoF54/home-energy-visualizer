<?php
class Database {
    private $pdo;
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

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
