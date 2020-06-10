<?php

include 'lib/statistical_lines.php';

$lineVoltages = array();
$barVoltages = array();
$params = array();
$range = "";

$voltageRanges = array(
    array("label" => "0-19%", "y" => 0),
    array("label" => "20-39%", "y" => 0),
    array("label" => "40-59%", "y" => 0),
    array("label" => "60-79%", "y" => 0),
    array("label" => "80-100%", "y" => 0)
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
    $getTempsUrl="http://localhost/Group-7-Smart-Farm/src/Web-Server/api/voltage/get_readings.php?".$params;
    curl_setopt($handle, CURLOPT_URL, $getTempsUrl);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($handle);
    curl_close($handle);

    $readings = json_decode($result);

    foreach($readings as $reading) {
        
        $id = $reading->sensor_id;
        $voltage = $reading->voltage;
        $datetime = $reading->datetime;
        $dataPoints = [
            'label' => $datetime,
            'y' => $voltage
        ];
        //Build the line graph data
        if(strpos(json_encode($lineVoltages), $id) == 0) {   //We have a new sensor
            array_push($lineVoltages, 
            [
                'type' => "spline",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => [$dataPoints]
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($lineVoltages as &$v) {
                if($v['name'] == $id) {
                    array_push($v['dataPoints'],$dataPoints);
                }
            }
        }

        //Build the bar graph data
        if(strpos(json_encode($barVoltages), $id) == 0) {   //We have a new sensor
            array_push($barVoltages, 
            [
                'type' => "column",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => $voltageRanges
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($barVoltages as &$v) {
                if($v['name'] == $id) {
                    switch($voltage) {
                        case ($voltage < 200):
                            $v['dataPoints'][0]["y"] += 1;
                        break;
                        case ($voltage < 400):
                            $v['dataPoints'][1]["y"] += 1;
                        break;
                        case ($voltage < 600):
                            $v['dataPoints'][2]["y"] += 1;
                        break;
                        case ($voltage < 800):
                            $v['dataPoints'][3]["y"] += 1;
                        break;
                        case ($voltage <= 1000):
                            $v['dataPoints'][4]["y"] += 1;
                        break;
                    }
                }
            }
        }
    }

    //Generate the statistical lines
    $lineAgrMean = aggregate_mean_line($lineVoltages);
    $lineLinReg = linear_regression_line($lineAgrMean);

    array_push($lineVoltages, $lineAgrMean);
    array_push($lineVoltages, $lineLinReg);

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
            title: "Battery",
            suffix: "",
            maximum: 1000
        },
        toolTip: {
            shared: "true"
        },
        legend:{
            cursor: "pointer",
            itemclick : toggleLineDataSeries
        },
        data: <?php echo json_encode($lineVoltages, JSON_NUMERIC_CHECK); ?>
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
        data: <?php echo json_encode($barVoltages, JSON_NUMERIC_CHECK); ?> 
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
<h1>Battery Levels</h1>
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