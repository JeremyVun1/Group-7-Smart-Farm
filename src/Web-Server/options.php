<?php
    include "objects/config.php";
    include "config/database.php";

    $success = false;

    if(isset($_POST["changeMoistureThreshold"])) {
        if(!empty($_POST["moistureThresholdValue"]) && is_numeric($_POST["moistureThresholdValue"])) {

            // grab value from the post body
            $moistureThreshold = $_POST["moistureThresholdValue"];

            // constraint between 0 and 1024
            if ($moistureThreshold < 0)
                $moistureThreshold = 0;
            elseif ($moistureThreshold > 1024)
                $moistureThreshold = 1024;

            // make connection and add to database
            $database = new Database();
            $conn = $database->getConnection();
            $config = Config($conn);
            $config->changeMoistureThreshold($moistureThreshold);

            $success = true;
        }
    }
?>

<!-- form controls for changing the moisture threshold -->
<!DOCTYPE HTML>
<html>
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<link rel="stylesheet" href="stylesheet.css">

<!-- libs -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"></script>

<!-- css -->
<link rel="stylesheet" href="/static/styles.css" />
   
</head>
<body>
    <!-- HEADER -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a href="/">
            <span class="navbar-brand mb-0 h1">
                <img src="./static/icon.png" />
                Smart Farm Dashboard
            </span>
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="options.php">Options</a>
                </li>
            </ul>
        </div>
        <span class="navbar-brand mb-0 h1">Group 7 - SWE-30011 IoT Programming</span>
    </nav>

    <!-- CONTENT -->
    <div class="container mx-auto p-2">
        <div class="row">
            <form method="post" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
                <h3>Change the moisture threshold value:</h3>
                <p>Determines if the water pump actuator is turned on and off when a moisture reading is received</p>
                <div class="form-group">
                    <label for="moistureThresholdValue">Moisture Value</label>
                    <input type="text" class="form-control" id="moistureThresholdValue" name="moistureThresholdValue">
                </div>
                <input type="submit" name="changeMoistureThreshold" value="changeMoistureThreshold">
            </form>
        </div>
        <?php
            if ($success) {
                echo "<div class='row'>Value successfully posted!</div>";
            }
        ?>
    </div>

    <!-- FOOTER -->
    <div class="bg-light footer">
        <span>Adam Knox | Jeremy Vun | Henil Patel</span>
        <span>2020</span>
    </div>
</body>
</html>