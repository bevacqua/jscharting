<?php
include("Includes/DataEngine.php");
               
$startDate =  htmlspecialchars($_GET["startDate"]);
if(empty($startDate))
{
    $startDate = '2014-1-1';
}
$de = new DataEngine();    
$startDate =  new DateTime($startDate);
$endDate = new DateTime(date_format($startDate,"Y") . '-' . date_format($startDate,"m") .'-' . date_format($startDate,"t"). ' 23:59:59'); 
$de->addParameter($startDate);
$de->addParameter($endDate);
$title = 'Total Sales: %sum From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y');                
$de->dataFields = 'name=Day,yAxis=TotalSales';                
$de->sqlStatement = 'SELECT Day(OrderDate) AS Day, SUM(Total) AS TotalSales FROM Orders
    WHERE OrderDate >=? And OrderDate <=? GROUP BY Day(OrderDate) ORDER BY Day(OrderDate);'; 

$de->dateGrouping = "Month";//This setting shows all the days in the month regardless of having data on that day or not.
$series = $de->getSeries();

$strJSON = '{
"title": { "label": {"text": "'. $title . '" }  },';
if ($series) { 
    $strJSON .= '"series": ';        
    $strJSON .= $series;                             
}//series  
else {
$strJSON .='"annotations": [
{
    "position": "CA:10,10",
    "labelText": "No Data",
    "labelColor": "red",
    "boxVisible": false
}
]';                    
}
$strJSON .='}';
echo $strJSON;
?>
