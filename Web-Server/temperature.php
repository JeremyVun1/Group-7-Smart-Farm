<?php

//Get ALL the data
$handle = curl_init();
$getAllUrl="http://localhost/Group-7-Smart-Farm/Web-Server/api/temperature/get_readings.php?";
curl_setopt($handle, CURLOPT_URL, $getAllUrl);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

$allReadings = json_decode($result);

$temperatures = array();

foreach($allReadings as $reading) {
    $id = $reading->sensor_id;
    $temperature = $reading->temperature;
    $datetime = $reading->datetime;
    $dataPoints = [
        'label' => $reading->datetime,
        'y' => $reading->temperature
    ];
    if(strpos(json_encode($temperatures), $id) == 0) {   //We have a new sensor
        array_push($temperatures, 
        [
            'type' => "spline",
            'showInLegend' => true,
            'name' => $id,
            'dataPoints' => [$dataPoints]
        ]);
    } else {
        // Key alread exists, add the reading to that keys dataset
        foreach($temperatures as &$temp) {
            if($temp['name'] == $id) {
                array_push($temp['dataPoints'],$dataPoints);
            }
        }
    }
}


?>

<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="stylesheet.css">
<script>
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
	theme:"dark2",
	animationEnabled: true,
	title:{
		text: "Temperatures"
	},
	axisY :{
		includeZero: false,
		title: "Temp",
		suffix: " degrees Celcius"
	},
	toolTip: {
		shared: "true"
	},
	legend:{
		cursor:"pointer",
		itemclick : toggleDataSeries
	},
	data: <?php echo json_encode($temperatures, JSON_NUMERIC_CHECK); ?>
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

<div id="chartContainer" style="height: 300px; width: 100%;"></div>
<script src="lib/canvasjs-2.3.2/canvasjs.min.js"></script>
</body>
</html>