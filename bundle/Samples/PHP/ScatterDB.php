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
$de->sqlStatement = 'SELECT Experience,salary,name As EmployeeName FROM Employees';
$de->dataFields = 'xAxis=Experience,yAxis=salary,EmployeeName';
$series = $de->getSeries();
?>


		<title>JSCharting  Scatter DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get multiple series split by another field in the database.
Learn how to:

  - Query a database using PHP.
  - Get point tooltips from a database.
  - Use the SplitBy feature of DataEngine to split MySQL database data into multiple series based on a data field.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 400,
  legendVisible: false,
  defaultSeriesType: 'marker',
  toolbarVisible: true,
  xAxisScale: { range: [0,36 ], interval: 5  },
  yAxisFormatString: 'c',
  xAxisLabelText: 'Years of Experience',
  yAxisLabelText: 'Income (USD)',
  defaultPointTooltip: '%EmployeeName earns %yValue with %xValue years experience',
  title: { label: {text: 'Scatter sample' }  }
};
if(php_var =<?php echo json_encode($series) ?>)
  {chartJson.series =jQuery.parseJSON(php_var);}
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>