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
//First Chart Data
$de = new DataEngine();
$startDate = new DateTime('2014-1-1');
$endDate = new DateTime('2014-12-31 23:59:59');
$de->addParameter($startDate);
$de->addParameter($endDate);
$title = 'Orders From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y');
$de->sqlStatement = 'SELECT MONTH(OrderDate) AS Month, SUM(1) AS Orders FROM Orders
WHERE OrderDate >=? And OrderDate <=? GROUP BY MONTH(OrderDate)ORDER BY MONTH(OrderDate);';
$series = $de->getSeries();

//Second Chart Data
$de = new DataEngine();
$de->addParameter($startDate);
$de->addParameter($endDate);
$title2 = 'Sales From ' . date_format($startDate, 'm/d/Y') . ' to ' . date_format($endDate, 'm/d/Y');
$de->sqlStatement = 'SELECT MONTH(OrderDate) AS Month, SUM(Total) AS Sales FROM Orders
WHERE OrderDate >=? And OrderDate <=? GROUP BY MONTH(OrderDate)ORDER BY MONTH(OrderDate);';
$series2 = $de->getSeries();
?>


		<title>JSCharting  Multi Chart DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 400px"></div>
            <div id="cc2" style="width: 840px; height: 400px"></div>
	
<script type="text/javascript">
/*
Multiple charts that query a Database using PHP.
Learn how to:

  - Select MySql database data based on a date range.
  - Create a chart title using PHP.
*/
// JS
var chart,php_var,php_var2;
//First Chart
var chartJson={
    targetElement: 'cc',
    height: 380,

    defaultSeriesType: 'column',
    defaultPointTooltip: '%yValue <br/>{%percentOfTotal:n1}% of 2014 %seriesName',
    toolbarVisible: true,

    xAxisLabelText: 'Months',
    xAxisScaleInterval: 1,
    yAxisLabelText: 'Number of Orders',
    legendPosition:'CA:5,5',
    legendDefaultEntryText: '<b>Total %name</b>: %sum<br/>',
    defaultPointColor: '#FF6600'
};

if(php_var =<?php echo json_encode($series) ?>)
                    {
                        var php_json = jQuery.parseJSON(php_var);
                        chartJson.series = php_json;

                        chartJson.xAxisDefaultTickLabelText = xAxisFormatter;
                        chartJson.titleLabelText = <?php echo json_encode($title) ?>;
                        }



//Second Chart
var chart2Json={
    targetElement: 'cc2',
    height: 380,


    defaultSeriesType: 'column',
    defaultPointTooltip: '%yValue <br/>{%percentOfTotal:n1}% of 2014 %seriesName',
    toolbarVisible: true,
    xAxisLabelText: 'Months',
    xAxisScaleInterval: 1,
    yAxisFormatString: 'c',
    yAxisLabelText: 'Sales (USD)',
    legendPosition:'CA:5,5',
    legendDefaultEntryText: '<b>Total %name</b>: %sum<br/>'
    };

    if(php_var2 =<?php echo json_encode($series2) ?>)
                    {

                        chart2Json.series =  jQuery.parseJSON(php_var2);
                        chart2Json.xAxisDefaultTickLabelText = xAxisFormatter;
                        chart2Json.titleLabelText = <?php echo json_encode($title2) ?>;
                        }
    var chart = new JSC.Chart(chartJson,function (){  var chart2 = new JSC.Chart(chart2Json); });


    function xAxisFormatter(val){
        return JSC.formatString(new Date(2014,val-1,1),'MMM');
        }


</script>
	</body>
</html>