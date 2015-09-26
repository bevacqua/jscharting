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
$de->sqlStatement = 'SELECT Sum(Purchases) as Purchases, Sum(Taxes) as Taxes,Sum(Supplies) as Supplies,Sum(Rent) as Rent, CASE WHEN  DatePeriod BETWEEN 1 AND  3 THEN "1" WHEN  DatePeriod BETWEEN 4 AND  6 THEN "2" WHEN  DatePeriod BETWEEN 7 AND  9 THEN "3" WHEN  DatePeriod BETWEEN 10 AND 12 THEN "4" END AS "Quarter" FROM AreaData Group by Quarter';
$de->dataFields = 'name=Quarter,yAxis=Purchases,yAxis=Taxes,yAxis=Supplies,yAxis=Rent';
$series = $de->getSeries();
?>


		<title>JSCharting  Quarters DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 610px">
</div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get series grouped by quarter.
Learn how to:

  - Sum numeric columns using MySQL database.
  - Group result from a MySQL database based on a data column.
  - Select MySql database data based on a column value range.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  defaultSeries: { type: 'columnAqua', transparency: 0.15  },
  palette: 'fiveColor32',
  yAxisScaleType: 'stacked',
  defaultPointLabelOffset: 15,
  height: 594,
  yAxisFormatString: 'c',
  yAxisLabelText: 'Spent (USD)',
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
    defaultTickLabelText: 'Q %value'
  },
  legend: {
    defaultEntryText: ' %name (%sum)',
    layout: 'horizontal',
    boxVisible: false,
    position: 'CA:1,1',
    visible: true
  },
  defaultPointTooltip: '%seriesName <b>%yValue</b><br/>{%percentOfGroup:n1}% of this month\'s cost<br/>{%percentOfTotal:n1}% of 2014 cost',
  titleLabelText: '2014 Spending'
};


if(php_var =<?php echo json_encode($series) ?>)
  {chartJson.series =jQuery.parseJSON(php_var);}
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>