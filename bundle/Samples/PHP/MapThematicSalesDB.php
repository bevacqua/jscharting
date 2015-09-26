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
$de->sqlStatement = 'SELECT Code,Sales FROM Locations';
$arrayData = $de->getArrayData();
?>


		<title>JSCharting  Map Thematic Sales DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 740px; height: 480px">
    </div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get thematic map data.
Learn how to:

  - Query a MySQL database using PHP.
*/
// JS

var chart,php_var,chartJson={
  targetElement: 'cc',
  type: 'map',
  height: 480,
  zAxisLabelText: 'Sales',
  palette: {
    pointValue: '{%zValue}',
    invert: true,
    colors: ['#006400','#00ff00','#ffff00','#ff0000','#8b0000' ]
  },
  legend: {
    titleLabelText: 'Sales',
    defaultEntryText: '$%value'
  },
  defaultPoint: { label: {text: '%stateCode' }  },
  defaultSeriesShapePadding: 0.02,
  series: [ {map: 'us' }  ]
};

php_var =<?php echo json_encode($arrayData) ?>;
var mapData = jQuery.parseJSON(php_var);
chartJson.series[0].points = $.map(mapData,function(item){ return {  map:'US.'+item[0],  z:item[1] }});
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>