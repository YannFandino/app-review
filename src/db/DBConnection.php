<?php
class DBConnection {
    const DB_HOST = 'localhost';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_NAME = 'app_review';
    private $conn;

    /**
     * DBConnection constructor.
     * @param $conn
     */
    public function __construct() {
        $this->conn = new PDO('mysql:host=' . self::DB_HOST . ';dbname=' . self::DB_NAME,
                        self::DB_USER, self::DB_PASS);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $this->conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES 'utf8'");
        return $this->conn;
    }

    public function getConn() {
        return $this->conn;
    }
}