<?php 

include '../../lib/phpMQTTpub.php';

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../config/database.php";
include_once "../../objects/config.php";

$database = new Database();
$conn = $database->getConnection();

$config = new Config($conn);

$data = json_decode(file_get_contents('php://input'));

if (
    //Make sure we have values!
    !empty($data->moisture_threshold)
) {
    //add the reading
    if ($config.changeMoistureThreshold($data->moisture_threshold)) {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "Moisture threshold was updated"));
    } else {    // if unable to enter the reading, tell the user
               
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to update the moisture threshold"));
    }    
} else {    // tell the user data is incomplete
    
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to update the omisture threshold. Data is incomplete."));
}

$conn->close();

?>