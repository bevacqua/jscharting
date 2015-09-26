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
$de->sqlStatement = 'SELECT Name,Dem2012,Rep2012 FROM Locations';
$arrayData = $de->getArrayData();
?>


		<title>JSCharting  Map US Election Thematic DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 740px; height: 500px">
    </div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get multiple series.
Learn how to:

  - Query a MySQL database using PHP.
  - Get multiple series from a MySQL Database.
*/
// JS

var chart,php_var,chartJson={
  targetElement: 'cc',
  titleLabelText: '2012 Presidential Election Results',
  type: 'map',
  height: 500,
  toolbarVisible: false,
  legend: { position: '440,15', layout: 'horizontal'  },
  defaultPoint: {
    outlineColor: 'white',
    labelText: '%stateCode',
    tooltip: '%name<br/><b>Obama:</b> %obama%<br/><b>Romney:</b> %romney%'
  },
  series: [
    {
      name: 'Romney vs. Obama',
      palette: {
        pointValue: '{%obama}',
        stops: [
          /*This color stays solid for 30% of the range and ends at 70% where the next color becomes solid. These can be tightened with settings like .4,.6*/
          [0,'#bb4e55',0.3,0.7 ],
          [1,'#40698b' ]
        ]
      },
      points: []
    }
  ]
};

php_var =<?php echo json_encode($arrayData) ?>;
var results = jQuery.parseJSON(php_var);

var electionSeries = chartJson.series[0];

for (var i = 0, iLen = results.length; i < iLen; i++) {
    var stateRes = results[i];
    electionSeries.points.push({map:'US.name:'+stateRes[0],
        attributes:{ obama:stateRes[1], romney:stateRes[2]}
    })

}
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>