<?php

date_default_timezone_set('Australia/Melbourne');
$timeRange = "30 days";
$params = array(
    "start" => date('Y-m-d H:i:s', strtotime("-".$timeRange))
);
$params = http_build_query($params,PHP_QUERY_RFC3986);

//Get temperatures
$handle = curl_init();
$getTempsURL="http://localhost/Group-7-Smart-Farm/Web-Server/api/temperature/get_readings.php?".$params;
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
$getTempsURL="http://localhost/Group-7-Smart-Farm/Web-Server/api/soil/get_readings.php?".$params;
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
$getTempsURL="http://localhost/Group-7-Smart-Farm/Web-Server/api/water/get_readings.php?".$params;
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
        case($waterLvl < 25):
            $waterLevels[5]["y"] += 1;
        break;
        case ($waterLvl < 30):
            $waterLevels[6]["y"] += 1;
        break;
        case ($waterLvl < 35):
            $waterLevels[7]["y"] += 1;
        break;
        case ($waterLvl >= 35):
            $waterLevels[8]["y"] += 1;
        break;
    }
}

?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="stylesheet.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>
<script src="lib/canvasjs-2.3.2/canvasjs.min.js"></script>
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

        tempChart.render();
        moistureChart.render();
        waterChart.render();
    }
</script>
   
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 text-center">
            <h1>Smart Farming Control Center</h1>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-4">
            <div id="temperatureContainer" style="height: 300px; width: 100%; display: inline-block;"></div>
            <a href="temperature.php" id="text">Click for more</span>
        </div>
        <div class="col-lg-4">
            <div id="soilMoistureContainer" style="height: 300px; width: 100%; display: inline-block;"></div>
            <a href="soil.php" id="text">Click for more</span>        
        </div>
        <div class="col-lg-4">
            <div id="waterLevelContainer" style="height: 300px; width: 100%; display: inline-block;"></div>
            <a href="water.php" id="text">Click for more</span>
        </div>
    </div>
    <!-- <div class="row">
        <div class="col-md-4 text-center">
            <a href="temperature.php" id="text">Click for more</span>
        </div>
        <div class="col-md-4 text-center">
            <a href="soil.php" id="text">Click for more</span>        
        </div>
        <div class="col-md-4 text-center">
            <a href="water.php" id="text">Click for more</span>
        </div>
    </div> -->
</div>
</body>
</html>