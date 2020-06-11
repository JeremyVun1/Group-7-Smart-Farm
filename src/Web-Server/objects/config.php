<?php

class Config {

    private $conn;
    private $table_name = "config";


    public $id;
    public $moisture_threshold;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    function changeMoistureThreshold($newValue) {
        if ($newValue < 0)
            $newValue = 0;
        if ($newValue > 1024)
            $newValue = 1024;

        $stmt = $this->conn->prepare("UPDATE $this->table_name SET moisture_threshold=$newValue WHERE config_id=1");

        if($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    function getMoistureThreshold() {
        $stmt = "SELECT moisture_threshold FROM $this->table_name WHERE config_id=1";

        $result = $this->conn->query($stmt);
        return $result->fetch_assoc();
    }
}

?>