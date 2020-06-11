<?php

    include 'lib/models.php';

    // produce array(DataSeries)
    function groupBySensorId($response, $valFieldName) {

        // group data records by sensor id
        $sensorIdGroups = [];
        foreach ($response as $r) {
            // create k,v if it doesn't exist
            if (!key_exists($r->sensor_id, $sensorIdGroups)) {
                $x = array($r->sensor_id => array());
                $sensorIdGroups = array_merge($sensorIdGroups, $x);
            }

            array_push($sensorIdGroups[$r->sensor_id], $r);
        }

        // produce dataseries from grouped data records
        $result = [];
        
        foreach(array_keys($sensorIdGroups) as $sensorId) {
            $records = $sensorIdGroups[$sensorId];
            $datapoints = [];

            $i = 0;
            foreach($records as $record) {
                $dp = new DataPoint($i, $record->$valFieldName);
                array_push($datapoints, $dp);
                $i += 1;
            }

            array_push($result, new DataSeries($sensorId, "line", $datapoints));
        }

        return $result;
    }

    // Get charting data from API
    function getSensorData($api, $valFieldName) {
        // Make RESTful Request
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, $api);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($handle);
        curl_close($handle);

        $obj = json_decode($response);

        // group response objects together by sensor_id
        $result = groupBySensorId($obj, $valFieldName);

        return $result;
    }

    // create a new data series as an aggregate of all the different sensors
    function getAggregateDataSeries($dataseries) {
        if ($dataseries == NULL)
            return NULL;

        $result = unserialize(serialize($dataseries[0]));

        foreach($dataseries as $ds) {
            // roll in the data series into the result aggregate average dataseries
            $i = 0;
            foreach($ds->dataPoints as $dp) {
                if ($i >= count($result->dataPoints)) {
                    array_push($result->dataPoints, new DataPoint($i, $dp->y));
                } else {
                    $result->dataPoints[$i]->x = rollingAverage($result->dataPoints[$i]->x, $dp->x, $result->dataPoints[$i]->count);
                    $result->dataPoints[$i]->y = rollingAverage($result->dataPoints[$i]->y, $dp->y, $result->dataPoints[$i]->count);
                    $result->dataPoints[$i]->count += 1;
                }

                $i += 1;
            }
        }

        $result->name = "Aggregate";
        return $result;
    }

    function multiplyArrays($arr1, $arr2) {
        if (count($arr1) != count($arr2))
            return NULL;

        $result = [];
        for($i=0; $i<count($arr1); $i++) {
            array_push($result, $arr1[$i] * $arr2[$i]);
        }

        return array_sum($result);
    }

    function linearRegression($aggregateSeries) {
        if ($aggregateSeries == NULL)
            return NULL;

        $xMean = $aggregateSeries->calcXMean();
        $yMean = $aggregateSeries->calcYMean();
        $xVariances = $aggregateSeries->calcXVariances();
        $yVariances = $aggregateSeries->calcYVariances();

        $m = multiplyArrays($xVariances, $yVariances) / multiplyArrays($xVariances, $yVariances);
        $c = $yMean - ($m * $xMean);

        $result = new LinearWeights($m, $c);
        return $result;
    }

    function buildRegressionSeries($linearWeights, $length) {
        if ($linearWeights == NULL)
            return NULL;

        $dataPoints = [];
        for ($i=0; $i<$length; $i++) {
            $y = $linearWeights->m*$i + $linearWeights->c;
            array_push($dataPoints, new DataPoint($i, $y));
        }

        return new DataSeries("Regression", "line", $dataPoints);
    }

    function getData($api, $valFieldName) {
        $baseUri = "http://ec2-54-161-186-84.compute-1.amazonaws.com/Group-7-Smart-Farm/src/Web-Server/";
        $api = $baseUri.$api;

        date_default_timezone_set('Australia/Melbourne');
        $timeRange = "30 days";
        $params = array(
            "start" => date('Y-m-d H:i:s', strtotime("-".$timeRange))
        );
        $params = http_build_query($params);

        // get sensor data from api grouped by sensor id
        $data = getSensorData($api, $valFieldName);
        
        // get aggregated average line of all sensors
        $aggregateSeries = getAggregateDataSeries($data);

        // do linear regression and get prediction 10 steps into the future
        $linearWeights = linearRegression($aggregateSeries);
        if ($linearWeights == NULL)
            return $data;

        $regressionSeries = buildRegressionSeries($linearWeights, count($aggregateSeries->dataPoints) + 10);

        array_push($data, $regressionSeries);
        array_push($data, $aggregateSeries);
        return $data;
    }

?>