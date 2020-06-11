<?php
    $type = $_GET["type"];
    $id = $_GET["id"];
?>

<!DOCTYPE HTML>
<html>
<head>
    <link rel="stylesheet" href="stylesheet.css">
    <script src="lib/canvasjs-2.3.2/canvasjs.min.js"></script>
</head>
<body>
    <h1><?php echo $type ?></h1>

    <div id="barChartContainer" style="display:<?php echo $display;?>; height: 300px; width: 100%;"></div>

    <form method="post" action="sensor.php">
        <h3>Please enter a datetime range you wish to search for:</h3>
        <br>
        Start:
        <input type="datetime-local" name="startDatetime">
        &nbsp; End:
        <input type="datetime-local" name="endDatetime">

        <input type="hidden" name="type" value=<?php echo $type ?>>
        <input type="hidden" name="id" value=<?php echo $id ?>>
        <br><br>
        <input type="submit" name="search" value="Search">
    </form>

    <br>
    <input type="button" onclick="location.href='index.php'" value="Home"></button>

</body>
</html>