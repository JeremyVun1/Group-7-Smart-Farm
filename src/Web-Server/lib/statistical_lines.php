<?php 


function linear_regression_line($lineData) {

    $x = [];
    $y = [];

    foreach($lineData['dataPoints'] as $ld) {
        array_push($x, $ld['x']);
        array_push($y, $ld['y']);
    }

    

    // calculate number points
    $n = count($x);

    // ensure both arrays of points are the same size
    if ($n != count($y)) {
      trigger_error("linear_regression(): Number of elements in coordinate arrays do not match.", E_USER_ERROR);
    }

    // calculate sums
    $x_sum = array_sum($x);
    $y_sum = array_sum($y);

    $xx_sum = 0;
    $xy_sum = 0;

    for($i = 0; $i < $n; $i++) {
        $xy_sum+=($x[$i]*$y[$i]);
        $xx_sum+=($x[$i]*$x[$i]);
    }

    // calculate slope
    $m = (($n * $xy_sum) - ($x_sum * $y_sum)) / (($n * $xx_sum) - ($x_sum * $x_sum));

    // calculate intercept
    $b = ($y_sum - ($m * $x_sum)) / $n;

    //Build the graph data
    $lineLinReg = 
    [
        'type' => "spline",
        'showInLegend' => true,
        'name' => "Linear Regression",
        'dataPoints' => []
    ];
    $future = 5;  //How far into the future you want to go
    $count = count($x) + $future;  
    for($i=0; $i < $count; $i++) {
        $y = $m*$i + $b;
        $dataPoints = [
          'label' => "",
          'y' => $y
        ];
        array_push($lineLinReg['dataPoints'], $dataPoints);
    }

    return $lineLinReg;
}


function aggregate_mean_line($lineData) {
    $count = count($lineData[0]['dataPoints']);
    foreach ($lineData as $ld) {    //Find the smallest dataPoints set
        $rangeCount = count($ld['dataPoints']);
        if($rangeCount < $count) $count = $rangeCount;
    }
    $lineAgrMean =  
        [
            'type' => "spline",
            'showInLegend' => true,
            'name' => "Mean Average",
            'dataPoints' => []
        ];
    for ($i=0; $i < $count; $i++) {     //Go through each plot itteration
        $sum = 0;
        for ($j=0; $j < count($lineData); $j++) {   //Find the mean of this plot itteration
            try {
                $sum += $lineData[$j]['dataPoints'][$i]['y']; 
            } catch(Exception $e) {}
        }
        $mean = $sum/count($lineData);
        $dataPoints = [
            'label' => "",
            'x' => $i,
            'y' => $mean
        ]; 
        array_push($lineAgrMean['dataPoints'], $dataPoints);
    }
    return $lineAgrMean;
}

?>