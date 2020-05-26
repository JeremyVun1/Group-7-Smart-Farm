<?php
include ('database_credentials.php');

class Database {
    public $conn;

    //Get the database connection
    public function getConnection() {
        $this->conn = new mysqli($GLOBALS["host"], $GLOBALS["username"], $GLOBALS["password"], $GLOBALS["dbname"]);


        if($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}

?>