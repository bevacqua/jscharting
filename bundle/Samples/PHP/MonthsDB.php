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
$startDate = new DateTime('2014-1-1');
$endDate = new DateTime('2014-6-30 23:59:59');
$title = 'Orders From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y') . ',  Total Orders : %sum';
$de = new DataEngine();
$de->addParameter($startDate);
$de->addParameter($endDate);
$de->sqlStatement = 'SELECT MONTH(OrderDate) AS Month, SUM(1) AS Orders FROM Orders WHERE OrderDate >=? And OrderDate <=?
    GROUP BY MONTH(OrderDate)
    ORDER BY MONTH(OrderDate)';    
    //$de->dataFields = 'xAxis=Month,yAxis=Orders'; //default setting
    $series = $de->getSeries();
?>


		<title>JSCharting  Months DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px"></div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get data grouped by months.
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
  defaultPointTooltip: '{%percentOfTotal:n1}% of 2014 %seriesName {%xValue}',
  xAxis: {
    labelText: 'Months',
    scale: {interval: 1 },
    defaultTickLabelText: xAxisFormatter
  },
  yAxisLabelText: 'Number of Orders'
};


if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      chartJson.xAxisDefaultTickLabelText = '{%value}';
      chartJson.series[0].palette = "default";
      chartJson.xAxisDefaultTickLabelText = xAxisFormatter;
      chartJson.titleLabelText = <?php echo json_encode($title) ?>;
      }
chart = new JSC.Chart(chartJson);

function xAxisFormatter(val){
    return JSC.formatString(new Date(2014,val-1,1),'MMM yyyy');
    }


</script>
	</body>
</html>