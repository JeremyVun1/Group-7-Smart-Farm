<?php

/*
$baseUri = "http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/";

date_default_timezone_set('Australia/Melbourne');
$timeRange = "30 days";
$params = array(
    "start" => date('Y-m-d H:i:s', strtotime("-".$timeRange))
);
$params = http_build_query($params);

//Get temperatures
$handle = curl_init();
$getTempsURL = $baseUri."api/temperature/get_readings.php?".$params;
//echo $getTempsURL;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

$readings = json_decode($result);

$temperatures = array(
    array("label" => "-0°C", "y" => 0),
    array("label" => "0-4.99°C", "y" => 0),
    array("label" => "5-9.99°C", "y" => 0),
    array("label" => "10-14.99°C", "y" => 0),
    array("label" => "15-19.99°C", "y" => 0),
    array("label" => "20-24.99°C", "y" => 0),
    array("label" => "25-29.99°C", "y" => 0),
    array("label" => "30-34.99°C", "y" => 0),
    array("label" => "35-39.99°C", "y" => 0),
    array("label" => "40-44.99°C", "y" => 0),
    array("label" => "45-49.99°C", "y" => 0),
    array("label" => "50°C+", "y" => 0)
);
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
*/
?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="stylesheet.css">

<!-- libs -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="lib/canvasjs-2.3.2/canvasjs.min.js"></script>

<!-- css -->
<link rel="stylesheet" href="/static/styles.css" />

<script>
    window.onload = function () {
        var tempChart = new CanvasJS.Chart("temperatureContainer", {
            theme: "dark2",
            animationEnabled: true,
            title:{
                text: "Recent Temperature Readings",
            },
            data: [{
                type: "column",
                indexLabel: "{label} : {y}",
                showInLegend: false,
                legendText: "{label} : {y}",
                dataPoints: <?php echo json_encode($temperatures, JSON_NUMERIC_CHECK); ?>
            }] 
        });

        var moistureChart = new CanvasJS.Chart("soilMoistureContainer", {
            theme: "dark2",
            animationEnabled: true,
            title:{
                text: "Recent Soil Moisture Readings",
            },
            data: [{
                type: "column",
                indexLabel: "{label} : {y}",
                showInLegend: false,
                legendText: "{label} : {y}",
                dataPoints: <?php echo json_encode($soilMoistures, JSON_NUMERIC_CHECK); ?>
            }] 
        });

        var waterChart = new CanvasJS.Chart("waterLevelContainer", {
            theme: "dark2",
            animationEnabled: true,
            title:{
                text: "Recent Water Level Readings",
            },
            data: [{
                type: "column",
                indexLabel: "{label} : {y}",
                showInLegend: false,
                legendText: "{label} : {y}",
                dataPoints: <?php echo json_encode($waterLevels, JSON_NUMERIC_CHECK); ?>
            }] 
        });


        var voltageChart = new CanvasJS.Chart("voltageContainer", {
            theme: "dark2",
            animationEnabled: true,
            title:{
                text: "Recent Battery Level Readings",
            },
            data: [{
                type: "column",
                indexLabel: "{label} : {y}",
                showInLegend: false,
                legendText: "{label} : {y}",
                dataPoints: <?php echo json_encode($voltages, JSON_NUMERIC_CHECK); ?>
            }] 
        });

        tempChart.render();
        moistureChart.render();
        waterChart.render();
        voltageChart.render();
    }
</script>
   
</head>
<body>

    <!-- HEADER -->
    <nav class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">
        <a href="/">
            <img src="./static/icon.png" />
            Smart Farm Dashboard
        </a>
            
        </span>
        <span class="navbar-brand mb-0 h1">Group 7 - IoT Programming</span>
    </nav>

    <!-- CONTENT -->
    <div class="container mx-auto p-2">
        <?php
            include('test_data.php');
            include('utility.php');
        ?>
        <div class="row p-0 m-0">
            <?=buildSensorTypeCard("Temperature", $testdata)?>
            <?=buildSensorTypeCard("Moisture", $testdata)?>
        </div>

        <div class="row p-0 m-0">
            <?=buildSensorTypeCard("Waterlevel", $testdata)?>
            <?=buildSensorTypeCard("Voltage", $testdata)?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="bg-light">
        footer
    </div>
</body>
</html>