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
$de->addParameter(new DateTime('2013-03-01'));
$de->addParameter(new DateTime('2014-1-1'));
$de->sqlStatement = 'SELECT TransDate, HighPrice AS MSFT FROM MSFT WHERE TransDate >=? AND TransDate <=? ORDER BY TransDate';
$series = $de->getSeries();
?>


		<title>JSCharting  Navigator DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px;"></div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get a single series using date ranges.
Learn how to:

  - Select MySql database data based on a date range.
  - Sort MySql database results by dates.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  width: 840,
  legendVisible: true,
  defaultSeriesType: 'NavigatorLine',
  xAxisFormatString: 'd',
  yAxisFormatString: 'C'
},points;

if(php_var =<?php echo json_encode($series) ?>)
{
  chartJson.series =jQuery.parseJSON(php_var);
    points=chartJson.series[0].points;
    for (var m = 0,mLen=points.length; m < mLen; m++) { points[m].x= new Date(points[m].x); }

}
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>