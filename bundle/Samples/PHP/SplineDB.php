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
$de->sqlStatement = 'SELECT DatePeriod,Purchases,Taxes,Supplies,Rent FROM AreaData';
$de->dataFields = 'xAxis=DatePeriod,yAxis=Purchases,yAxis=Taxes,yAxis=Supplies,yAxis=Rent';
$series = $de->getSeries();
?>


		<title>JSCharting  Spline DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 940px; height: 420px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get multiple spline series.
Learn how to:

  - Query a MySQL database using PHP.
  - Get multiple series from a MySQL Database.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  defaultPointTooltip: '<b>%yValue</b><br/>%percentOfSeries% of %seriesName',
  width: 900,
  height: 400,
  defaultSeriesType: 'spline',
  titleLabelText: ' Costs (Last 12 Months) ',
  yAxisFormatString: 'c',
  yAxisLabelText: 'Costs',
  xAxisLabelText: 'Months'
};
if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      chartJson.xAxisDefaultTickLabelText = xAxisFormatter;
      }
chart = new JSC.Chart(chartJson);

function xAxisFormatter(val){
    return JSC.formatString(new Date(2014,val-1,1),'MMM');
    }


</script>
	</body>
</html>