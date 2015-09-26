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
$de->sqlStatement = 'SELECT Sum(Purchases) as Purchases, Sum(Taxes) as Taxes,Sum(Supplies) as Supplies,Sum(Rent) as Rent, Samples  DatePeriod BETWEEN 1 AND  3 THEN "1" WHEN  DatePeriod BETWEEN 4 AND  6 THEN "2" WHEN  DatePeriod BETWEEN 7 AND  9 THEN "3" WHEN  DatePeriod BETWEEN 10 AND 12 THEN "4" END AS "Quarter" FROM AreaData Group by Quarter';
$de->dataFields = 'name=Quarter,yAxis=Purchases,yAxis=Taxes,yAxis=Supplies,yAxis=Rent';
$series = $de->getSeries();
?>


		<title>JSCharting  Quarters Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 610px">
</div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get a single series.
Learn how to:

  - Sum numeric columns using SQL.
  - Group MySql result based on a data column.
  - Select MySql data based on column value ranges.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  defaultSeries: { type: 'column', transparency: 0.15  },
  defaultSeriesShading: 'aqua',
  palette: 'fiveColor32',
  yAxisScale: 'stacked',
  defaultPointLabelOffset: 15,
  height: 594,
  yAxisFormatString: 'c',
  yAxisLabelText: 'Units Sold',
  defaultPoint: { markerSize: 7  },
  marginRight: 20,
  legendDefaultEntryStyleFontSize: 13,
  yAxisDefaultTickLabelStyleFontSize: 14,
  xAxisDefaultTickLabelStyleFontSize: 16,
  xAxisDefaultTickLabelOffset: '0,20',
  yAxisLabelStyleFontSize: 17,
  xAxisLabelStyleFontSize: 17,
  titleLabelStyleFontSize: 16,
  xAxis: {
    labelText: 'Quarters',
    interval: 1,
    rangePadding: 0.0001,
    defaultTickLabelText: 'Q %value'
  },
  legend: {
    defaultEntryText: ' %name (%sum)',
    layout: 'horizontal',
    boxVisible: false,
    position: 'CA:1,1',
    visible: true
  },
  defaultPointTooltip: '%seriesName <b>$%yValue</b><br><%percentOfGroup,"n1">% of this month\'s cost<br><%percentOfTotal,"n1">% of 2014 cost',
  titleLabelText: '2014 Spending',
  defaultPointLabelStyleFontSize: 12
};


if(php_var =<?php echo json_encode($series) ?>)
  {chartJson.series =jQuery.parseJSON(php_var);}
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>