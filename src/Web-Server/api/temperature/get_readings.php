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

$id = !empty($_GET["id"])?$_GET["id"]:"all";

date_default_timezone_set('Australia/Melbourne');
$start = !empty($_GET["start"])?$_GET["start"]:date("Y-m-d H:i:s",mktime(0,0,0,5,1,2020));  //Default to implementation date. There will be no records before this date
$end = !empty($_GET["end"])?$_GET["end"]:date("Y-m-d H:i:s");   //Default to now.

$readings = $temp->getReadings($id, $start, $end);

if(!empty($readings)) {
    // set response code - 200 OK
    http_response_code(200);
  
    // deliver results to the user
    echo json_encode($readings);

} else {    // if unable to retrieve the reading, tell the user
           
    // set response code - 500 Internal Service Error
    http_response_code(500);

    // tell the user
    echo json_encode(array("message" => "Unable to retrieve temperature readings"));
}

$conn->close();

?>