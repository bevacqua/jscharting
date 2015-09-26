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
$de->sqlStatement = 'SELECT * FROM Employees';
$de->dataFields = 'name=name,yAxis=salary';
$series = $de->getSeries();
?>


		<title>JSCharting  Step Line DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get a data series.
Learn how to:

  - Query a MySQL database using PHP.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 380,
  legendVisible: false,
  defaultSeriesType: 'lineStep',
  xAxisDefaultTickLabelRotation: -45,
  yAxisLabelText: 'Salary',
  xAxisLabelText: 'Employees',
  toolbarVisible: true,
  yAxisFormatString: 'c',
  defaultPointTooltip: '%name: <b>%yValue</b><br/>%percentOfSeries% of total Salaries',
  title: { label: {text: 'Stepline sample' }  }
};


 if(php_var =<?php echo json_encode($series) ?>)
  {chartJson.series =jQuery.parseJSON(php_var);  }
     var chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>