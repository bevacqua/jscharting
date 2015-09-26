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
  $de->sqlStatement = 'SELECT DatePeriod,AverageHigh,AverageLow FROM AreaData';
  $de->dataFields = 'xAxis=DatePeriod,yAxis=AverageHigh,yAxis=AverageLow';
  $series = $de->getSeries();
?>


		<title>JSCharting  Axis Shadowing DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; height: 400px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get multiple series on different axes.
Learn how to:

  - Query a MySQL database using PHP.
  - Get multiple series from a MySQL Database.
*/
// JS

var chart,php_var,chartJson={
  targetElement: 'cc',
  renderMode: 'JavaScript',
  defaultSeriesType: 'spline',
  height: 400,
  width: 640,
  palette: [ 'crimson', '#03bbfb'  ],
  legend: {
    position: 'CA:360,4',
    layout: 'vertical',
    boxVisible: false
  },
  title: {
    label: {
      text: 'Average Temperatures (Chicago)  |  Range %min°F - %max°F    Average: %average°F'
    },
    position: 'full'
  },
  yAxis: [
    {
      id: 'mainY',
      labelText: '(°F) Fahrenheit',
      defaultTickLabel: {
        text: '%value°F',
        style: { fontWeight: 'bold', fontSize: '12px'  }
      },
      markers: [
        {
          value: [0,32 ],
          labelText: 'Freezing',
          labelStyleFontSize: 14,
          labelAlign: 'center',
          color: ['#65affb',0.5 ]
        },
        {
          value: 72,
          labelText: 'Perfect (72°F)',
          labelStyleFontSize: 14,
          lineWidth: 3,
          color: ['#fcc348',0.5 ]
        }
      ]
    },
    {
      scaleSyncWith: 'mainY',
      orientation: 'right',
      formatString: 'n2',
      labelText: '(°C) Celcius',
      defaultTickLabel: {
        text: '{(%Value-32)*5/9:n1}°C',
        style: { fontWeight: 'bold', fontSize: '12px'  }
      }
    }
  ],
  xAxis: {
    formatString: 'MMM',
    labelText: 'Months',
    scale: {interval: 1,rangePadding: 0.0001 },
    defaultTickLabel: {
      style: {  fontWeight: 'bold',  fontSize: '12px'}
    },
    defaultTickLabelText: xAxisFormatter
  },
  defaultPointTooltip: '<b>%xValue</b><br/>%seriesName: %yValue°F  ({(%yValue-32)*5/9:n1}°C)',
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