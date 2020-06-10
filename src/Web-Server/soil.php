<?php

include 'lib/statistical_lines.php';

$lineMoistures = array();
$barMoistures = array();
$params = array();
$range = "";

$moistureRanges = array(
    array("label" => "0 - 20%", "y" => 0),
    array("label" => "20 - 39%", "y" => 0),
    array("label" => "40 - 59%", "y" => 0),
    array("label" => "60 - 79%", "y" => 0),
    array("label" => "80 - 100%", "y" => 0)
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
    $getTempsUrl="http://localhost/Group-7-Smart-Farm/src/Web-Server/api/soil/get_readings.php?".$params;
    curl_setopt($handle, CURLOPT_URL, $getTempsUrl);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($handle);
    curl_close($handle);

    $readings = json_decode($result);

    foreach($readings as $reading) {
        $id = $reading->sensor_id;
        $moisture = $reading->moisture_level;
        $datetime = $reading->datetime;
        $dataPoints = [
            'label' => $datetime,
            'y' => $moisture
        ];

        //Build the line graph data
        if(strpos(json_encode($lineMoistures), $id) == 0) {   //We have a new sensor
            array_push($lineMoistures, 
            [
                'type' => "spline",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => [$dataPoints]
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($lineMoistures as &$m) {
                if($m['name'] == $id) {
                    array_push($m['dataPoints'],$dataPoints);
                }
            }
        }

        //Build the bar graph data
        if(strpos(json_encode($barMoistures), $id) == 0) {   //We have a new sensor
            array_push($barMoistures, 
            [
                'type' => "column",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => $moistureRanges
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($barMoistures as &$m) {
                if($m['name'] == $id) {
                    switch($moisture) {
                        case ($moisture < 500):
                            $m['dataPoints'][0]["y"] += 1;
                        break;
                        case ($moisture < 650):
                            $m['dataPoints'][1]["y"] += 1;
                        break;
                        case ($moisture < 800):
                            $m['dataPoints'][2]["y"] += 1;
                        break;
                        case ($moisture < 950):
                            $m['dataPoints'][3]["y"] += 1;
                        break;
                        case ($moisture >= 950):
                            $m['dataPoints'][4]["y"] += 1;
                        break;
                    }
                }
            }
        }
    }

    //Generate the statistical lines
    $lineAgrMean = aggregate_mean_line($lineMoistures);
    $lineLinReg = linear_regression_line($lineAgrMean);

    array_push($lineMoistures, $lineAgrMean);
    array_push($lineMoistures, $lineLinReg);

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
            includeZero: false,
            title: "Moisture Level",
            suffix: "",
            maximum: 1100
        },
        toolTip: {
            shared: "true"
        },
        legend:{
            cursor:"pointer",
            itemclick : toggleLineDataSeries
        },
        data: <?php echo json_encode($lineMoistures, JSON_NUMERIC_CHECK); ?>
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
        data: <?php echo json_encode($barMoistures, JSON_NUMERIC_CHECK); ?> 
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
<h1>Soil Moisture Readings</h1>
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