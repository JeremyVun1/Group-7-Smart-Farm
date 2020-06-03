<?php

date_default_timezone_set('Australia/Melbourne');
$params = array(
    "start" => date('Y-m-d H:i:s', strtotime('-60 minutes'))
);
$params = http_build_query($params);

//Get temperatures
$handle = curl_init();
$getTempsURL="http://localhost/Group-7-Smart-Farm/Web-Server/api/temperature/get_readings?".$params;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$temperatures = curl_exec($handle);
curl_close($handle);

//Get soil moistures
$handle = curl_init();
$getTempsURL="http://localhost/Group-7-Smart-Farm/Web-Server/api/soil/get_readings?".$params;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

//Get water levels
$handle = curl_init();
$getTempsURL="http://localhost/Group-7-Smart-Farm/Web-Server/api/water/get_readings?".$params;
curl_setopt($handle, CURLOPT_URL, $getTempsURL);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($handle);
curl_close($handle);

?>

<!DOCTYPE HTML>
<html>
<head>
<script>
    window.onload = function () {

    var chart = new CanvasJS.Chart("chartContainer", {
	theme: "dark2",
  	animationEnabled: true,
	title:{
		text: "Email Categories",
		horizontalAlign: "left"
	},
    data:  <?php echo json_encode($temperatures, JSON_NUMERIC_CHECK); ?>
    });

    chart.render();
</script>
</head>
<body>
<h1>Smart Farming Control Center</h1>
<br><br>
<div id="temperatureDoughnutContainer" style="height: 370px; width: 100%;"></div>
<div id="temperatureDoughnutContainer" style="height: 370px; width: 100%;"></div>
<div id="temperatureDoughnutContainer" style="height: 370px; width: 100%;"></div>
<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
</body>
</html>