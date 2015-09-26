<?php
//The included Includes/DataEngine.php contains
//functions to help easily embed the charts and connect to a database.
include("Includes/DataEngine.php");
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text-html; charset=utf-8"/>
<script type="text/javascript" src="../../JSC/jquery-latest.min.js"></script>
<script type="text/javascript" src="../../JSC/JSCharting.js"></script>
<?php
$startDate = new DateTime('2014-11-11 00:00:00');
$endDate = new DateTime('2014-11-11 23:59:59');
$title = 'Sales for ' . date_format($startDate, 'm/d/Y') . ',  Total : {%sum:c}';
$de = new DataEngine();
$de->addParameter($startDate);
$de->addParameter($endDate);
$de->sqlStatement = 'SELECT OrderDate AS Hours, SUM(Total) AS Sales FROM Orders
WHERE OrderDate >=? And OrderDate <=?
    GROUP BY HOUR(OrderDate)
    ORDER BY HOUR(OrderDate);';
    $de->dataFields = 'xAxis=Hours,yAxis=Sales'; //default setting
    $de->dateGrouping = "day";
    $series = $de->getSeries();
?>


		<title>JSCharting  Day DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px"></div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get data grouped by hours of the day.
Learn how to:

  - Select MySql database data based on a date range.
  - Use DataEngine DateGrouping feature with MySql a database to get results based on day.
  - Sort MySql database results by dates.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 380,
  legendVisible: false,
  defaultSeriesType: 'column',
  defaultPointTooltip: '{%percentOfTotal:n1}% of 2014 %seriesName',
  yAxisLabelText: 'Sales (USD)',
  yAxisFormatString: 'c',
  xAxis: {
    formatString: 'HH',
    labelText: 'Hours',
    scale: {
      interval: {  unit: 'hour',  multiplier: 1},
      type: 'time'
    }
  }
};


if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      chartJson.series[0].palette = "default";
      chartJson.titleLabelText = <?php echo json_encode($title) ?>;
      }
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>