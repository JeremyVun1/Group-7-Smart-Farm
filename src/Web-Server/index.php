<?php
    include("charting.php");
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
    var createChart = function(containerId, title, chartType="line") {
        var result = new CanvasJS.Chart(containerId, {
            theme: "dark2",
            animationEnabled: true,
            title: {
                text: title
            },
            data: [{
                type: chartType,
                
            }]
        });
    }
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

        /*
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
        */
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
            <?=buildSensorTypeCard("Temperature", $temperature_data)?>
            <?=buildSensorTypeCard("Moisture", $moisture_data)?>
        </div>

        <div class="row p-0 m-0">
            <?=buildSensorTypeCard("Waterlevel", $waterlevel_data)?>
            <?=buildSensorTypeCard("Voltage", $voltage_data)?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="bg-light">
        footer
    </div>
</body>
</html>