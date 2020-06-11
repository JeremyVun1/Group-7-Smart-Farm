<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../config/database.php";
include_once "../../objects/config.php";

$database = new Database();
$conn = $database->getConnection();

$config = new Config($conn);

$moisture_threshold = $config->getMoistureThreshold();

if(!empty($moisture_threshold)) {
    // set response code - 200 OK
    http_response_code(200);
  
    // deliver results to the user
    echo json_encode($moisture_threshold);
} else {    // if unable to retrieve the reading, tell the user
           
    // set response code - 500 Internal Service Error
    http_response_code(500);

    // tell the user
    echo json_encode(array("message" => "Unable to retrieve configuration from db"));
}

$conn->close();

?>