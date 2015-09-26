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
//spSalesDateGroup stored procedure should be defined in your database with the following 3 parameters
$de->storedProcedure = 'spSalesDateGroup';
$de->addParameter($startDate);
$de->addParameter($endDate);
$de->addParameter("MONTH");
$de->dataFields = 'xAxis=OrderDate,yAxis=Sales'; //default setting
$series = $de->getSeries();
?>


		<title>JSCharting  Stored Procedure DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px"></div>
	
<script type="text/javascript">
/*
Query a MySQL Database using stored procedure in PHP to get data.
Learn how to:

  - Select MySql database data based on a date range.
  - Group MySql database results based on month number of dates.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 380,
  legendVisible: false,
  defaultSeriesType: 'column',
  defaultPointTooltip: '{%percentOfTotal:n1}% of 2014 %seriesName',
  yAxisLabelText: 'Total Sales  (USD)',
  yAxisFormatString: 'c',
  xAxis: {
    formatString: 'MMM',
    labelText: 'Months',
    scale: {
      interval: {  unit: 'month',  multiplier: 1},
      type: 'time'
    }
  }
};


if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      chartJson.series[0].palette = "default";
      chartJson.titleLabelText = <?php echo json_encode($title) ?>;
      var xvalue = new Date(chartJson.series[0].points[0].x);
      }
chart = new JSC.Chart(chartJson);

function xAxisFormatter(val){
    return JSC.formatString(new Date(2014,val-1,1),'MMM');
    }


</script>
	</body>
</html>