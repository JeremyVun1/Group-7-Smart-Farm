<?php

function buildSensorList($sensor_rows) {
    $result = '<table class="table table-striped table-hover table-sm">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col">Sensor Id</th>
                            <th scope="col">Last Reading</th>
                            <th scope="col">View</th>
                        </tr>
                    </thead>
                    <tbody>';
    
    foreach ($sensor_rows as $r) {
        $result = $result.'<tr><td>'.$r["sensor_id"].'</td>';
        $result = $result.'<td>'.$r["reading"].'</td>';
        $result = $result.'<td>'.'<a href=sensor.php?id='.$r["sensor_id"].' class="btn-sm btn-primary">View Sensor</a>';
        $result = $result.'</tr>';
    }
    
    $result = $result.'</tbody></table>';

    return $result;
}

function buildSensorTypeCard($sensor_type, $sensor_data) {
    $result = '<div class="col-sm m-2 border border-dark bg-light rounded shadow">
                    <div class="row m-2">
                        <h2 class="sensor-card-title font-weight-bold mx-auto">';
                            $result = $result.$sensor_type.'
                        </h2>
                    </div>
                    <div class="row m-2">
                        <div class="graph" id="tempGraphContainer" style="height: 300px; width: 100%; display: inline-block;"></div>
                    </div>
                    <div class="row m-2">';
                        $result = $result.buildSensorList($sensor_data).
                    '</div>
                    <div class="row>
                        <a href="';
                            $result = $result.$sensor_type.'.php" id="text">Click for more</a>
                    </div>
                </div>';

    return $result;
}

?>