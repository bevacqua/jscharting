<?php
//namespace JSCharting;  Generates error for php older than 5.3
require_once "DataModel.php";
require_once "MySqlConnection.php";
class DataEngine {
//public members
public $sqlStatement = "";
public $dataFields = "";
public $startDate;
public $endDate;
public $showEmptyElement = FALSE;
public $dateGrouping;
public $storedProcedure = "";
public $chartType = "Combo";

private $sqlParams= array();
private $dataFieldSet = FALSE;
private $xAxisField = "";
private $yAxisField = "";
private $zAxisField = "";
private $xDateTimeField = "";
private $yAxisFieldList = array();
private $xAxisStartField = "";
private $yAxisStartField = "";
private $splitByField = "";
private $nameField = "";
private $bubbleSizeField = "";
private $ganttCompleteField = "";
private $priceField = "";
private $highField = "";
private $lowField = "";
private $openField = "";
private $closeField = "";
private $volumeField = "";
private $urlField = "";
private $urlTargetField = "";
private $toolTipField = "";
private $labelField = "";
private $errorOffsetField = "";
private $errorPlusOffsetField = "";
private $errorHighValueField = "";
private $errorLowValueField = "";
private $errorMinusOffsetField = "";
private $errorPercentField = "";
private $errorPlusPercentField = "";
private $errorMinusPercentField = "";
private $heightField = "";
private $lengthField = "";
private $instanceIDField = "";
private $instanceParentIDField = "";
private $lowerDataFields = "";
private $customFields;
private $currentSeries;
private $linkDB;
private $stmtDB;
private $resultDB;

public function getSeries() {
    //date_default_timezone_set('America/Los_Angeles');
    //date_default_timezone_set('UTC');
    $timezoneSettingBackup = date_default_timezone_get();
    if(!empty($this->currentSeries))
    {
        $this->yAxisField="";
        $this->yAxisFieldList= array();
        $this->xAxisField="";
        $this->zAxisField="";
        $this->toolTipField="";
    }
    $this->setDataFields();
    if(!empty($this->highField) )
      return $this->getFinancialSeries ();
    $this->getDataDB();
    if(!$this->resultDB)
        return "";
$fieldsInfo = mysqli_fetch_fields($this->resultDB);
$rowCount = mysqli_num_rows($this->resultDB);
if($rowCount < 1){
    return "";
}
$localColumnNames = mysqli_fetch_assoc($this->resultDB);
$totalColumns = mysqli_num_fields($this->resultDB);

$xAxisFieldType = 246;//DECIMAL, NUMERIC
//$yAxisFieldType = 246;//DECIMAL, NUMERIC
$zAxisFieldType = 246;//DECIMAL, NUMERIC
$xAxisStartFieldType = 246;//DECIMAL, NUMERIC
$yAxisStartFieldType = 246;//DECIMAL, NUMERIC
$nameFieldType = 253;
$yAxisColumn = -1;
$xAxisColumn = -1;
$zAxisColumn = -1;
$yAxisStartColumn = -1;
$xAxisStartColumn = -1;
$splitByColumn = -1;
$bubbleSizeColumn = -1;
$nameColumn = -1;
$ganttCompleteColumn = -1;
$toolTipColumn = -1;
$labelColumn = -1;
$localxAxisField = "";
$localzAxisField = "";
$localSeriesName = "";
$customFieldsColums = array();
$strJSONCompressed ="";
$totalSeries = count($this->yAxisFieldList);

if ($totalSeries < 1) {
$totalSeries = 1;
}

if(!empty($this->currentSeries))
{
    if(substr($this->currentSeries,-1)=="]")
    {
        $strJSONCompressed = substr($this->currentSeries,0,(strlen($this->currentSeries)-1));
    }
    else {
        $strJSONCompressed = $this->currentSeries;
    }
    $strJSONCompressed .= ",";
}
 else {
    $strJSONCompressed ='[';
 }
for ($y = 0;$y < $totalSeries;$y++) {

if ($this->dataFieldSet === FALSE) {
if ($totalColumns == 1) {
    $yAxisColumn = 0;    
    $yAxisFieldType = $fieldsInfo[$yAxisColumn]->type;
} else {
    $yAxisColumn = 1;
    $xAxisColumn = 0;
    $yAxisFieldType = $fieldsInfo[$yAxisColumn]->type;
    $xAxisFieldType = $fieldsInfo[$xAxisColumn]->type;    
}
$localSeriesName = $fieldsInfo[$yAxisColumn]->name;
 
 
switch ($this->chartType) {
case "Gantt":
if ($totalColumns > 2) {
    $nameColumn = 2;
}
if ($totalColumns > 3) {
    $ganttCompleteColumn = 3;
}
break;
case "Bubble":
//$localxAxisField = mysqli_field_name($result, 0);
    $localxAxisField =  $fieldsInfo[$yAxisColumn]->name;
if ($totalColumns > 2) {
$bubbleSizeColumn = 2;
}
break;
default:

$localxAxisField =  $fieldsInfo[$yAxisColumn]->name;

break;
}//end switch
}//end $dataFieldSet
else//if dataFieldSet==true
{
    if( count($this->yAxisFieldList)>$y){
	$this->yAxisField = $this->yAxisFieldList[$y];
    }
    $yAxisFieldTemp = explode('=',$this->yAxisField);
    if(count($yAxisFieldTemp)>0)
            $this->yAxisField =$yAxisFieldTemp[0];

    if(count($yAxisFieldTemp)>1)
            $localSeriesName=$yAxisFieldTemp[1];
    else
            $localSeriesName =$yAxisFieldTemp[0];

    
    if(!empty($this->xAxisField))
    {
            $xAxisColumn = array_search($this->xAxisField,array_keys($localColumnNames));
            if($xAxisColumn===FALSE)
                    die("Could not find field " . $this->xAxisField);
            $localxAxisField = $this->xAxisField;
            $xAxisFieldType =$fieldsInfo[$xAxisColumn]->type;
            //$xAxisFieldType = mysql_field_type($result, $xAxisColumn);
    }
    if(!empty($this->zAxisField))
    {
            $zAxisColumn = array_search($this->zAxisField,array_keys($localColumnNames));
            if($zAxisColumn===FALSE)
                    die("Could not find field " . $this->zAxisField);
            $localzAxisField = $this->zAxisField;
            $zAxisFieldType = $fieldsInfo[$zAxisColumn]->type;
    }
    if(!empty($this->yAxisField))
    {
            $yAxisColumn = array_search($this->yAxisField,array_keys($localColumnNames));
            if($yAxisColumn===FALSE)
                    die("Could not find field " . $this->yAxisField);
            $yAxisFieldType =$fieldsInfo[$yAxisColumn]->type;
            //$yAxisFieldType = mysqli_field_type($result, $yAxisColumn);

    }
    if(!empty($this->xAxisStartField))
    {
            $xAxisStartColumn = array_search($this->xAxisStartField,array_keys($localColumnNames));
            if($xAxisStartColumn===FALSE)
                    die("Could not find field " . $this->xAxisStartField);
    }
    if(!empty($this->yAxisStartField))
    {
            $yAxisStartColumn = array_search($this->yAxisStartField,array_keys($localColumnNames));
            if($yAxisStartColumn===FALSE)
                    die("Could not find field " . $this->yAxisStartField);
    }

    if(!empty($this->splitByField))
    {
            $splitByColumn = array_search($this->splitByField,array_keys($localColumnNames));
            if($splitByColumn===FALSE)
                    die("Could not find field " . $this->splitByField);
            $localsplitByField = $this->splitByField;
    }


    if(!empty($this->bubbleSizeField))
    {
            $bubbleSizeColumn = array_search($this->bubbleSizeField,array_keys($localColumnNames));
            if($bubbleSizeColumn===FALSE)
                    die("Could not find field " . $this->bubbleSizeField);
    }

    if(!empty($this->nameField))
    {
            $nameColumn = array_search($this->nameField,array_keys($localColumnNames));
            if($nameColumn===FALSE)
                    die("Could not find field " . $this->nameField);
    }
    if(!empty($this->ganttCompleteField))
    {
            $ganttCompleteColumn = array_search($this->ganttCompleteField,array_keys($localColumnNames));
            if($ganttCompleteColumn===FALSE)
                    die("Could not find field " . $this->ganttCompleteField);
    }    
    if(!empty($this->toolTipField))
    {
            $toolTipColumn = array_search($this->toolTipField,array_keys($localColumnNames));
            if($toolTipColumn===FALSE)
                    die("Could not find field " . $this->toolTipField);
    }

    if(!empty($this->labelField))
    {
            $labelColumn = array_search($this->labelField,array_keys($localColumnNames));
            if($labelColumn===FALSE)
                    die("Could not find field " . $this->labelField);
    }      
     if($this->customFields)
     {
         foreach ($this->customFields as $key => $value)
         {
            $customFieldsColum = array_search($key ,array_keys($localColumnNames));
            if ($customFieldsColum ===FALSE)
               die("Could not find field " . $key);
            $customFieldsColums[$customFieldsColum] = $value;
         }

     }
    
}//end 
$strJSONCompressed .='{"name":"';
if($localSeriesName)
{
    $strJSONCompressed .= $localSeriesName;
}
else
{
  $strJSONCompressed .=' Series ';
  $strJSONCompressed .= strval($y+1);
}
    $strJSONCompressed .='","points":';
    
    $strJSON = '[';
           
    mysqli_data_seek($this->resultDB, 0); 
    $row = mysqli_fetch_row($this->resultDB);
    $currentDate = strtotime($row[$xAxisColumn]);
    $index =0;
    $hour=0;
    $daysInMonth=30;   
    $month=0;
    $this->dateGrouping = strtolower($this->dateGrouping);
    
    do {
        if(!empty($this->dateGrouping))
        {
            $strJSON .= '{';
            switch($this->dateGrouping)
            {
                case "day":
                    if($xAxisColumn>-1)
                    {
                        if($xAxisFieldType==12)
                        {
                            $dt2 =  new DateTime($row[$xAxisColumn]);
                            $hour = date_format($dt2,"H")+0;
                            date_time_set($dt2,$hour, 0, 0);                            
                            $dt = date_timestamp_get($dt2);
                            while($hour >  $index)
                            {
                                $missingdt = ((date('U',($dt-(($hour-$index)*3600))))*1000) ;
                                $strJSON .= '"x":' . $missingdt . ',';  
                                //$strJSON .= '"x":"' . date('c',($dt-(($hour-$index)*3600))) . '",';  //'c' :ISO 8601 date (added in PHP 5), javascript:yyyy-MM-ddTHH\:mm\:ss.fffffffzzz
                                 $strJSON .= '"y":0.00,';
                                 $strJSON = rtrim($strJSON, ',');
                                 $strJSON .= '},';
                                 $strJSON .= '{';
                                 $index = $index+1;
                            }
                            $index = $index+1;                            
                            $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';  
                            //$strJSON .= '"x":"' . date('c',$dt) . '",';  //'c' :ISO 8601 date (added in PHP 5), javascript:yyyy-MM-ddTHH\:mm\:ss.fffffffzzz
                        }     
                        else
                        {
                            $nameColumn = $xAxisColumn;
                            $xAxisColumn=-1;
                        }
                        /*else
                        {
                            die("For dateGrouping, xAxis must be 'datetime' type.");
                        }  */      
                    }
                     if($nameColumn>-1)
                    {
                         $curName = $row[$nameColumn];
                         $hour = intval($curName);
                         while($hour >  $index)
                        {
                            $strJSON .= '"name":"' . (string)($index) . '",';  
                             $strJSON .= '"y":0.00,';
                             $strJSON = rtrim($strJSON, ',');
                             $strJSON .= '},';
                             $strJSON .= '{';
                             $index = $index+1;
                        }
                        $index = $index+1;
                        $strJSON .= '"name":"' . $curName. '",';
                    }
                    break;
                case "month":
                    if($xAxisColumn>-1)
                    {
                        if($xAxisFieldType==12)
                        {
                            $index = $index+1;    
                            
                            $dt2 =  new DateTime($row[$xAxisColumn]);  
                            date_time_set($dt2,0, 0, 0);
                            $day = date_format($dt2,"d")+0;
                            $dt = date_timestamp_get($dt2);
                            
                            if($day >  $index)
                            {
                                $missingDays = $day-$index;
                                $dt = $dt - ($missingDays *86400);
                               
                            }
                            while($day >  $index)
                            {
                                 $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';  
                                 $strJSON .= '"y":0.00,';
                                 $strJSON = rtrim($strJSON, ',');
                                 $strJSON .= '},';
                                 $strJSON .= '{';
                                 $index = $index+1;
                                 $dt = $dt+ 86400; //(24*60*60);
                            } 
                            $index = $index+1;
                            //$strJSON .= '"x":"' . date('c',$dt) . '",';  //'c' :ISO 8601 date (added in PHP 5), javascript:yyyy-MM-ddTHH\:mm\:ss.fffffffzzz
                            $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';    
                        }  
                        else
                        {
                            $nameColumn = $xAxisColumn;
                            $xAxisColumn=-1;
                        }
                    }
                     if($nameColumn>-1)
                    {
                         $index = $index+1;
                         $curName = $row[$nameColumn];
                         $day = intval($curName);
                         while($day >  $index)
                        {
                            $strJSON .= '"name":"' . (string)($index) . '",';  
                             $strJSON .= '"y":0.00,';
                             $strJSON = rtrim($strJSON, ',');
                             $strJSON .= '},';
                             $strJSON .= '{';
                             $index = $index+1;
                        }
                        $strJSON .= '"name":"' . $curName. '",';
                    }
                    break;
                case "year":
               if($xAxisColumn>-1)
               {
                   if($xAxisFieldType==12)
                   {
                       $index = $index+1;
                       $dt2 =  new DateTime($row[$xAxisColumn]);
                       $month = date_format($dt2,"m")+0;                      
                       $dt = strtotime($row[$xAxisColumn]);
                       if($month >  $index)
                        {
                            $missingMonths = $month-$index;
                            $missingMonths = '-'. $missingMonths . ' month';
                            $dt = strtotime( $missingMonths ,$dt);
                        }
                       while($month >  $index)
                       {
                            $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';  
                            //$strJSON .= '"x":"' . date('c',$dt) . '",';  //'c' :SO 8601 date (added in PHP 5), javascript:yyyy-MM-ddTHH\:mm\:ss.fffffffzzz
                            $strJSON .= '"y":0.00,';
                            $strJSON = rtrim($strJSON, ',');
                            $strJSON .= '},';
                            $strJSON .= '{';
                            $index = $index+1;
                            $dt = strtotime( '+1 month' ,$dt);
                       }
                       $index = $index+1;
                       // $strJSON .= '"x":"' . date('c',$dt) . '",';  //'c' :SO 8601 date (added in PHP 5), javascript:yyyy-MM-ddTHH\:mm\:ss.fffffffzzz
                      $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';    
                   } 
                   else
                   {
                       $nameColumn = $xAxisColumn;
                       $xAxisColumn=-1;
                   }
               }
               if($nameColumn>-1)
               {
                    $index = $index+1;
                    $curName = $row[$nameColumn];
                    $month = intval($curName);
                    while($month >  $index)
                   {
                       $strJSON .= '"name":"' . (string)($index) . '",';  
                        $strJSON .= '"y":0.00,';
                        $strJSON = rtrim($strJSON, ',');
                        $strJSON .= '},';
                        $strJSON .= '{';
                        $index = $index+1;
                   }
                   $strJSON .= '"name":"' . $curName. '",';
               }
               break;
                default:
                    die("dategrouping:'" . $this->dateGrouping . "' is not supported. Only Day, Month and Year are supported.");
                    break;
            }
            if($yAxisColumn>-1)
            {
                $strJSON .= '"y":' . $row[$yAxisColumn] . ',';
            }
            if(count($customFieldsColums)>0)
            {
                $strJSON .= '"attributes":{';
                foreach ($customFieldsColums as $key => $value)
                {
                    $strJSON .= '"' . $value . '":"' . $row[$key] . '",';
                }
                $strJSON = rtrim($strJSON, ',');
                $strJSON .= '},';
            }                   
            if($toolTipColumn>-1)
            {
                $strJSON .= '"tooltip":"' . $row[$toolTipColumn] . '",';
            }
            $strJSON = rtrim($strJSON, ',');
            $strJSON .= '},';
        }
        else
        {
            //while ($row = mysqli_fetch_row($result)) {
            $strJSON .= '{';
            if(count($customFieldsColums)>0)
            {
                $strJSON .= '"attributes":{';
                foreach ($customFieldsColums as $key => $value)
                {
                    $strJSON .= '"' . $value . '":"' . $row[$key] . '",';
                }
                $strJSON = rtrim($strJSON, ',');
                $strJSON .= '},';
            }
            if($nameColumn>-1)
            {
                $strJSON .= '"name":"' . $row[$nameColumn]. '",';
            }
            if($xAxisColumn>-1)
            {
                if($xAxisFieldType==12)
                {
                    $dt = strtotime($row[$xAxisColumn]);
                    //$strJSON .= '"x":"' . date('Y-m-d',$dt) . '",';  
                    $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';    
                }
                else if($xAxisFieldType==253)
                {
                    $strJSON .= '"name":"' . $row[$xAxisColumn]. '",';
                }
                else
                {
                    $strJSON .= '"x":' . $row[$xAxisColumn]. ',';
                }        
            }
            if($zAxisColumn>-1)
            {
                $strJSON .= '"z":' . $row[$zAxisColumn]. ',';
            }
            if($yAxisColumn>-1)
            {
                $strJSON .= '"y":' . $row[$yAxisColumn] . ',';
            }
            if($toolTipColumn>-1)
            {
                $strJSON .= '"tooltip":"' . $row[$toolTipColumn] . '",';
            }
            $strJSON = rtrim($strJSON, ',');
            $strJSON .= '},';
        }//end dategrouping        
    } while($row = mysqli_fetch_row($this->resultDB));

    //Add empty element at the end if required.
    if(!empty($this->dateGrouping))
    {
        switch($this->dateGrouping)
        {
            case "day":
                $hour = $hour+1;                   
                while($hour <24)
                {
                     $strJSON .= '{';
                     if($xAxisColumn>-1)
                     {
                         $dt = $dt+3600;
                         $missingdt = ((date('U', $dt))*1000) ;
                         $strJSON .= '"x":' . $missingdt . ',';  
                     }
                     else
                     {
                        $strJSON .= '"name":"' . (string)($hour) . '",';  
                     }
                     $strJSON .= '"y":0.00,';
                     $strJSON = rtrim($strJSON, ',');
                     $strJSON .= '},';
                     $hour = $hour+1;
                }
            break;
             case "month":
                 if(empty($this->startDate))
                 {
                     $this->startDate = $dt2;
                 }
                 if(!empty($this->startDate))
                {
                    $daysInMonth= intval(date_format($this->startDate,"t"));
                }   
                $day = $day+1;                   
                while($day <$daysInMonth+1)
                {
                     $strJSON .= '{';
                     if($xAxisColumn>-1)
                     {
                        $dt = $dt+ 86400; //(24*60*60);
                        $strJSON .= '"x":' .  ((date('U', $dt))*1000) . ',';  
                     }
                     else
                     {
                         $strJSON .= '"name":"' . (string)($day) . '",';  
                     }
                     $strJSON .= '"y":0.00,';
                     $strJSON = rtrim($strJSON, ',');
                     $strJSON .= '},';
                     $day = $day+1;
                }
            break;
            case "year":
                $month = $month+1;                   
                while($month <13)
                {
                     $strJSON .= '{';
                     if($xAxisColumn>-1)
                     {
                        $dt = strtotime( "+1 month" ,$dt);
                        $strJSON .= '"x":' . (date('U',$dt)*1000) . ',';    
                        //$strJSON .= '"x":"' . date('c',$dt) . '",';  //'c' :SO 8601 date (added in PHP 5), javascript:yyyy-MM-ddTHH\:mm\:ss.fffffffzzz
                     }
                     else
                     {
                        $strJSON .= '"name":"' . (string)($month) . '",';  
                     }
                     $strJSON .= '"y":0.00,';
                     $strJSON = rtrim($strJSON, ',');
                     $strJSON .= '},';
                     $month = $month+1;
                }
            break;
        }
    }

    $strJSON = rtrim($strJSON, ',');
    $strJSON .=']';//end points
    
    $pointArray = json_decode($strJSON);
    $pointsCompressed = $this->pointsToArray($pointArray);
    $strJSONCompressed .= $pointsCompressed;
    
    $strJSONCompressed .='},';//end one series
}//end for
 
$strJSONCompressed = rtrim($strJSONCompressed , ',');//remove the last , because there is no more series
$strJSONCompressed .=']';//end series

$this->currentSeries = $strJSONCompressed;

$this->clear();


return $strJSONCompressed;
}
public function getArrayData() {
  
    $timezoneSettingBackup = date_default_timezone_get();  
    $this->getDataDB();
    if(!$this->resultDB)
    {
        return "";
    }
$fieldsInfo = mysqli_fetch_fields($this->resultDB);
$rowCount = mysqli_num_rows($this->resultDB);
if($rowCount < 1){
    return "";
}

    $totalColumns = mysqli_num_fields($this->resultDB);          
    mysqli_data_seek($this->resultDB, 0); 
    $row = mysqli_fetch_row($this->resultDB);
    $strJSON = '[';     
    do {
        $strJSON .= '[';
        for ($col = 0;$col < $totalColumns;$col++) {
            
            switch($fieldsInfo[$col]->type)
            {
                case 12:
                    $dt = strtotime($row[$col]);  
                    $strJSON .= (date('U',$dt)*1000) . ','; 
                    break;
                case 253:
                    $strJSON .= '"' . $row[$col]. '",';
                    break;
                default:
                    $strJSON .=  $row[$col]. ',';
                    break;
            }
        }
         $strJSON = rtrim($strJSON, ',');
        $strJSON .= '],';
    } while($row = mysqli_fetch_row($this->resultDB));

    $strJSON = rtrim($strJSON, ',');
    $strJSON .=']';//end points
    return $strJSON;
}
public function __call($name, $arguments)
{
    if($name=="addParameter")
    {
        $localParam = array();
        if(count($arguments)==3)
        {
           array_push($localParam,$arguments[0],$arguments[1],$arguments[2]);    
        }
        else if(count($arguments)==2)
        {
            array_push($localParam,$arguments[0],$arguments[1],"");    
        }
        else
        {
            $paramType = gettype($arguments[0]);
            if($arguments[0] instanceof DateTime)
            {
                $paramType="datetime";
            }
            
            array_push($localParam,$arguments[0],$paramType,"");    
        }
        
        array_push($this->sqlParams,$localParam);
    }
}
private function setDataFields(){
    if(empty($this->dataFields)) 
    {
        return;
    }
//dataTable=null;
$this->dataFieldSet = True;
//intialize the YAxis list
$this->yAxisFieldList = array();
while(strpos($this->dataFields, " =")===true){
$this->dataFields = str_replace(" =", "=", $this->dataFields);
}


$this->lowerDataFields = strtolower($this->dataFields);

$this->tableName = $this->GetDataField("table=");

$this->xAxisField = $this->GetDataField("xaxis=");
if(strlen($this->xAxisField)==0){
    $this->xAxisField = $this->GetDataField("xvalue=");
}
$this->zAxisField = $this->GetDataField("zaxis=");
if(strlen($this->zAxisField)==0){
    $this->zAxisField = $this->GetDataField("zvalue=");
}
$this->xDateTimeField = $this->GetDataField("xdatetime="); //same as xAxisField but not setting name 5/28/2010
if (!empty($this->xDateTimeField)){
    $this->xAxisField = $this->xDateTimeField;
}

$this->yAxisField = $this->GetDataField("yaxis=");
if(strlen($this->yAxisField)>0)
{
array_push($this->yAxisFieldList,$this->yAxisField);
while(strlen($this->yAxisField) > 0)
{
$this->yAxisField = $this->GetDataField("yaxis=");
if(strlen($this->yAxisField) > 0){
    array_push($this->yAxisFieldList,$this->yAxisField);
}
else
break;
}
}
else
{
$this->yAxisField = $this->GetDataField("yvalue=");
if(strlen($this->yAxisField)>0)
{
array_push($this->yAxisFieldList,$this->yAxisField);
while(strlen($this->yAxisField) > 0)
{
$this->yAxisField = $this->GetDataField("yvalue=");
if(strlen($this->yAxisField) > 0)
array_push($this->yAxisFieldList,$this->yAxisField);
else
break;
}
}
else
{
$this->yAxisField = $this->GetDataField("volume=");
if(strlen($this->yAxisField)==0)
$this->yAxisField = $this->GetDataField("ganttend=");
if(strlen($this->yAxisField)==0)
$this->yAxisField = $this->GetDataField("ganttenddate=");
if(strlen($this->yAxisField) > 0)
array_push($this->yAxisFieldList,$this->yAxisField);
}
}

$this->xAxisStartField = $this->GetDataField("xvaluestart=");


$this->yAxisStartField = $this->GetDataField("yvaluestart=");
if(strlen($this->yAxisStartField)==0)
$this->yAxisStartField = $this->GetDataField("ganttstart=");
if(strlen($this->yAxisStartField)==0)
$this->yAxisStartField = $this->GetDataField("ganttstartdate=");

$this->nameField = $this->GetDataField("ganttname=");
if(strlen($this->nameField)==0)
$this->nameField = $this->GetDataField("name=");


$this->splitByField = $this->GetDataField("splitby=");

$this->bubbleSizeField = $this->GetDataField("bubblesize=");
if(strlen($this->bubbleSizeField)==0)
$this->bubbleSizeField = $this->GetDataField("bubble=");



$this->ganttCompleteField = $this->GetDataField("ganttcomplete=");
if(strlen($this->ganttCompleteField)==0)
$this->ganttCompleteField = $this->GetDataField("complete=");

$this->priceField = $this->GetDataField("price=");
$this->highField = $this->GetDataField("high=");
$this->lowField = $this->GetDataField("low=");
$this->openField = $this->GetDataField("open=");
$this->closeField = $this->GetDataField("close=");
$this->volumeField = $this->GetDataField("volume=");
$this->urlField = $this->GetDataField("url=");
$this->urlTargetField = $this->GetDataField("urltarget=");
$this->toolTipField = $this->GetDataField("tooltip=");
$this->labelField = $this->GetDataField("labeltemplate=");
$this->errorOffsetField = $this->GetDataField("erroroffset=");
$this->errorPlusOffsetField = $this->GetDataField("errorplusoffset=");
$this->errorHighValueField = $this->GetDataField("errorhighvalue=");
$this->errorLowValueField = $this->GetDataField("errorlowvalue=");
$this->errorMinusOffsetField = $this->GetDataField("errorminusoffset=");
$this->errorPercentField = $this->GetDataField("errorpercent=");
$this->errorPlusPercentField = $this->GetDataField("errorpluspercent=");
$this->errorMinusPercentField = $this->GetDataField("errorminuspercent=");
$this->heightField = $this->GetDataField("height=");
$this->lengthField = $this->GetDataField("length=");
$this->instanceIDField = $this->GetDataField("instanceid=");
$this->instanceParentIDField = $this->GetDataField("instanceparentid=");


if(strlen($this->dataFields)>0)
{
$this->customFields = array();
$ArrayCustomDataFields = explode(',', $this->dataFields);
foreach ($ArrayCustomDataFields as $key => $value)
{
$nameValue = explode('=', $value);
if(count($nameValue)>1)
{
    $this->customFields[$nameValue[0]] = $nameValue[1];
}
else
{
    $this->customFields[$nameValue[0]] = $nameValue[0];
}
}
}
}
private function getDataField($name)
{    if(empty($this->lowerDataFields))
{
        return "";
}
    $fieldValue = "";
    $len = strlen($name);
    $j=-1;
    $i = strpos($this->lowerDataFields,$name);
    if($i!==False)
    {
            $i = $i + $len;
            $j = strpos($this->lowerDataFields,",",$i);
            if($j) //only executes if $j >0 
            {
                    while($this->lowerDataFields[$j-1]=='\\')
                    {
                            $this->lowerDataFields = substr_replace($this->lowerDataFields,'',$j-1,1);
                            $this->dataFields = substr_replace($this->dataFields,'',$j-1,1);
                            $j = strpos($this->lowerDataFields,",",$j+1);
                            if($j===FALSE)
                                    break;
                    }
                    if($j===False)
                    {
                            $fieldValue = substr($this->dataFields,$i);
                            $i= $i - $len;
                            $this->dataFields =substr_replace($this->dataFields,'',$i,count($this->dataFields)-$i);//remove
                            $this->lowerDataFields = substr_replace($this->lowerDataFields,'',$i,count($this->lowerDataFields)-$i);

                    }
                    else
                    {
                            $fieldValue = substr($this->dataFields,$i,$j-$i);
                            $start=$i-$len;
                            $this->dataFields = substr_replace($this->dataFields,'',$start,$j-$start+1);
                            $this->lowerDataFields = substr_replace($this->lowerDataFields,'',$start,$j-$start+1);
                    }
            }
            else
            {//this is last entry without "," in field's name
                    $fieldValue = substr($this->dataFields,$i);
                    $i=$i-$len;
                    $this->dataFields = substr_replace($this->dataFields,'',$i,strlen($this->dataFields)-$i);
                    $this->lowerDataFields = substr_replace($this->lowerDataFields,'',$i,strlen($this->lowerDataFields)-$i);

            }
            $this->dataFields = trim($this->dataFields);
            $this->lowerDataFields =trim($this->lowerDataFields);
    }


    return $fieldValue;
}
private function getFinancialSeries() {
    
    $this->getDataDB();
    if(!$this->resultDB)
        return "";
    $fieldsInfo = mysqli_fetch_fields($this->resultDB);
    $rowCount = mysqli_num_rows($this->resultDB);
    if($rowCount < 1){
        return "";
    }
    $localColumnNames = mysqli_fetch_assoc($this->resultDB);
    $totalColumns = mysqli_num_fields($this->resultDB);

    $sc = array();
    $fromDate;
    $toDate;
    $cursorDate;
    $cursorDate2;
    $dt;				
    $yValue;
    $priceValue=0;
    $yValueDt;				
    $ht;
    $numberOfElements=0;
    $cf ="en-US";
    $yAxisColumn=-1;
    $xAxisColumn=-1;
    $openColumn=-1;
    $closeColumn=-1;
    $highColumn=-1;
    $lowColumn=-1;
    $volumeColumn=-1;
    $priceColumn=-1;
    $errorOffsetColumn=-1;
    $errorPlusOffsetColumn=-1;
    $errorHighValueColumn=-1;
    $errorLowValueColumn=-1;
    $errorMinusOffsetColumn=-1;
    $errorPercentColumn=-1;
    $errorPlusPercentColumn=-1;
    $errorMinusPercentColumn=-1; 
    $localxAxisField="";
    $localSeriesName="";
    $strJSONCompressed ="";
    //$tableName = mysql_field_table($result,0);
    $tableName = $fieldsInfo[0]->table;
    if($tableName)
    {
        $localSeriesName = $tableName;
    }
    
    if ($this->dataFieldSet === FALSE)
    {
        if($totalColumns >2)
        {
            $xAxisColumn=0;
            $yAxisColumn=1;
            $localxAxisField =$fieldsInfo[$xAxisColumn]->name;
            
            
            
            $priceColumn = 2;
        }
        else//2 fields
        {
            $xAxisColumn=0;
            $priceColumn = 1;
            $localxAxisField =$fieldsInfo[$xAxisColumn]->name;
        }
    }
    else//dataFieldSet is TRUE
    {
        if($totalSeries>0)
                $yAxisField=$this->yAxisFieldList[0];

        if(!empty($this->xAxisField))
        {
                $xAxisColumn = array_search($this->xAxisField,array_keys($localColumnNames));
                if($xAxisColumn===FALSE)
                        die("Could not find field " . $this->xAxisField);
                $localxAxisField = $this->xAxisField;
                $xAxisFieldType = $fieldsInfo[$xAxisColumn]->type;
        }
        if(!empty($this->yAxisField))
        {
                $yAxisColumn = array_search($this->yAxisField,array_keys($localColumnNames));
                        if($yAxisColumn===FALSE)
                                die("Could not find field " . $this->yAxisField);
        }
        if(!empty($this->priceField))
        {
                $priceColumn = array_search($this->priceField,array_keys($localColumnNames));
                if($priceColumn<0)
                        die("Could not find field " .  $this->priceField);
        }
        if(!empty($this->openField))
        {
                $openColumn = array_search($this->openField,array_keys($localColumnNames));
                if($openColumn<0)
                        die("Could not find field " .  $this->openField);
        }
        if(!empty($this->closeField))
        {
                $closeColumn = array_search($this->closeField,array_keys($localColumnNames));
                if(closeColumn<0)
                        die("Could not find field " .  $this->closeField);						
        }
        if(!empty($this->lowField))
        {
                $lowColumn = array_search($this->lowField,array_keys($localColumnNames));
                if($lowColumn<0)
                        die("Could not find field " . $this->lowField);						
        }
        if(!empty($this->highField))
        {
                $highColumn = array_search($this->highField,array_keys($localColumnNames));
                if($highColumn<0)
                        die("Could not find field " . $this->highField);						
        }
         if(!empty($this->volumeField))
        {
                $volumeColumn = array_search($this->volumeField,array_keys($localColumnNames));
                if($volumeColumn<0)
                        die("Could not find field " . $this->volumeField);						
        }
        if(!empty($this->errorOffsetField))
        {
                $errorOffsetColumn = array_search($this->errorOffsetField,array_keys($localColumnNames));
                if($errorOffsetColumn<0)
                        die("Could not find field " . $this->errorOffsetField);
        }
        if(!empty($this->errorPlusOffsetField))
        {
                $errorPlusOffsetColumn = array_search($this->errorPlusOffsetField,array_keys($localColumnNames));
                if($errorPlusOffsetColumn<0)
                        die("Could not find field " . $this->errorPlusOffsetField);
        }
        if(!empty($this->errorHighValueField))
        {
                $errorHighValueColumn = array_search($this->errorHighValueField,array_keys($localColumnNames));
                if($errorHighValueColumn<0)
                        die("Could not find field " . $this->errorHighValueField);
        }
        if(!empty($this->errorLowValueField))
        {
                $errorLowValueColumn = array_search($this->errorLowValueField,array_keys($localColumnNames));
                if($errorLowValueColumn<0)
                        die("Could not find field " . $this->errorLowValueField);
        }
        if(!empty($this->errorMinusOffsetField))
        {
                $errorMinusOffsetColumn = array_search($this->errorMinusOffsetField,array_keys($localColumnNames));
                if($errorMinusOffsetColumn<0)
                        die("Could not find field " . $this->errorMinusOffsetField);
        }
        if(!empty($this->errorPercentField))
        {
                $errorPercentColumn = array_search($this->errorPercentField,array_keys($localColumnNames));
                if($errorPercentColumn<0)
                        die("Could not find field " . $this->errorPercentField);
        }
        if(!empty($this->errorPlusPercentField))
        {
                $errorPlusPercentColumn = array_search($this->errorPlusPercentField,array_keys($localColumnNames));
                if($errorPlusPercentColumn<0)
                        die("Could not find field " . $this->errorPlusPercentField);
        }
        if(!empty($this->errorMinusPercentField))
        {
                $errorMinusPercentColumn = array_search($this->errorMinusPercentField,array_keys($localColumnNames));
                if($errorMinusPercentColumn<0)
                        die("Could not find field " . $this->errorMinusPercentField);
        }
}//end of dataFieldSet
if(!empty($this->currentSeries))
{
    if(substr($this->currentSeries,-1)=="]")
    {
        $strJSONCompressed = substr($this->currentSeries,0,(strlen($this->currentSeries)-1));
    }
    else {
        $strJSONCompressed = $this->currentSeries;
    }
    $strJSONCompressed .= ",";
}
 else {
    $strJSONCompressed ='[';
 }
 
$strJSON = '[';
$lowerDateGrouping = strtolower($this->dateGrouping);
switch($lowerDateGrouping){		
case "months":
        if($xAxisFieldType!= 12)
        {
                die(" Invalid SQL statement. When using 'DateGrouping' the xAxis field in the SQL statement must be of type DateTime. Please check the field type and return order from your database. Data order expected: xAxis, yAxis. You can also use the dataFields property to map to any field name / order.  ");
                break;
        }
        $monthIndex=-1;
        $ser = new Series('ser 1');
        $singleElement;
        mysqli_data_seek($this->resultDB, 0); 
        $row = mysqli_fetch_row($this->resultDB);
        do
        {
            if($yAxisColumn>-1)
            {
                    if(empty($row[$yAxisColumn])) 
                            $yValue = 0;
                    else
                            $yValue = floatval($row[$yAxisColumn]);
            }
            if($priceColumn>-1)
            {
                    if(empty($row[$priceColumn])) 
                            $priceValue = 0;
                    else
                            $priceValue = floatval($row[$priceColumn]);
            }

            $cursorDate =  strtotime($row[$xAxisColumn]);

            $monthsCurrent = date('Y',$cursorDate) + date('m',$cursorDate);
            if($monthIndex != $monthsCurrent){
                    if(!empty($singleElement))
                    {                            
                            array_push($ser->Elements,$singleElement);
                    }
                    $monthIndex = $monthsCurrent;
                    if(!empty($priceValue))
                    {
                            $singleElement = new Element('',$cursorDate,$priceValue,$priceValue,$priceValue,$priceValue);
                    }
                    else
                    {
                            $singleElement = new Element();
                            $singleElement->xValue= $cursorDate;
                    }
                    //SF.trickleDefaults($singleElement,ser.defaultElement);
                    if(!empty($yValue))
                            $singleElement->volume = $yValue;
            }
            else{
                    if(!empty($priceValue))
                    {
                            if($singleElement->low > $priceValue)
                                    $singleElement->low = $priceValue;
                            if($singleElement->high < $priceValue)
                                    $singleElement->high = $priceValue;
                            $singleElement->close = $priceValue;
                    }
                    if(!empty($yValue))
                    {
                        if(empty($singleElement->volume)) {
                            $singleElement->volume = $yValue;
                        }
                        else {
                            $singleElement->volume = $singleElement->volume + $yValue;
                        }
                    }
            }

        }while($row = mysqli_fetch_row($this->resultDB));
        if($singleElement != FALSE)
        {
            array_push($ser->Elements,$singleElement);
        }
        array_push($sc, $ser);
        break;					
default:
    $ser = new Series();
    if(!empty($localSeriesName))
    {
        $ser->name = $localSeriesName;
    }
        
    array_push($sc,$ser);
						
   mysqli_data_seek($this->resultDB, 0); 
   $row = mysqli_fetch_row($this->resultDB);
   do
   {  
        $singleElement = new Element();
        if($yAxisColumn>-1){
            if(empty($row[$yAxisColumn])) 
                    $singleElement->volume = NULL;
            else
                    $singleElement->volume = floatval($row[$yAxisColumn]);
        }					
        if($xAxisColumn>-1)
        {

                if(empty($row[$xAxisColumn])) 
                        $singleElement->xValue = NULL;
                else
                        $singleElement->xValue = strtotime($row[$xAxisColumn]);

        }
        if($priceColumn>-1)
        {
                if(empty($row[$priceColumn]))
                        $singleElement->close = NULL;
                else
                        $singleElement->close = floatval($row[$priceColumn]);
        }
        if($openColumn>-1)
        {
                if(empty($row[$openColumn]))
                        $singleElement->open = NULL;
                else
                        $singleElement->open = floatval($row[$openColumn]);
        }
        if($closeColumn>-1)
        {
                if(empty($row[$closeColumn])) 
                        $singleElement->close= NULL;
                else
                        $singleElement->close = floatval($row[$closeColumn]);
        }
        if($lowColumn>-1)
        {
                if(empty($row[$lowColumn])) 
                        $singleElement->low= NULL;
                else
                        $singleElement->low = floatval($row[$lowColumn]);
        }
        if($highColumn>-1)
        {
                if(empty($row[$highColumn])) 
                        $singleElement->high= NULL;
                else
                        $singleElement->high = floatval($row[$highColumn]);
        }
        if($errorOffsetColumn>-1)
        {
                if(!empty($row[$errorOffsetColumn])) 
                        $singleElement->errorOffset = floatval($row[$errorOffsetColumn]);
        }
        if($errorPlusOffsetColumn>-1)
        {
                if(!empty($row[$errorPlusOffsetColumn])) 
                        $singleElement->errorPlusOffset = floatval($row[$errorPlusOffsetColumn]);
        }
        if($errorHighValueColumn>-1)
        {
                if(!empty($row[$errorHighValueColumn])) 
                        $singleElement->errorHighValue = floatval($row[$errorHighValueColumn]);
        }
        if($errorLowValueColumn>-1)
        {
                if(!empty($row[$errorLowValueColumn])) 
                        $singleElement->errorLowValue = floatval($row[$errorLowValueColumn]);
        }
        if($errorMinusOffsetColumn>-1)
        {
                if(!empty($row[$errorMinusOffsetColumn])) 
                        $singleElement->errorMinusOffset = floatval($row[$errorMinusOffsetColumn]);
        }
        if($errorPercentColumn>-1)
        {
                if(!empty($row[$errorPercentColumn])) 
                        $singleElement->errorPercent = floatval($row[$errorPercentColumn]);
        }
        if($errorPlusPercentColumn>-1)
        {
                if(!empty($row[$errorPlusPercentColumn])) 
                        $singleElement->errorPlusPercent = floatval($row[$errorPlusPercentColumn]);
        }
        if($errorMinusPercentColumn>-1)
        {
                if(!empty($row[$errorMinusPercentColumn])) 
                        $singleElement->errorMinusPercent = floatval($row[$errorMinusPercentColumn]);
        }
        array_push($ser->Elements,$singleElement);
							
	}while($row = mysqli_fetch_row($this->resultDB));
    break;
    }//end of switch($lowerDateGrouping
    $scCount = count($sc);
    
    if(!empty($this->currentSeries))
    {
        if(substr($this->currentSeries,-1)=="]")
        {
            $strJSON = substr($this->currentSeries,0,(strlen($this->currentSeries)-1));
        }
        else {
            $strJSON = $this->currentSeries;
        }
        $strJSON .= ",";
    }
    else {
       $strJSON ='[';
    }
    if ($scCount> 0)
    {
        for($s=0;$s < count($sc); $s++)
        {
           
          $strJSONCompressed .='{"name":"';
          if($sc[$s]->name)
          {
              $strJSONCompressed .= $sc[$s]->name;
          }
          else
          {
             $strJSONCompressed .= ' Series ' .  strval( $s+1);     
          }
          if($elem->volume)
          {
              
          }
          else
          {
            $strJSONCompressed .= '","points":';
          }
            for($el=0;$el<count($sc[$s]->Elements); $el++)
            {
                $elem = $sc[$s]->Elements[$el];
                $dt2 = $elem->xValue;                 
                if($elem->volume)
                 { //point object for NavigatorMultiY (seriestype: 'ohlc' doesn't work, 
                    //Array       
                    //$strJSON .= '[ ' . (date('U',$elem->xValue)*1000) . ',' . $elem->open . ',' . $elem->high . ',' . $elem->low . ',' . $elem->close . ',' . $elem->volume . '],';  
                    $strJSON .= '{"x":' . (date('U',$elem->xValue)*1000) . ',"open":' . $elem->open . ',"high":' . $elem->high . ',"low":' . $elem->low . ',"close":' . $elem->close . ',"volume":' . $elem->volume. '},';     
                 }
                else{
                     $strJSON .= '{"x":' . (date('U',$elem->xValue)*1000) . ',"open":' . $elem->open . ',"high":' . $elem->high . ',"low":' . $elem->low . ',"close":' . $elem->close. '},';     

                }
            }
            //$strJSON = rtrim($strJSON, ',');
            //$strJSON .= ']}';
        }
        $strJSON = rtrim($strJSON, ',');
        $strJSON .=']';//end points
        if($elem->volume)
        {
            $strJSONCompressed .= $strJSON;// the above lines replaced this line to use compression
        }
        else
        {
             $pointArray = json_decode($strJSON);
        
            $pointsCompressed = $this->pointsToArray($pointArray);
            $strJSONCompressed .= $pointsCompressed;
            
        }

       
        
        

        $strJSONCompressed .='},';//end one series
    }//end for
 
$strJSONCompressed = rtrim($strJSONCompressed , ',');//remove the last , because there is no more series
$strJSONCompressed .=']';//end series

$this->currentSeries = $strJSONCompressed;

$this->clear();


return $strJSONCompressed;
}
private function pointsToArray($points) {
    $template = "\"JSC.pointsFromArray('%FIELDS%',[%ARR%])\"";
    $firstPoint = current($points);
    $secondPoint = next($points);
    $fields = $this->getPropNamesRecur($firstPoint);
    $pntArrTxt = '';
    $pntArr = array();
    for ($i = 0, $iLen = count($points); $i < $iLen; $i++) {
        $obj = $points[$i];
        $fldArr = array();
        for ($f = 0, $fLen = count($fields); $f < $fLen; $f++) {
            $fVal = $this->evalPath($obj, $fields[$f]);// obj[fields[f]];
            if (  gettype($fVal) === 'string') {
                array_push($fldArr, '\'' . $fVal . '\'');
            }
            /*else if (  gettype($fVal) === 'object') {
                array_push($fldArr, '');
            }*/
            else if (gettype($fVal) === 'array'){ //if (array_pop($fVal) != NULL) { // This means if fVal is an array
                array_push($fldArr, json_decode($fVal));
            } else {
                array_push($fldArr, $fVal);
            }

        }
        array_push($pntArr, '[' . join(',', $fldArr) . ']');
    }
    $pntArrTxt .= implode(',', $pntArr);

    $resultStr = str_replace('%FIELDS%', join(',', $fields),$template);
    $resultStr = str_replace('%ARR%', $pntArrTxt,$resultStr);
    return $resultStr;
}
function is_assoc($array) {
  return ($array !== array_values($array));
}
private function getPropNames($obj) {
    $myKeys = array();    
    for ($i = 0;$i<count($obj); $i++)
    {
        array_push($myKeys,$obj[$i]);
    }    
    return $myKeys; //['a','b','c'];
}
private function getPropNamesRecur($obj) {
    $myKeys = array();       
    //for (myKeys[i++] in obj);
    foreach ($obj as $key => $value)
    {
        if(gettype($value)=='object')
        {
             $arr = $this->getPropNamesRecur($value); 
             foreach ($arr as $key2 => $value2)
            {
                $a3 =  $key . '.' . $value2;
                array_push($myKeys,$a3);
               
            } 
        }
        else
        {
            array_push($myKeys,$key);
        }
    }  
    foreach ($obj as $key => $value) {
        $a2 = gettype($value);
        if ($a2 ==='object2') {
            $arr = $this->getPropNamesRecur($value); 
            $arrLen = count($arr); 
            $index =0;
            $i = array_search($key,$myKeys);
            foreach ($arr as $key2 => $value2)
            {
                $a3 =  $key . '.' . $value2;
                
                //array_push($myKeys[$i],$a3);
                if($index>0)
                {
                    //$myKeys[$i] .= ', ' . $a3;  
                    array_push($myKeys,$a3);
                }
                else 
                {
                    $myKeys[$i] = $a3;      
                }
                  $index = $index+1;    
                //array_push($myKeys[$i],$value);
            } 
           
        }
        else if ($a2 ==='array') {
            $i = array_search($key,$myKeys);
            $origValue = $myKeys[$i];
            
            //$myKeys[$i] = $myKeys[$i]. '[n]';   
            
            $arr = $this->getPropNamesRecur2(current($value));  
            $myKeys[$i] =  array();  
            array_push($myKeys[$i],$origValue);
             foreach ($arr as $key => $value)
            {
             //$myKeys[$i] = $myKeys[$i]. $value . ',' ;
                array_push($myKeys[$i],$value);
            } 
            //rtrim($myKeys[$i], ",");
            //$myKeys[$i] = $myKeys[$i]. '}' ;
                    
        }
    }
    return $myKeys; //['a','b','c'];

}
private function getPropNamesRecur2($obj) {
    $myKeys = array();       
    //for (myKeys[i++] in obj);
    foreach ($obj as $key => $value)
    {
        array_push($myKeys,$key);
    }  
    foreach ($obj as $key => $value) {
        $a2 = gettype($value);
        if ($a2 ==='object') {
            $arr = $this->getPropNamesRecur2($value);            
            $a3 =  $key . '.' . $arr[0];
            $i = array_search($key,$myKeys);
            $myKeys[$i] = $a3;            
        }
        else if ($a2 ==='array') {
           //$i = array_search($key,$myKeys);
            //$myKeys[//$i] = $myKeys[$i]. '[n]';    
            ////$arr = $this->getPropNamesRecur($value);            
            //$a3 =  $key . '.' . $arr[0];
                    
        }
    }
    return $myKeys; //['a','b','c'];

}
private function magic($obj, $var, $value = NULL)
{
    if($value == NULL)
    {
        return $obj->$var;
    }
    else
    {
        $obj->$var = $value;
    }
}
private function evalPath($root, $path) {
    if ($root && $path) {
        $steps = explode('.',$path);
        $curStep = $root;
        $stpI = 0;
        $stpLen;
        $step;
        for ($stpLen = count($steps); $stpI < $stpLen; $stpI++)
            /*for (stpI in steps)*/ {
            $step = $steps[$stpI];
            $value = $this->magic($curStep,$step,NULL);
            if ($value!==NULL) {
                $curStep = $value;
            }
            else {
                return NULL;
            }
        }
        return $curStep;
    }
    return NULL;
}
private function getDataDB()
{
    if(!empty($this->startDate))
    {
        $this->sqlStatement = str_ireplace("#StartDate#","'" .  date_format($this->startDate, 'Y-m-d H:i:s') . "'",$this->sqlStatement);
    }
    if(!empty($this->endDate))
    {
        $this->sqlStatement = str_ireplace("#EndDate#","'" .  date_format($this->endDate, 'Y-m-d H:i:s') . "'",$this->sqlStatement);
    }
$this->linkDB = ConnectToMySql();
$this->stmtDB = mysqli_stmt_init($this->linkDB);
$countParams = count($this->sqlParams);
if(!empty($this->storedProcedure))
{
    $storedProcedureCall = $this->storedProcedure;
    if (0 !== strpos(strtolower($this->storedProcedure), 'call ')) {
        $storedProcedureCall = "call " . $storedProcedureCall . "(";
    }
    for( $i=0;$i<$countParams;$i++)
    {
        $storedProcedureCall .= "?,";
    }
    $storedProcedureCall = rtrim($storedProcedureCall, ',');
    $storedProcedureCall .= ")";
 
 if (mysqli_stmt_prepare($this->stmtDB, $storedProcedureCall)) {
     /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
    $a_params = array(); 
    $param_type = '';    
    foreach ($this->sqlParams as $key => $value)
    {
        if(is_numeric($value[0]))
        {
            $a_params[] = $value[0];
            $param_type .= "d";
        }
        else if($value[0] instanceof DateTime)
        {
            $param_dt = date_format($value[0], 'Y-m-d H:i:s');
            $a_params[] =  $param_dt;
            $param_type .= "s";
        }   
        else
        {
            $a_params[] =  $value[0];
            $param_type .= "s";
        }   
    }
   
    call_user_func_array('mysqli_stmt_bind_param', array_merge (array($this->stmtDB, $param_type), $this->refValues($a_params))); 
    /* execute query */
    mysqli_stmt_execute($this->stmtDB);
    $this->resultDB = mysqli_stmt_get_result($this->stmtDB) or die($this->stmtDB->error) ;
}
else
{
    die('Sql error: ' . $this->stmtDB->error);
} 
   
}
else
{
    if (mysqli_stmt_prepare($this->stmtDB, $this->sqlStatement)) {
        if($countParams>0)
        {
             $a_params = array(); 
            $param_type = '';    
             foreach ($this->sqlParams as $key => $value)
            {
                if(is_numeric($value[0]))
                {
                    $a_params[] = $value[0];
                    $param_type .= "d";
                }
                else if($value[0] instanceof DateTime)
                {
                    $param_dt = date_format($value[0], 'Y-m-d H:i:s');
                    $a_params[] =  $param_dt;
                    $param_type .= "s";
                }   
                else
                {
                    $a_params[] =  $value[0];
                    $param_type .= "s";
                }   
            }
   
            call_user_func_array('mysqli_stmt_bind_param', array_merge (array($this->stmtDB, $param_type), $this->refValues($a_params))); 
        }
        /* execute query */
        mysqli_stmt_execute($this->stmtDB);
        $this->resultDB = mysqli_stmt_get_result($this->stmtDB) or die($this->stmtDB->error) ;
    }
    else
    {
        die('Sql error: ' . $this->stmtDB->error);
    }     
}
}
private function clear()
{
    // Free the resources associated with the result set
// This is done automatically at the end of the script
mysqli_free_result($this->resultDB);

mysqli_stmt_close($this->stmtDB);
/* close connection */
mysqli_close($this->linkDB);



//clear
$this->sqlStatement="";
$this->storedProcedure="";
$this->startDate = NULL;
$this->endDate = NULL;
$this->dataFields="";
$this->dataFieldSet=FALSE;
$this->dateGrouping="";
$this->sqlParams = array();
}
private function refValues($arr){ 
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+ 
    { 
        $refs = array(); 
        foreach($arr as $key => $value) 
            $refs[$key] = &$arr[$key]; 
        return $refs; 
    } 
    return $arr; 
} 

}//end DataEngine class
?>