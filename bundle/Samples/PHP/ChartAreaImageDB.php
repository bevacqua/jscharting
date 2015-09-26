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


		<title>JSCharting  Chart Area Image DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get multiple shaded bar series on a chart with a background image.
Learn how to:

  - Query a MySQL database using PHP.
  - Get multiple series from a MySQL Database.
*/
// JS

var chart,php_var,chartJson={
  targetElement: 'cc',
  chartAreaFillImage: '../images/background.jpg',
  height: 400,
  defaultSeries: { type: 'columnAqua', transparency: 0.15  },
  defaultPointTooltip: '%seriesName <b>%yValue</b><br/>{%percentOfGroup:n1}% of this month\'s cost<br/>{%percentOfTotal:n1}% of 2014 cost',
  titleLabel: { text: '2014 Spending', styleFontSize: 12  },
  yAxis: {
    formatString: 'c',
    labelText: 'Spent (USD)',
    labelStyleFontSize: 13
  },
  xAxis: {
    label: {text: 'Quarters',styleFontSize: 13 },
    scaleInterval: 1,
    defaultTickLabelText: '{new Date(2014,((%value-1)*3),1):MMM} (Q%value)'
  },
  legend: {
    defaultEntryText: ' %name (%sum)',
    layout: 'horizontal',
    boxVisible: false,
    position: 'CA:1,1'
  },
  toolbarVisible: false
};


  if(php_var =<?php echo json_encode($series) ?>)
  {chartJson.series =jQuery.parseJSON(php_var);  }
  chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>