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
$de->sqlStatement = 'SELECT Sum(Purchases) as Purchases, Sum(Taxes) as Taxes,Sum(Supplies) as Supplies,Sum(Rent) as Rent, CASE WHEN  DatePeriod BETWEEN 1 AND  3 THEN "1" WHEN  DatePeriod BETWEEN 4 AND  6 THEN "2" WHEN  DatePeriod BETWEEN 7 AND  9 THEN "3" WHEN  DatePeriod BETWEEN 10 AND 12 THEN "4" END AS "Quarter" FROM AreaData Group by Quarter';
$de->dataFields = 'name=Quarter,yAxis=Purchases,yAxis=Taxes,yAxis=Supplies,yAxis=Rent';
$series = $de->getSeries();
?>


		<title>JSCharting  Dynamic Data DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<table style="width: 640px; margin: 0px auto;">
        <tr>
            <td colspan="2" style="height: 400px;">
                <div id="cc" style="width: 635px; height: 400px"></div>
            </td>
        </tr>
        <tr>
            <td style="width: 500px;">
                <div id="cc2" style="width: 395px; height: 360px;"></div>
            </td>
            <td valign="top" style="padding-top: 35px;">
                <div id="cc3"></div>
            </td>
        </tr>
    </table>
	
<script type="text/javascript">
/*
Query a Database using PHP to add sum series to a separate chart.
Learn how to:

  - Sum numeric columns using MySQL database.
  - Group result from a MySQL database based on a data column.
  - Get multiple series from a MySQL Database.
*/
// JS
function hoverEvent(point){
    var tmpHtm='',i,
        oSeries = chart.get(this.config.attributes.parentID);

    $.each(oSeries.config.points,function(i,point){
        tmpHtm += chart.get(point.id).replaceTokens(entryTemplate);
    })

    $('#cc3').html(tableTemplate.replace('%entries%',tmpHtm));
}
function tooltipFormatter(point){
    //debugger;
    var txt='<b>%name</b>{%yValue:c}<br/>%percentOfTotal% of Total<br/>Per Quarter:<br/>',
        oSeries = chart.get(point.config.attributes.parentID);
    $.each(oSeries.config.points,function(i,point){
        txt += chart.get(point.id).replaceTokens('<b>%name</b> %yValue<br/>');
    })
    return txt;
}
var chart2,
    json2 = {
        targetElement: 'cc2',
        yAxisFormatString: 'c',
        defaultSeries:{shapePadding:.4,type: 'pie'  },
        titleLabelText:'Dynamic Sums',
        defaultPoint:{
            label:{text:'<b>%name</b> <br/>%yValue <br/> %percentOfTotal%',offset:15},
            tooltip:tooltipFormatter,
            eventsMouseOver:hoverEvent
        },
        height:350,
        annotations:[{
            position:'CA:1,1',boxVisible:false,
            labelText:'Generated automatically from above data.<br/>Hover a pie slice to get details from above chart.',
            transparency:.5
        }],
        series:[ { points:[] } ]
    };

var chartJson={
    targetElement: 'cc',
    defaultSeriesType: 'column',
    legendVisible: false,
    height: 400,
    titleLabelText: 'Original Database Data',
    defaultPointTooltip: 'Q%name %Yvalue<br/>%percentOfSeries% of %seriesName',
    yAxisFormatString: 'c',
    xAxis: {
        labelText: 'Quarters',
        scaleRangePadding: 0.0001,
        defaultTickLabelText: 'Q %value'
    }
};
function myCallback()
{
    // When the first chart is ready, this will generate the pie chart based on its data.
    // Try to get data calculations from the main chart.

    var calc = this.getCalculations();

    // Add series sums as a new series. Also, include the original series ID as attributes for each point so it can be used to build the table on hover..
    for(var i = 0;i<calc.sumList.length;i++){
        json2.series[0].points[i] = {name:calc.sumNames[i], y:calc.sumList[i], attributes:{parentID:calc.sumIDs[i]}}
    }
    // Generate the new chart.
    chart2 = new JSC.Chart(json2);

};
var entryTemplate='<tr><td style="width: 77px">%name</td><td>%yValue</td></tr>',
    tableTemplate='	<table style="width: 100%"><tr><td style="width: 77px; background-color: #E9E9E9"><strong>Quarter</strong></td><td style="background-color: #E9E9E9"><strong>Value</strong></td></tr>%entries%</table>';

var php_var =<?php echo json_encode($series) ?>;


if(php_var)
{

    var php_json = jQuery.parseJSON(php_var);
    chartJson.series = php_json;

    // Generate the chart and pass a callback for when the chart is rendered.
    var chart = new JSC.Chart(chartJson, myCallback);
}


</script>
	</body>
</html>