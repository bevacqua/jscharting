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
$de->sqlStatement = 'SELECT * FROM WorldPopulation';
$de->dataFields = 'xAxis=Year,yAxis=Population,zAxis=AnnualGrowth';
$series = $de->getSeries();
?>


		<title>JSCharting  Zooming XYDB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get a data series x, y, and z values.
Learn how to:

  - Query a MySQL database using PHP.
*/
// JS

var chartJson={
  targetElement: 'cc',
  legendVisible: false,
  height: 380,
  defaultSeries: { type: 'bubble', sizeMax: 20  },
  toolbarVisible: true,
  axisToZoom: 'xy',
  yAxis: { scaleZoomLimit: 1000000  },
  yAxisLabelText: 'World Population',
  yAxisLabelStyleFontSize: 17,
  xAxisLabelStyleFontSize: 17,
  title: { label: {text: 'World Population 1950-1985' }  },
  xAxis: {
    labelText: 'Years',
    formatString: 'd',
    scale: {zoomLimit: 1 }
  },
  annotations: [
    {
      position: 'CA:95,5',
      labelText: 'Click-Drag the chart area to zoom.',
      boxVisible: false
    }
  ]
};
var php_var;
if(php_var =<?php echo json_encode($series) ?>)
  {
      var php_json = jQuery.parseJSON(php_var);
      chartJson.series = php_json;

     }
var chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>