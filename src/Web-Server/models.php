<?php
    function rollingAverage($currValue, $newValue, $count) {
        if ($count == 1) {
            return $newValue;
        } else {
            $result = $currValue - ($currValue / $count);
            $result = $result + ($newValue / $count);
            return $result;
        }
    }

    class LinearWeights {
        public $m;
        public $c;

        function __construct($m, $c) {
            $this->m = $m;
            $this->c = $c;
        }
    }

    class DataPoint {
        public $x;
        public $y;
        public $count;

        function __construct($x, $y) {
            $this->x = $x;
            $this->y = $y;
            $this->count = 1;
        }
    }
    
    class DataSeries {
        public $dataPoints;
        public $name;
        public $type;

        public $indexLabel;
        public $showInLegend;
        public $legendText;

        function __construct($name, $type, $dataPoints) {
            $this->name = $name;
            $this->type = $type;
            $this->dataPoints = $dataPoints;
            $this->showInLegend = true;
        }

        public function calcXMean() {
            $result = $this->dataPoints[0]->x;
            
            $count = 1;
            foreach ($this->dataPoints as $dp) {
                $result = rollingAverage($result, $dp->x, $count);
                $count += 1;
            }

            return $result;
        }

        public function calcYMean() {
            $result = $this->dataPoints[0]->y;
            
            $count = 1;
            foreach ($this->dataPoints as $dp) {
                $result = rollingAverage($result, $dp->y, $count);
                $count += 1;
            }

            return $result;
        }

        public function calcXVariances() {
            $result = [];
            $xMean = $this->calcXMean();

            foreach ($this->dataPoints as $dp) {
                array_push($result, $dp->x - $xMean);
            }
            return $result;
        }

        public function calcYVariances() {
            $result = [];
            $yMean = $this->calcYMean();

            foreach ($this->dataPoints as $dp) {
                array_push($result, $dp->y - $yMean);
            }
            return $result;
        }

        public function calcYVariance() {
            $variances = [];
            $yMean = $this->calcYMean();

            foreach ($this->dataPoints as $dp) {
                $y = $dp->y - $yMean;
                array_push($variances, $y * $y);
            }

            return array_sum($variances);
        }

        public function calcStdDev() {
            return sqrt($this->calcYVariance());
        }
    }
?>