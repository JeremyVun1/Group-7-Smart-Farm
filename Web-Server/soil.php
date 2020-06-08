<?php

$moistures = array();

if(isset($_POST['search'])) {
    
    $params = array();

    if(!empty($_POST['startDatetime'])) {
       $start = new DateTime($_POST['startDatetime']);
       $params += array("start" => $start->format('Y-m-d H:i:s'));
    } 
    if(!empty($_POST['endDatetime'])) {
        $end = new DateTime($_POST['endDatetime']);
        $params += array("end" => $end->format('Y-m-d H:i:s'));
    }    

    $params = http_build_query($params);
    $handle = curl_init();
    $getTempsUrl="http://localhost/Group-7-Smart-Farm/Web-Server/api/soil/get_readings.php?".$params;
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
            'label' => $reading->datetime,
            'y' => $reading->moisture_level
        ];
        if(strpos(json_encode($moistures), $id) == 0) {   //We have a new sensor
            array_push($moistures, 
            [
                'type' => "spline",
                'showInLegend' => true,
                'name' => $id,
                'dataPoints' => [$dataPoints]
            ]);
        } else {
            // Key alread exists, add the reading to that keys dataset
            foreach($moistures as &$m) {
                if($m['name'] == $id) {
                    array_push($m['dataPoints'],$dataPoints);
                }
            }
        }
    }
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

var chart = new CanvasJS.Chart("chartContainer", {
	theme:"dark2",
	animationEnabled: true,
	title:{
		text: "Soil Moisture Readings"
	},
	axisY :{
		includeZero: false,
		title: "Moisture Level",
		suffix: "",
        maximum: 1024
	},
	toolTip: {
		shared: "true"
	},
	legend:{
		cursor:"pointer",
		itemclick : toggleDataSeries
	},
	data: <?php echo json_encode($moistures, JSON_NUMERIC_CHECK); ?>
});
chart.render();

function toggleDataSeries(e) {
	if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible ){
		e.dataSeries.visible = false;
	} else {
		e.dataSeries.visible = true;
	}
	chart.render();
}

}    
</script>
</head>
<body>

<div id="chartContainer" style="display:<?php echo $display;?>; height: 300px; width: 100%;"></div>

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