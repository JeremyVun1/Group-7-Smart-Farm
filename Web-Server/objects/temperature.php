<?php

class Temperature {

    private $conn;
    private $table_name = "temperature";


    public $id;
    public $reading;
    public $datetime;

    public function __construct($db) {
        $this->conn = $db;
    }

    function addReading() {
        $stmt = $this->conn->prepare("INSERT INTO " . $this->table_name . " (sensor_id, temperature, datetime) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->id, $this->reading, $this->datetime);

        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }
}

?>