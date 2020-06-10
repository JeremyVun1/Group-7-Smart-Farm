<?php

/*
 * CONFIG STUFF
 */
$baseUri = "http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/";
date_default_timezone_set('Australia/Melbourne');
$timeRange = "30 days";

function create_temp_labels($temp_step_size) {
    $result = array();

    $i=$temp_step_size;
    while ($i < 100) {
        $temp_arr = array($i => strval($i-$temp_step_size).'-'.strval($i-0.01)."0°C");
        $result = array_merge($result, $temp_arr);
        $i += 5;
    }

    return $result;
}

$temp_step_size = 5;
$temp_labels = create_temp_labels($temp_step_size);


// build request params
$params = array(
    "start" => date('Y-m-d H:i:s', strtotime("-".$timeRange))
);
$params = http_build_query($params);

// Get charting data from API
function getData($api, $valName) {
    $handle = curl_init();
    $url = $baseUri.$api.$params;

    //echo $url;
    curl_setopt($handle, CURLOPT_URL, $getTempsURL);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($handle);
    curl_close($handle);

    $readings = json_decode($response);
    foreach($readings as $r) {
        $val = $r[$valName];
    }


    return $result;
}

//print_r($readings);

foreach($readings as $r) {
    $temp = $r->temperature;
    switch($temp) {
        case ($temp < 0):
            $temperatures[0]["y"] += 1;
        break;
        case ($temp < 5):
            $temperatures[1]["y"] += 1;
        break;
        case ($temp < 10):
            $temperatures[2]["y"] += 1;
        break;
        case ($temp < 15):
            $temperatures[3]["y"] += 1;
        break;
        case ($temp < 20):
            $temperatures[4]["y"] += 1;
        break;
        case ($temp < 25):
            $temperatures[5]["y"] += 1;
        break;
        case ($temp < 30):
            $temperatures[6]["y"] += 1;
        break;
        case ($temp < 35):
            $temperatures[7]["y"] += 1;
        break;
        case ($temp < 40):
            $temperatures[8]["y"] += 1;
        break;
        case ($temp < 45):
            $temperatures[9]["y"] += 1;
        break;
        case ($temp < 50):
            $temperatures[10]["y"] += 1;
        break;
        case ($temp >= 50):
            $temperatures[11]["y"] += 1;
        break;
    }
}


//Get soil moistures
$handle = curl_init();
$getTempsURL = $baseUri."api/soil/get_readings.php?".$params;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

$readings = json_decode($result);
$soilMoistures = array(
    array("label" => "0 - 20%", "y" => 0),
    array("label" => "20 - 39%", "y" => 0),
    array("label" => "40 - 59%", "y" => 0),
    array("label" => "60 - 79%", "y" => 0),
    array("label" => "80 - 100%", "y" => 0)
);
//print_r($readings);

foreach($readings as $r) {
    $moisture = $r->moisture_level;
    switch($moisture) {
        case ($moisture < 500):
            $soilMoistures[0]["y"] += 1;
        break;
        case ($moisture < 650):
            $soilMoistures[1]["y"] += 1;
        break;
        case ($moisture < 800):
            $soilMoistures[2]["y"] += 1;
        break;
        case ($moisture < 950):
            $soilMoistures[3]["y"] += 1;
        break;
        case ($moisture >= 950):
            $soilMoistures[4]["y"] += 1;
        break;
    }
}



//Get water levels
$handle = curl_init();
$getTempsURL = $baseUri."api/water/get_readings.php?".$params;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

$readings = json_decode($result);
$waterLevels = array(
    array("label" => "0 - 20%", "y" => 0),
    array("label" => "20 - 39%", "y" => 0),
    array("label" => "40 - 59%", "y" => 0),
    array("label" => "60 - 79%", "y" => 0),
    array("label" => "80 - 100%", "y" => 0)
);

foreach($readings as $r) {
    $waterLvl = $r->water_level;
    switch($waterLvl) {
        case ($waterLvl < 0):
            $waterLevels[0]["y"] += 1;
        break;
        case ($waterLvl < 5):
            $waterLevels[1]["y"] += 1;
        break;
        case ($waterLvl < 10):
            $waterLevels[2]["y"] += 1;
        break;
        case ($waterLvl < 15):
            $waterLevels[3]["y"] += 1;
        break;
        case ($waterLvl < 20):
            $waterLevels[4]["y"] += 1;
        break;
    }
}

//Get voltage
$handle = curl_init();
$getTempsURL = $baseUri."api/voltage/get_readings.php?".$params;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

$readings = json_decode($result);
$voltages = array(
    array("label" => "0 - 20%", "y" => 0),
    array("label" => "20 - 39%", "y" => 0),
    array("label" => "40 - 59%", "y" => 0),
    array("label" => "60 - 79%", "y" => 0),
    array("label" => "80 - 100%", "y" => 0)
);
//print_r($readings);

foreach($readings as $r) {
    $v = $r->voltage;
    switch($v) {
        case ($v < 200):
            $voltages[0]["y"] += 1;
        break;
        case ($v < 400):
            $voltages[1]["y"] += 1;
        break;
        case ($v < 600):
            $voltages[2]["y"] += 1;
        break;
        case ($v < 800):
            $voltages[3]["y"] += 1;
        break;
        case ($v <= 1000):
            $voltages[4]["y"] += 1;
        break;
    }
}
?>

?>