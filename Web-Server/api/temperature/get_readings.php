<?php

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../config/database.php";
include_once "../../objects/temperature.php";

$database = new Database();
$conn = $database->getConnection();

$temp = new Temperature($conn);

$start = $_GET["sdt"];
$end = $_GET["edt"];

$readings = $temp->getReadings($start, $end);

if(!empty($readings)) {
    // set response code - 200 OK
    http_response_code(200);
  
    // deliver results to the user
    echo json_encode($readings);

} else {    // if unable to retrieve the reading, tell the user
           
    // set response code - 503 Internal Service Error
    http_response_code(500);

    // tell the user
    echo json_encode(array("message" => "Unable to retrieve temperature readings"));
}

$conn->close();

?>