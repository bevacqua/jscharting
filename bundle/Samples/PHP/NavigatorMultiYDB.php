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
$de->addParameter(new DateTime('2013-01-01'));
$de->addParameter(new DateTime('2013-03-31'));
$de->sqlStatement = 'SELECT TransDate, HighPrice, LowPrice, OpenPrice, ClosePrice, Volume FROM MSFT WHERE TransDate >=? AND TransDate <=? ORDER BY TransDate';
$de->dataFields = 'xAxis=TransDate,High=HighPrice,Low=LowPrice,Open=OpenPrice,Close=ClosePrice,Volume=Volume';
$series = $de->getSeries();
?>


		<title>JSCharting  Navigator Multi YDB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px;"></div>
	
<script type="text/javascript">
/*
Query a Database using PHP to get OHLC financial data on a CandleStick chart including volume data.
Learn how to:

  - Select MySql database data based on a date range.
  - Sort MySql database results by dates.
*/
// JS
var chart,php_var,chartJson={
  targetElement: 'cc',
  renderMode: 'JavaScript',
  type: 'NavigatorLine',
  height: 300,
  yAxis: [
    {formatString: 'C',labelText: 'price' },
    {
      id: 'y2',
      orientation: 'right',
      labelText: 'volume'
    }
  ],
  navigator: {
    toolbar: {visible: true },
    previewArea: {
      visible: true,
      height: 45,
      margin: 5,
      interactivity: 'cells,scrollWheel,arbitrarySelection',
      seriesSettings: {
        type: 'area',
        transparency: 0.01,
        color: 'lightgreen'
      }
    },
    xScrollbarEnabled: true,
    silverlight: {loadingAnimation: 'none' }
  }
},points;

if(php_var =<?php echo json_encode($series) ?>)
{
  chartJson.series =jQuery.parseJSON(php_var);
    points=chartJson.series[0].points;
    for (var m = 0,mLen=points.length; m < mLen; m++) { points[m][0]= new Date(points[m][0]); }

    var ohlcPoints = [];
    var volData= function(){
        var result = {name:'Volume',points:[]};
        for(var i in points){
            var p = points[i];
            result.points.push([p.x,p.volume]);
    ohlcPoints.push([p.x,p.open,p.high,p.low,p.close]);
    delete p.volume;
        }
    chartJson.series[0].points = ohlcPoints;
        result.type='column';
        return result;
    }();
   // chartJson.series[0].yAxis=0;
    var vol = volData;
    vol.yAxis = 'y2';
    vol.transparency=0.2;
    chartJson.series.push(vol);

}
chart = new JSC.Chart(chartJson);


</script>
	</body>
</html>