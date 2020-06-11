<?php
    include("charting.php");

    $params = array();
    $range = "";
    $voltageError = "false";

    // get post form parameters
    if(isset($_POST['search'])) {
        $type = $_POST['type'];
        $id = $_POST['id'];
        $id = str_replace("_", " ", $id);
        $params += array("id" => $id);

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

        $display = "block";
    } else {
        $display = "none";
    }

    $params = http_build_query($params);
    $chartData;
    $error = "false";

    if ($type && $id) {
        switch($type) {
            case "Temperature":
                $chartData = getData("api/temperature/get_readings.php?".$params, "temperature");
                break;
            case "Moisture":
                $chartData = getData("api/soil/get_readings.php?".$params, "moisture_level");
                break;
            case "WaterLevel":
                $chartData = getData("api/water/get_readings.php?".$params, "water_level");
                break;
        }

        //$voltageChartData = getData("api/voltage/get_readings.php?".$params, "voltage");
    } else {
        $error = "true";
        // set response code - 400 bad request
        http_response_code(400);
  
        // tell the user
        echo json_encode(array("message" => "Unable to enter soil moisture reading. Data is incomplete."));
    }
    
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
    var createChart = function(containerId, title, data, chartType="line") {
        var result = new CanvasJS.Chart(containerId, {
            theme: "dark2",
            animationEnabled: true,
            title: {
                text: title
            },
            data: data
        });

        return result;
    }

    window.onload = function () {
        if (<?php echo $error; ?> == "true")
            return;

        var chartData = createChart("ChartContainer", "", <?php echo json_encode($chartData, JSON_NUMERIC_CHECK); ?>, "line");
        chartData.render();

        //var voltageData = createChart("VoltageChartContainer", "<?php //echo $type; ?>", <?php //echo json_encode($voltageChartData, JSON_NUMERIC_CHECK); ?>, "line");
        //voltageData.render();
    }
</script>
   
</head>
<body style="display=<?php $display ?>">

    <!-- HEADER -->
    <nav class="navbar navbar-light bg-light">
        <span class="navbar-brand mb-0 h1">
        <a href="/">
            <img src="./static/icon.png" />
            <?php echo "[".$type."] ".$id; ?>
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
            <?=buildSensorTypeCard("", $chartData, false, false)?>
        </div>

        <div class="row p-0 m-0">
            <?=buildStatCard($chartData)?>
        </div>

        <div class="row p-0 m-0">
            <?=buildBatteryStatusCard($voltageChartData)?>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="bg-light">
        footer
    </div>
</body>
</html>