<?php
//namespace JSCharting;  Generates error for php older than 5.3
class Series {
    public $name = "";
    public $type = "";
    public $sqlStatement = "";
    public $Elements = array();
    public function __construct() {
        $this->name = '';
    }
     public function __construct1($serName) {
        $this->name = $serName;
    }
    public function __construct2($serName, $serType) {
        $this->name = $serName;
        $this->type = $serType;
    }
}
/*class SeriesCollection {
    var $series = array();
    public function Add($obj) {   
        $this->series[] = $obj;
    }
}*/
class Element {
        public $name;
        public $zValue;
        public $yValue;
        public $yValueStart;
        public $xValue;
        public $xValueStart;
        public $xDateTime;
        public $xDateTimeStart;
        public $yDateTime;
        public $yDateTimeStart;
        public $bubbleSize;
        public $complete = 0;
        public $open;
        public $close;
        public $high;
        public $low;
        public $volume;
        public function __construct()
        {
            
        }
    public function __construct6($name, $dateTime1, $double1, $double2, $double3, $double4) {
        $this->name = $name;       
        $high = $double1;
        $low = $double2;
        $lpen = $double3;
        $close = $double4;
        $xDateTime = $dateTime1;
    }
}
/*class ElementCollection {
    var $elements = array();
    public function Add($obj) {   
        $this->elements[] = $obj;
    }
}*/

