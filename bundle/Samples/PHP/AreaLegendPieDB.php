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
$de->sqlStatement = 'SELECT DatePeriod,Purchases,Taxes,Supplies,Rent FROM AreaData';
$de->dataFields = 'xAxis=DatePeriod,yAxis=Purchases,yAxis=Taxes,yAxis=Supplies,yAxis=Rent';
$series = $de->getSeries();
?>


		<title>JSCharting  Area Legend Pie DB Chart</title>
		  <style type="text/css">/*CSS*/</style>
	</head>
	<body>
	<div id="cc" style="width: 840px; height: 594px">
</div>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get multiple series.
Learn how to:

  - Query a MySQL database using PHP.
  - Get multiple series from a MySQL Database.
*/
// JS
var sumSeries = {
    name:'Summary',type:'pie',
    defaultPoint:{
        tooltip:'%name <b>%yValue</b><br/>{%percentOfSeries:n1}% of Total',
        labelText:'%name <br/>[%yValue | {%percentOfSeries:n1}%]',
        labelLineLength:3,
        labelStyle:{fontSize:12}
    },
cursor:'pointer',
    shape:{center:'200,100',size:110},
    points:[
        {name:'Purchases',y:1507.00,color:'#E51E19',id:'purchases'},
        {name:'Taxes',y:1320.90,color:'#FC7529'},
        {name:'Supplies',y:1312.10,color:'#F9F23D'},
        {name:'Rent',y:1149.90,color:'#8DEA55'}

    ]
};
var chart,php_var,chartJson={
  targetElement: 'cc',
  defaultSeries: { type: 'area', transparency: 0.15  },
  palette: 'fiveColor32',
  yAxisScaleType: 'stacked',
  defaultPointLabelOffset: 15,
  height: 594,
  yAxisFormatString: 'c',
  yAxisLabelText: 'Cost (USD)',
  defaultPoint: { markerSize: 7  },
  marginRight: 20,
  legendDefaultEntryStyleFontSize: 13,
  yAxisDefaultTickLabelStyleFontSize: 14,
  xAxisDefaultTickLabelStyleFontSize: 16,
  xAxisDefaultTickLabelOffset: '0,20',
  yAxisLabelStyleFontSize: 17,
  xAxisLabelStyleFontSize: 17,
  titleLabelStyleFontSize: 16,
  defaultPointEventsClick: toggleSer,
  xAxis: {
    labelText: '2014',
    scale: {interval: 1,rangePadding: 0.0001 },
    defaultTickLabelText: xAxisFormatter
  },
  legend: {
    defaultEntryText: ' %name [%sum | {%percentOfTotal:n1}%]',
    defaultEntryWidth: 210,
    width: 420,
    layout: 'horizontal',
    boxVisible: false,
    position: 'CA:1,1',
    visible: false
  },
  defaultPointTooltip: '%seriesName <b>%yValue</b><br/>{%percentOfGroup:n1}% of this month\'s cost<br/>{%percentOfTotal:n1}% of 2014 cost',
  titleLabelText: 'XYZ Inc 2014 Cost Chart. Total Costs (%sum)',
  defaultPointLabelStyleFontSize: 12
},jSer;


  if(php_var =<?php echo json_encode($series) ?>)
  {
      jSer=chartJson.series =jQuery.parseJSON(php_var);
      jSer[0].id='pur';
      jSer[1].id='tax';
      jSer[2].id='sup';
      jSer[3].id='ren';

      jSer.push(sumSeries);
  }
  chart = new JSC.Chart(chartJson);

  function xAxisFormatter(val){
      return JSC.formatString(new Date(2014,val-1,1),'MMM');
  }
  function toggleSer(e){
    if(this.series.name ==='Summary'){
        var id,isVis;
        switch(this.name){
            case 'Purchases':id='pur';break;
            case 'Taxes':id='tax';break;
            case 'Supplies':id='sup';break;
            case 'Rent':id='ren';break;
        }

        var s = chart.get(id);
        if(s){isVis = s.show();this.explode();  }

    }
}


</script>
	</body>
</html>