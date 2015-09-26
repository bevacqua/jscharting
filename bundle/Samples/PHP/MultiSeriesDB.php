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
$de = new DataEngine();
$de->addParameter(new DateTime('2014-1-1'));
$de->addParameter(new DateTime('2014-12-31 23:59:59'));
$de->sqlStatement = 'SELECT MONTH(OrderDate) AS Month, SUM(1) AS Sales FROM Orders
WHERE OrderDate >=? And OrderDate <=? GROUP BY MONTH(OrderDate)ORDER BY MONTH(OrderDate);';
//$de->dataFields = 'xAxis=Month,yAxis=Sales'; //default
$series = $de->getSeries();

//Multiple series can be generated from different sqlStatement
$de->sqlStatement = 'SELECT DatePeriod,Purchases As Costs FROM AreaData';
//$de->dataFields = 'xAxis=DatePeriod,yAxis=Costs';    //default
$series = $de->getSeries();

//Multiple series can be generated from different sqlStatement
$de->sqlStatement = 'SELECT DatePeriod,Purchases As Costs FROM AreaData';
//$de->dataFields = 'xAxis=DatePeriod,yAxis=Costs';    //default
$series = $de->getSeries();
?>


		<title>JSCharting  Multi Series DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get multiple series.
Learn how to:

  - Format MySql database Dates as month numbers.
  - Select MySql database data based on a date range.
  - Group MySql database results based on month number of dates.
  - Sort MySql database results by dates.
  - Use multiple SQL statements to get multiple series from a MySQL database.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 380,
  legendVisible: true,
  defaultSeriesType: 'line',
  toolbarVisible: false,
  yAxisFormatString: 'c',
  yAxisScale: { rangeMin: 0, interval: 200  },
  xAxisScale: { range: [1,12 ], interval: 1  },
  xAxisDefaultTickLabelText: xAxisFormatter,
  defaultPointTooltip: '<b>%yValue</b><br/>%percentOfSeries% of %seriesName',
  title: {
    label: {text: 'Multi series from different tables' }
  }
};


if(php_var =<?php echo json_encode($series) ?>)
  {chartJson.series =jQuery.parseJSON(php_var);}
chart = new JSC.Chart(chartJson);
function xAxisFormatter(val){
    return JSC.formatString(new Date(2014,val-1,1),'MMM');
    }


</script>
	</body>
</html>