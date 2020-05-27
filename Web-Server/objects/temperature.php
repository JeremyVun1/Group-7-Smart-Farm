<?php

class Temperature {

    private $conn;
    private $table_name = "temperature";


    public $id;
    public $reading;
    public $datetime;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function addReading() {
        $stmt = $this->conn->prepare("INSERT INTO $this->table_name (sensor_id, temperature, datetime) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $this->id, $this->reading, $this->datetime);

        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function getReadings($start, $end) {
        if(!empty($start) && !empty($end)) {
            $stmt = "SELECT * FROM $this->table_name WHERE datetime >= '$start' AND datetime <= '$end'";
        } else {
            //empty arguments, get all records
            $stmt = "SELECT * FROM $this->table_name";
        }

        $result = $this->conn->query($stmt);

        $readings = array();

        if($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $readings[] = $row;
            }
        }
        return $readings;
    }
}

?>