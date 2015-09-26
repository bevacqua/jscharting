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
  $de->sqlStatement = 'SELECT * FROM AreaData';
  $de->dataFields = 'xAxis=DatePeriod,yAxis=Purchases,yAxis=Taxes,yAxis=Supplies,yAxis=Rent';
  $series = $de->getSeries();
?>


		<title>JSCharting  Bar DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 740px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get multiple bar series.
Learn how to:

  - Query a MySQL database using PHP.
  - Get multiple series from a MySQL Database.
*/
// JS

var chart,php_var,chartJson={
  targetElement: 'cc',
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
    label: {text: 'Months',styleFontSize: 13 },
    scaleInterval: 1,
    defaultTickLabelText: xAxisFormatter
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

  function xAxisFormatter(val){
      return JSC.formatString(new Date(2014,val-1,1),'MMM');
  }


</script>
	</body>
</html>