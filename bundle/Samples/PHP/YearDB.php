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
$endDate = new DateTime('2014-12-31 23:59:59');
$title = 'Orders From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y') . ',  Total : %sum';
$de = new DataEngine();
$de->addParameter($startDate);
$de->addParameter($endDate);
$de->sqlStatement = 'SELECT OrderDate AS Month, SUM(1) AS Orders FROM Orders
WHERE OrderDate >=? And OrderDate <=?
    GROUP BY MONTH(OrderDate)
    ORDER BY MONTH(OrderDate);';
    $de->dataFields = 'xAxis=Month,yAxis=Orders'; //default setting
    $de->dateGrouping = "year";
    $series = $de->getSeries();
?>


		<title>JSCharting  Year DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px"></div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get data for a year grouped by months.
Learn how to:

  - Select MySQL database data based on a date range.
  - Use DataEngine DateGrouping feature with MySQL a database to get results based on years.
  - Sort MySQL database results by dates.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 380,
  legendVisible: false,
  defaultSeriesType: 'column',
  defaultPointTooltip: '{%percentOfTotal:n1}% of 2014 %seriesName',
  yAxisLabelText: 'Number of Orders',
  xAxis: {
    labelText: 'Months',
    scale: {
      interval: {  unit: 'month',  multiplier: 1},
      type: 'time'
    },
    defaultTickLabelText: '{%value:MMM}'
  }
};


if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      chartJson.series[0].palette = "default";
      chartJson.titleLabelText = <?php echo json_encode($title) ?>;

      }
chart = new JSC.Chart(chartJson);

function xAxisFormatter(val){
    return JSC.formatString(new Date(2014,val-1,1),'MMM');
    }


</script>
	</body>
</html>