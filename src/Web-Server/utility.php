<?php

function buildSensorList($type, $sensor_data, $renderButton) {
    $result = '<table class="table table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Sensor Id</th>
                            <th scope="col">Last Reading</th>';

    if ($renderButton)
        $result = $result.'<th scope="col">View</th>';
    
    $result = $result.'</tr>
                    </thead>
                    <tbody>';

    foreach ($sensor_data as $sensor) {
        $sensorId = $sensor->name;
        if ($sensorId == "Aggregate" || $sensorId == "Regression")
            continue;

        $sensorIdStr = str_replace(" ", "_", $sensorId);
        $lastReading = end($sensor->dataPoints)->y;
        $result = $result.'<tr><td>'.$sensorId.'</td>';
        $result = $result.'<td>'.$lastReading.'</td>';

        if ($renderButton)
            $result = $result.'<td><a href=sensor_form.php?type='.$type.'&id='.$sensorIdStr.' class="btn-sm btn-primary">View Sensor</a></td>';

        $result = $result.'</tr>';
    }
    
    $result = $result.'</tbody></table>';

    return $result;
}

function buildSensorTypeCard($sensor_type, $sensor_data, $small=true, $renderButton=true) {
    $result = '<div class="';
    if ($small)
        $result = $result.'col-sm';
    else $result = $result.'col';
    
    $result = $result.' m-2 border border-dark bg-light rounded shadow">
                    <div class="row m-2">
                        <h2 class="sensor-card-title font-weight-bold mx-auto">';
                            $result = $result.$sensor_type.'
                        </h2>
                    </div>
                    <div class="row m-2">
                        <div class="graph shadow" id="'.$sensor_type.'ChartContainer" style="height: 300px; width: 100%; display: inline-block;"></div>
                    </div>
                    <div class="row m-2">';
                        $result = $result.buildSensorList($sensor_type, $sensor_data, $renderButton).
                    '</div>
                    <div class="row>
                        <a href="';
                            $result = $result.$sensor_type.'.php" id="text">Click for more</a>
                    </div>
                </div>';

    return $result;
}

function buildBatteryStatusCard($sensor_data, $error="false") {
    if ($error == "true")
        return;

    $lastReading = end($sensor_data->dataPoints->y);
    $batteryState = $lastReading < 400 ? "GOOD" : "LOW BATTERY";
    $batteryState = $lastReading == 0 ? "UNKNOWN" : $batteryState;

    $result = '<div class="col m-2 border border-dark bg-light rounded shadow">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">Battery State</th>
                            <th scope="col">Last Internal Voltage Reading</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>'.$batteryState.'</td>
                            <td>'.$lastReading.'</td>
                        <tr>
                    </tbody>
                </table>
                <div class="graph shadow" id="VoltageChartContainer" style="height: 300px; width: 100%; display: inline-block;"></div>
            </div>';

    return $result;
}

function buildBarChart($data) {
    $result = '<div class="col m-2 border border-dark bg-light rounded shadow">
                    <div id="barChartContainer""></div>
                </div>';
    return $result;
};

function buildStatCard($chartData) {
    foreach ($chartData as $data) {
        if ($data->name == "Aggregate") {
            $mean = $data->calcYMean();
            $stdDev = $data->calcYVariance();
            $variance = $data->calcStdDev();

            $result = '<div class="col m-2 border border-dark bg-light rounded shadow">
                            <table class="table table-striped table-hover table-sm mt-2">
                                <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Statistic</th>
                                        <th scope="col">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Mean</td>
                                        <td>'.$mean.'</td>
                                    </tr>
                                    <tr>
                                        <td>Std Deviation</td>
                                        <td>'.$stdDev.'</td>
                                    </tr>
                                    <tr>
                                        <td>Variance</td>
                                        <td>'.$variance.'</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>';

            return $result;
        }
    }
}

?>