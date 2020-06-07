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

    function getReadings($id, $start, $end) {
        $stmt = "SELECT sensor_id, temperature, datetime FROM $this->table_name";
        $stmt .= " WHERE datetime >= '$start' AND datetime <= '$end'";

        //If user defines an id, search only for that
        if($id != "all") {
            $stmt .= " AND sensor_id = '$id'";
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