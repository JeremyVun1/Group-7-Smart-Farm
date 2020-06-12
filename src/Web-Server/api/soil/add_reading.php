<?php 

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once "../../config/database.php";
include_once "../../objects/soil.php";
include_once "../../objects/config.php";

include '../../lib/phpMQTTpub.php';

$database = new Database();
$conn = $database->getConnection();

$soil = new Soil($conn);
$config = new Config($conn);

$data = json_decode(file_get_contents('php://input'));

if (
    //Make sure we have values!
    !empty($data->id) &&
    (!empty($data->reading) || $data->reading == "0")
) {
    //add the reading
    $soil->id = $data->id;
    $soil->reading = $data->reading;
    date_default_timezone_set('Australia/Melbourne');
    $soil->datetime = date("Y-m-d H:i:s");

    if($soil->addReading()) {
        // set response code - 201 created
        http_response_code(201);
  
        // tell the user
        echo json_encode(array("message" => "Soil moisture reading was entered"));

        //trigger pump
        trigger_pump($soil->reading);

    } else {    // if unable to enter the reading, tell the user
               
        // set response code - 503 service unavailable
        http_response_code(503);
  
        // tell the user
        echo json_encode(array("message" => "Unable to enter soil moisture reading"));
    }    
} else {    // tell the user data is incomplete
    
    // set response code - 400 bad request
    http_response_code(400);
  
    // tell the user
    echo json_encode(array("message" => "Unable to enter soil moisture reading. Data is incomplete."));
}

$conn->close();

// TODO - get the trheshold from DB
function trigger_pump($reading) {
    $handle = curl_init();
    $getApi = "http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/api/config/get_moisture_threshold.php";
    curl_setopt($handle, CURLOPT_URL, $getApi);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($handle);
    curl_close($handle);

    $response = json_decode($result);
    $threshold = $response->moisture_threshold;

    if($reading <= $threshold) {   //Turn pump on
        mqtt_publish("p",1);
    } elseif($reading > $threshold) {  //Turn the pump off
        mqtt_publish("p",0);
    }
}

?>