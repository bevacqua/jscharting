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
$de->sqlStatement = 'SELECT Code,Population FROM Locations';
$arrayData = $de->getArrayData();
?>


		<title>JSCharting  Map Thematic Population DB Chart</title>
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
  palette: {
    pointValue: '{%zValue}',
    colors: [
      '#f7fcfd',
      '#e0ecf4',
      '#bfd3e6',
      '#9ebcda',
      '#8c96c6',
      '#8c6bb1',
      '#88419d',
      '#810f7c',
      '#4d004b'
    ]
  },
  legend: {
    titleLabelText: 'Population <br>(Millions)  ',
    width: 80,
    defaultEntryText: '{%value/1000000:n1}M'
  },
  zAxisLabelText: 'Population',
  defaultPointLabelText: '%stateCode',
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