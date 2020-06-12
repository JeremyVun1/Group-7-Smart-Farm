<?php

function buildDataArray($datapoints, $count) {
    $result = array();

    foreach ($datapoints as $datapoint) {
        array_push($result, $datapoint->y);
    }

    $result = array_slice($result, 0, $count);
    return $result;
}

function calcCorrelation($datapoints1, $datapoints2) {
    $count = min(count($datapoints1), count($datapoints2));
    $arr1 = buildDataArray($datapoints1, $count);
    $arr2 = buildDataArray($datapoints2, $count);

    $result = stats_stat_correlation($arr1, $arr2);
    if (is_nan($result))
        $result = "";
    else $result = number_format((float)$result, 2, '.', '');
    return $result;
}

function calcCovariance($datapoints1, $datapoints2) {
    $count = min(count($datapoints1), count($datapoints2));
    $arr1 = buildDataArray($datapoints1, $count);
    $arr2 = buildDataArray($datapoints2, $count);

    $result = stats_covariance($arr1, $arr2);
    $result = number_format((float)$result, 2, '.', '');
    return $result;
}

function calcT($datapoints1, $datapoints2) {
    $count = min(count($datapoints1), count($datapoints2));

    /*
    if (count($datapoints1) <=1)
        print_r($datapoints1);
    if (count($datapoints2) <=1)
        print_r($datapoints2);
    */

    $arr1 = buildDataArray($datapoints1, $count);
    $arr2 = buildDataArray($datapoints2, $count);

    $result = stats_stat_independent_t($arr1, $arr2);
    $result = number_format((float)$result, 2, '.', '');
    return $result;
}

function getDataSeriesNamed($sensor_data, $name) {
    foreach($sensor_data as $sensor) {
        $sensorId = $sensor->name;
        if ($sensorId == $name)
            return $sensor;
    }
    return NULL;
}

function buildSensorList($type, $sensor_data, $renderButton) {
    $result = '<table class="table table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Last</th>
                            <th scope="col">R^2</th>
                            <th scope="col">Covar</th>
                            <th scope="col">T-val</th>';

    if ($renderButton)
        $result = $result.'<th scope="col">View</th>';
    
    $result = $result.'</tr>
                    </thead>
                    <tbody>';

    $aggregateDS = getAggregateDataSeries($sensor_data);
    

    foreach ($sensor_data as $sensor) {
        $sensorId = $sensor->name;
        if ($sensorId == "Aggregate" || $sensorId == "Regression")
            continue;

        $sensorIdStr = str_replace(" ", "%", $sensorId);
        $lastReading = end($sensor->dataPoints)->y;

        $r2 = calcCorrelation($sensor->dataPoints, $aggregateDS->dataPoints);
        $covar = calcCovariance($sensor->dataPoints, $aggregateDS->dataPoints);
        //print_r($sensor->dataPoints);
        $t = calcT($sensor->dataPoints, $aggregateDS->dataPoints);

        $result = $result.'<tr><td>'.$sensorId.'</td>';
        $result = $result.'<td>'.$lastReading.'</td>';
        $result = $result.'<td>'.$r2.'</td>';
        $result = $result.'<td>'.$covar.'</td>';
        $result = $result.'<td>'.$t.'</td>';

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

function buildBatteryStatusCard($sensor_data, $id, $error="false") {
    if ($error == "true")
        return;

    $data = NULL;
    foreach($sensor_data as $ds) {
        if ($ds->name == $id) {
            $data = $ds;
        }
    }

    if ($data == NULL)
        return;

    $lastReading = end($data->dataPoints)->y;
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
            $mean = number_format((float)$data->calcYMean(), 2, '.', '');
            $stdDev = number_format((float)$data->calcYVariance(), 2, '.', '');
            $variance = number_format((float)$data->calcStdDev(), 2, '.', '');

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