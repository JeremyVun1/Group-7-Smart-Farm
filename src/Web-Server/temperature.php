<?php

include 'lib/statistical_lines.php';

$lineTemperatures = array();
$barTemperatures = array();
$params = array();
$range = "";

$temperatureRanges = array(
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

if(isset($_POST['search'])) {

    if(!empty($_POST['startDatetime'])) {
        $start = new DateTime($_POST['startDatetime']);
        $params += array("start" => $start->format('Y-m-d H:i:s'));
     } 
     if(!empty($_POST['endDatetime'])) {
         $end = new DateTime($_POST['endDatetime']);
         $params += array("end" => $end->format('Y-m-d H:i:s'));
         if(isset($params['start'])) {
             $range .= $params['start'] . "  to  " . $params['end'];
         } else {
             $range .= "Everything until " . $params['end']; 
         }
        } else {
            if(isset($params['start'])) $range = "Everything after " . $params['start'];
        }     
 
     if(empty($_POST['startDatetime']) && empty($_POST['endDatetime'])) {
         $range = "All Records";
     }

    $params = http_build_query($params);
    $handle = curl_init();
    $getTempsUrl="http://localhost/Group-7-Smart-Farm/src/Web-Server/api/temperature/get_readings.php?".$params;
    curl_setopt($handle, CURLOPT_URL, $getTempsUrl);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($handle);
    curl_close($handle);

    $readings = json_decode($result);

    foreach($readings as $reading) {
        
        $id = $reading->sensor_id;
        $temperature = $reading->temperature;
        $datetime = $reading->datetime;
        $dataPoints = [
            'label' => $datetime,
            'y' => $temperature
        ];
        //Build the line graph data
        if(strpos(json_encode($lineTemperatures), $id) == 0) {   //We have a new sensor
            array_push($lineTemperatures, 
            [
                'type' => "spline",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => [$dataPoints]
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($lineTemperatures as &$temp) {
                if($temp['name'] == $id) {
                    array_push($temp['dataPoints'],$dataPoints);
                }
            }
        }

        //Build the bar graph data
        if(strpos(json_encode($barTemperatures), $id) == 0) {   //We have a new sensor
            array_push($barTemperatures, 
            [
                'type' => "column",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => $temperatureRanges
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($barTemperatures as &$temp) {
                if($temp['name'] == $id) {
                    switch($temperature) {
                        case ($temperature < 0):
                            $temp['dataPoints'][0]["y"] += 1;
                        break;
                        case ($temperature < 5):
                            $temp['dataPoints'][1]["y"] += 1;
                        break;
                        case ($temperature < 10):
                            $temp['dataPoints'][2]["y"] += 1;
                        break;
                        case ($temperature < 15):
                            $temp['dataPoints'][3]["y"] += 1;
                        break;
                        case ($temperature < 20):
                            $temp['dataPoints'][4]["y"] += 1;
                        break;
                        case ($temperature < 25):
                            $temp['dataPoints'][5]["y"] += 1;
                        break;
                        case ($temperature < 30):
                            $temp['dataPoints'][6]["y"] += 1;
                        break;
                        case ($temperature < 35):
                            $temp['dataPoints'][7]["y"] += 1;
                        break;
                        case ($temperature < 40):
                            $temp['dataPoints'][8]["y"] += 1;
                        break;
                        case ($temperature < 45):
                            $temp['dataPoints'][9]["y"] += 1;
                        break;
                        case ($temperature < 50):
                            $temp['dataPoints'][10]["y"] += 1;
                        break;
                        case ($temperature >= 50):
                            $temp['dataPoints'][11]["y"] += 1;
                        break;
                    }
                }
            }
        }
    }

    //Generate the statistical lines
    $lineAgrMean = aggregate_mean_line($lineTemperatures);
    $lineLinReg = linear_regression_line($lineAgrMean);

    array_push($lineTemperatures, $lineAgrMean);
    array_push($lineTemperatures, $lineLinReg);

    $display = "block";
} else {
    $display = "none";
}


?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="stylesheet.css">
<script src="lib/canvasjs-2.3.2/canvasjs.min.js"></script>
<script>
window.onload = function () {
    var lineChart = new CanvasJS.Chart("lineChartContainer", {
        theme:"dark2",
        animationEnabled: true,
        title:{
            text: ""
        },
        axisY :{
            title: "Temp",
            suffix: "°C"
        },
        toolTip: {
            shared: "true"
        },
        legend:{
            cursor: "pointer",
            itemclick : toggleLineDataSeries
        },
        data: <?php echo json_encode($lineTemperatures, JSON_NUMERIC_CHECK); ?>
    });

    var barChart = new CanvasJS.Chart("barChartContainer", {
        theme: "dark2",
        animationEnabled: true,
        title:{
            text: "",
        },
        legend:{
            cursor: "pointer",
            itemclick : toggleBarDataSeries
        },
        data: <?php echo json_encode($barTemperatures, JSON_NUMERIC_CHECK); ?> 
    });

    lineChart.render();
    barChart.render();

    function toggleLineDataSeries(e) {
        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
            e.dataSeries.visible = false;
        } else {
            e.dataSeries.visible = true;
        }
        lineChart.render();
    }

    function toggleBarDataSeries(e) {
        if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
            e.dataSeries.visible = false;
        } else {
            e.dataSeries.visible = true;
        }
        barChart.render();
    }
}    
</script>
</head>
<body>
<h1>Temperatures</h1>
<br>
<h2 style="display:<?php echo $display;?>;">Search Range: <?php echo $range; ?></h2>

<div id="lineChartContainer" style="display:<?php echo $display;?>; height: 300px; width: 100%;"></div>
<br><br>
<div id="barChartContainer" style="display:<?php echo $display;?>; height: 300px; width: 100%;"></div>

<form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
    <h3>Please enter a datetime range you wish to search for:</h3>
    <br>
    Start:
    <input type="datetime-local" name="startDatetime">
    &nbsp; End:
    <input type="datetime-local" name="endDatetime">
    <br><br>
    <input type="submit" name="search" value="Search">
</form>

<br>
<input type="button" onclick="location.href='index.php'" value="Home"></button>

</body>
</html>