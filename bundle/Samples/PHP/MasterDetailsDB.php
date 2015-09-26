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
$startDate = new DateTime('2014-1-1');
$endDate = new DateTime('2014-12-31 23:59:59');
$title = 'Total Sales: %sum From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y');
$de->addParameter($startDate);
$de->addParameter($endDate);
$de->dataFields = 'name=Month,yAxis=Total Sales';
$de->sqlStatement = 'SELECT YEAR(OrderDate) AS Year, MONTH(OrderDate) AS Month, SUM(Total) AS "Total Sales" FROM Orders
WHERE OrderDate >=? And OrderDate <=? GROUP BY YEAR(OrderDate), MONTH(OrderDate)
ORDER BY YEAR(OrderDate), MONTH(OrderDate);';
$de->dateGrouping = "Year";//This setting shows all the months in the year regardless of having data on that month or not.
$series = $de->getSeries();
?>


		<title>JSCharting  Master Details DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="masterChart" style="width: 740px; height: 370px; margin: 0px auto;" >
                </div>
            <br/>
            <div id="detailsChart" style="width: 740px; height: 370px; margin: 0px auto;" >
                </div>
	
<script type="text/javascript">
/*
Query a Database using a PHP script page to serve the detailed monthly data.
Learn how to:

  - Format dates as year numbers from a MySql database.
  - Use the PHP DataEngine dateGrouping feature with MySql.
  - Use a PHP script file to load data dynamically from MySql with Ajax.
*/
// JS

var chart,chart2;



$(document).ready(function(){getData('2014-01-01');});

var chartJson={
    targetElement: 'masterChart',
    height: 380,
    legendVisible: false,
    defaultSeriesType: 'column',
    defaultPointTooltip: '{%percentOfTotal:n1}% of 2014 %seriesName',
    toolbarVisible: true,
    xAxisLabelText: 'Months',
    defaultSeriesPointSelection:true,
    xAxisScaleInterval: 1,
    yAxisFormatString: 'c',
    defaultPointEventsClick:pointClick,
    annotations:[{
    labelText:'Click a bar to see details for that month.',
        position:'CA:3,3',
        boxVisible:false
}]


};
var php_var =<?php echo json_encode($series) ?>;


        if(php_var)
        {
           var php_json = jQuery.parseJSON(php_var);
           chartJson.series = php_json;
           chartJson.xAxisDefaultTickLabelText = xAxisFormatter;
            chartJson.series[0].palette="default";
           chartJson.titleLabelText = <?php echo json_encode($title) ?>;
           var startDate = new Date (2014,1,1,0,0,0,0);


        }
      /*  else
        {
         chartJson.annotations =
         [
            {
              position: 'CA:10,10',
              labelText: 'No Data',
              labelColor: 'red',
              boxVisible: false
            }
        ];
       }*/
       var chart = new JSC.Chart(chartJson,function(){

    /*Select the first point in this chart*/
    this.getSeries()[0].points[0].select(true);
});

function processChart(json2,fromDate,color)
{
    //var json2 = jQuery.parseJSON(data);
    var fromDate2 = fromDate.split("-");
    var year = parseInt(fromDate2[0]);
    var mon = parseInt(fromDate2[1]);
    var day = parseInt(fromDate2[2]);
    var points = json2.series[0].points;
    if(typeof points ==='string'){ json2.series[0].points = eval('('+points+')'); }

    //json2.series[0].color=JSC.getPalette('default')[0];
    if(!chart2){


    chart2 = new JSC.Chart({
    "targetElement": "detailsChart",
    type:'column',
    xAxisLabelText:'Days',
    legendVisible:false,
    "yAxisFormatString": "c",
    title:json2.title,
    series:json2.series
    });
    }else{

    var points = json2.series[0].points;
     for (var m = 0,mLen=points.length; m < mLen; m++) {  points[m].color= color;  }
    chart2.getSeries()[0].updatePoints(json2.series[0].points );
    chart2.setTitle(json2.title);
   /* var chartSeries = chart2.getSeries();

    chart2.setTitle({labelText:json2.title.label.text});

    chartSeries[0].updatePoints(json2.series[0].points )*/



    }
    }

function getData(fromDate,color)
{
    if(!fromDate)
    {
    fromDate = "2014-1-1";
    }
    //$.getJSON( url+"?startDate="+fromDate, function( data ) {
    $.getJSON( "MasterDetailsDataDB.php?startDate="+fromDate, function( data ) {
    processChart(data,fromDate,color);
    });

    }
    function xAxisFormatter(val){
    return JSC.formatString(new Date(2014,val-1,1),'MMM');
    }


    function pointClick(e){
        getData(this.replaceTokens( startDate.getFullYear() + '-%name-01'),this.replaceTokens( '%color'));
    }


</script>
	</body>
</html>