<?php
include ('database_credentials.php');

class Database {
    public $conn;

    //Get the database connection
    public function getConnection() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->dbname);


        if($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
        return $this->conn;
    }
}

?>