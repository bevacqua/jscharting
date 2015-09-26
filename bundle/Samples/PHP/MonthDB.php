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
$startDate = new DateTime('2014-10-1');
$endDate = new DateTime('2014-10-31 23:59:59');
$title = 'Sales From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y') . ',  Total : %sum';
$de = new DataEngine();
$de->addParameter($startDate);
$de->addParameter($endDate);
$de->sqlStatement = 'SELECT OrderDate AS Month, SUM(Total) AS Sales FROM Orders
WHERE OrderDate >=? And OrderDate <=?
    GROUP BY DAY(OrderDate)
    ORDER BY DAY(OrderDate);';
    $de->dataFields = 'xAxis=Month,yAxis=Sales'; //default setting
    $de->dateGrouping = "Month";
    $series = $de->getSeries();
?>


		<title>JSCharting  Month DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px"></div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get day data for a month.
Learn how to:

  - Select MySql database data based on a date range.
  - Group MySql database results based on month number of dates.
  - Sort MySql database results by dates.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 380,
  legendVisible: false,
  defaultSeriesType: 'column',
  xAxisLabelText: 'Months',
  yAxisLabelText: 'Sales (USD)',
  defaultPointTooltip: '{%xValue:d}',
  xAxis: {
    formatString: 'dd',
    labelText: 'Days',
    scale: {
      interval: {  unit: 'day',  multiplier: 1},
      type: 'time'
    }
  }
};


if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      points=chartJson.series[0].points;
      var dt;
      for (var m = 0,mLen=points.length; m < mLen; m++) {

      dt =  new Date(points[m].x);
      points[m].x= new Date(dt.getFullYear(),dt.getMonth(),dt.getDate());
      }
      chartJson.series[0].palette = "default";
      chartJson.titleLabelText = <?php echo json_encode($title) ?>;
      }
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>