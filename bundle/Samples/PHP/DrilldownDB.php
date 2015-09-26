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

?>


		<title>JSCharting  Drilldown DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 640px; ">
</div>
<input id="drillUpBtn" type="button" value="Back to" >
	
<script type="text/javascript">
/*
Query a Database using a PHP script page to serve the drilldown data.
Learn how to:

  - Use the PHP DataEngine dateGrouping feature with MySql.
  - Get multiple series from a MySql Database.
  - Use a PHP script file to load data dynamically from MySql with Ajax.
  - Drilldown chart using MySql and PHP.
*/
// JS
var drillLevels = ["Years","Months","Days","Hours"];
var curLevel = 0;
var template = {
  targetElement: 'cc',
  legendVisible: false,
  defaultSeriesType: 'column',
  toolbarVisible: false,
  xAxisScaleInterval: 1,
  yAxisFormatString: 'c',
  legendDefaultEntryText: 'Total %name %sum '
};
var startDate;
var drillParamsStack=[];
var lastDrillParams=[];
var thisDrillParams=[];
var nowShowing='Years';
var palette ;
var chart;
var $drillUpBtn;
$(document).ready(function(){
    palette = JSC.getPalette('default');
    $drillUpBtn=$('#drillUpBtn');
    $drillUpBtn.click(function(){  drillUp(); })
    getData('Years','2013');
});

function drillUp(){
    curLevel--;
    drillParamsStack.pop();
    lastDrillParams=drillParamsStack.pop();
    getData(lastDrillParams[0],lastDrillParams[1]);
}

function updateChart(json){

    if(!chart){
        chart = new JSC.Chart({
            type:'column',
            template:template,
            defaultPointEventsClick:pointClick,
            series:json.series,
            xAxis:{ id:'xAxis', labelText:'Years'},
            title:{labelText:'Years 2013 to 2014, Total: %sum'}
        });
    }
  else{
        var chartSeries = chart.getSeries();
var xAxis = chart.get('xAxis');

        chartSeries[0].updatePoints(json.series[0].points);
        /*//Remove old current chart series
        for(var i = 0;i<chartSeries.length;i++){chartSeries[i].remove();  }
        //Add new chart series
        for(var i = 0;i<json.series.length;i++){chart.addSeries(json.series[i])  }*/
        chart.setTitle({labelText:nowShowing+', Total: %sum'});
        xAxis.setLabel( { text: drillLevels[curLevel] });
    }
    updateDrillUpButton();
}

function pointClick(e){
var a = 0;
    a++;
    // If clicked by a poit
    if(e){
        switch(drillLevels[curLevel]){
            case "Years":

                getData(drillLevels[++curLevel],this.name);
                break;
            case "Months":

                getData(drillLevels[++curLevel],this.replaceTokens( startDate.getFullYear() + '-%name-01'));
                break;
            case "Days":
                getData(drillLevels[++curLevel],this.replaceTokens( startDate.getFullYear()+ '-' +  (startDate.getMonth()+1) + '-%name'));
                break;
            case "hours":
                drillUp();
                break;
            default:
              break;
        }
    }
}

function updateDrillUpButton(){
    $drillUpBtn.show();
    switch(drillLevels[curLevel]){
        case "Years":
            $drillUpBtn.hide();
            break;
        case "Months":
            $drillUpBtn.attr({value:'Back to Years'});
            break;
        case "Days":
            $drillUpBtn.attr({value:'Back to '+JSC.formatString(startDate,'yyyy')});
            break;
        case "Hours":
            $drillUpBtn.attr({value:'Back to '+JSC.formatString(startDate,'MMMM yyyy')});
            break;
        default:
          break;
    }

}

function processChart(data, dateGrouping, fromDate)
{

    startDate = new Date ();
    var json = data;
    var point;
    var points = json.series[0].points;
    var year = parseInt(fromDate);
    var monthsAbbriviation= new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');

    json.series[0].name = "Sales";
    if(typeof points ==='string'){json.series[0].points = eval('('+points+')'); }

    if(dateGrouping=='Years')
    {
        // startDate = new Date (fromDate);

        startDate = new Date (year,0,1,0,0,0,0);
        nowShowing = 'Years 2013 to 2014';
        for(var m=0;m < json.series[0].points.length;m++)
        {
            point=json.series[0].points[m];
            point.color = palette[m];
        }

        //json.series[0].palette=palette;
        json.xAxis = { label: { text: 'Years'} };
    }
    else if(dateGrouping=='Months')
    {

        // var year = parseInt(fromDate);
        nowShowing='Sales for '+year;
        //    json.title={labelText : nowShowing+', Total: %sum'};
        startDate = new Date (year,0,1,0,0,0,0);

        json.xAxis = { label: { text: 'Months'} };
        for(var m=0;m < json.series[0].points.length;m++)
        {
            point=json.series[0].points[m];
            var objDate = new Date(point.name + "/1/" + startDate.getFullYear());
            point.name= JSC.formatString(objDate,'MMM');
            point.x=m;
            point.color =palette[year-2013];

        }
        //  json.series[0].color = palette[year-2013];

    }
    else if(dateGrouping=='Days')
    {
        var dateParts = fromDate.split('-');
        for(var m=0;m < json.series[0].points.length;m++) {
            point = json.series[0].points[m];
            point.color =palette[year-2013];
        }
        startDate = new Date (year,monthsAbbriviation.indexOf(dateParts[1]),1,0,0,0,0);// new Date(Date.parse(fromDate));


        nowShowing='Sales for '+JSC.formatString(startDate,'MMMM yyyy');


        json.xAxis = { label: { text: 'Days'} };
        //json.series[0].color = palette[year-2013];
    }
    else if(dateGrouping=='Hours')
    {
        var fromDate2 = fromDate.split("-");
        startDate = new Date (parseInt(fromDate2[0]),parseInt(fromDate2[1])-1,parseInt(fromDate2[2]),0,0,0,0);
        //startDate = new Date(Date.parse(fromDate));


        nowShowing='Sales for '+JSC.formatString(startDate,'d');

        for(var m=0;m < json.series[0].points.length;m++)
        {
            point = json.series[0].points[m];
            point.name= point.name.toString();
            point.color =palette[year-2013];
        }

        json.xAxis = { label: { text: 'Hours'} };
        // json.series[0].color = palette[year-2013];
    }
    //  json.title={labelText : nowShowing};
    updateChart(json);

}
function getData(dateGrouping, fromDate)
{
    drillParamsStack.push([dateGrouping,fromDate])
    lastDrillParams = thisDrillParams;
    thisDrillParams = [dateGrouping,fromDate];
    if(!dateGrouping)
    {  dateGrouping ="Years"; }
    if(!fromDate)
    {  fromDate = "2013"; }
    $.getJSON( "DrilldownDataDB.php?dategrouping="+dateGrouping+"&startDate="+fromDate, function( data ) {  processChart(data,dateGrouping,fromDate); }).fail(function(e) {  console.log(e.responseText ); });

}


</script>
	</body>
</html>