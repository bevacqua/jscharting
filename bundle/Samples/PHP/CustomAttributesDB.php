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
$de->addParameter("Aida");
$de->addParameter("George");
$de->addParameter("Joe");
$de->addParameter("David");
$de->sqlStatement = 'SELECT id,name,salary,Location,phone,Picture FROM Employees where name =? or name =? or  name =? or name =?';
$de->dataFields = 'xAxis=name,yAxis=id,Location=location,phone=phone,Picture=img';
$series = $de->getSeries();
?>


		<title>JSCharting  Custom Attributes DB Chart</title>
		  <style type="text/css">.infoDiv{
      vertical-align: top;
      top: -110px;
      position:relative;
      }
  .infoTable {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 13px;
      width: 220px;
      margin-top: 40px;
      padding: 0px;
      vertical-align: top;
      }
  .infoTable th {
      background-color: #E0E0E0;
      font-weight: bold;
      padding: 5px;
      border:0
      }
  .infoTable td { padding: 5px; border-style: none;}
  .infoTable .altRow { background-color: #F9F9F9;border-style:  none;}
  .iTblMCol { text-align: center;}</style>
	</head>
	<body>
	<table >
          <tr>
              <td  >
                  <div id="cc" style="width: 656px; height: 410px">
                  </div>
              </td>
              <td >
                  <div id="pie" class="infoDiv">
                      <table id="sideTable"  cellspacing="0" class="infoTable">
                          <thead>
                              <tr>
                                  <th>Rep</th>
                                  <th class="iTblMCol">Carats Sold</th>
                                  <th>Value </th>
                              </tr>
                          </thead>
                          <tbody></tbody>
                      </table>
                  </div>
                  <div id="div2" class="infoDiv" >

                  </div>
              </td>
          </tr>
      </table>
	
<script type="text/javascript">
/*
Query a MySQL Database using PHP to get additional data attributes.
Learn how to:

  - Query a MySQL database using PHP.
  - Get custom data attributes from a MySQL Database.
*/
// JS
var clickableCol='#222222',
    valPerCarat = 11875,
    htmTemplate = '<table cellspacing="0" border="4" bordercolorlight="%color" bordercolordark="%color" bordercolor="%color" class="infoTable"><tr><th style="background-color: %color;"><img height="64" src="../images/%img" width="64"></th><th style="background-color: %color;">%name<br />%location</th></tr><tr><td>Contact</td><td>%phone</td></tr><tr class="altRow"><td>Carats</td><td>%yValue ct</td></tr><tr><td>Sales</td><td>{%yValue*'+valPerCarat+':c}</td></tr><tr class="altRow"><td>Percent</td><td>{%percentOfTotal:n1}%</td></tr></table>',
    sideTableRow = '<tr><td>%name</td><td class="iTblMCol">%yValue ct</td><td>{%yValue*'+valPerCarat+':c}</td></tr>';



$("#div2").hide();

var chart,php_var,chartJson={
  targetElement: 'cc',
  height: 400,
  toolbarVisible: false,
  titleLabel: {
    text: 'Diamond sales in carats. | Total: %sum ct  | Average carats per Rep: {%average:n2} ct',
    styleFontSize: 11,
    color: clickableCol
  },
  defaultSeries: { type: 'columnAqua'  },
  defaultPoint: {
    statesHover: {outline: {  width: 5} },
    label: {
      styleFontSize: 11,
      text: '%yValue ct [{%yValue*11875:c}]<br/> %location',
      color: clickableCol
    },
    eventsMouseOver: pointEvent,
    tooltip: '<b>%name</b> %yValue ct<br/>%percentOfTotal% of Total Sales<br/>{%yValue*11875:c}'
  },
  xAxis: {
    defaultTick: {label: {  styleFontSize: 16,  offset: '0,20'} },
    labelStyleFontSize: 17
  },
  legend: {
    position: 'CA:1,1',
    boxVisible: false,
    fill: ['rgba(255,255,255,.51)',false ],
    defaultEntry: {
      text: '<b>Out of %pointCount %name</b>  <br/>  Top Rep: <b>%maxPointName</b> (%max ct)   <br/>  Worse: <b>%minPointName</b> (%min ct)',
      color: clickableCol,
      iconWidth: 1
    }
  },
  yAxis: [
    {
      id: 'mainY',
      label: {  text: 'Diamond carats',  styleFontSize: 17},
      defaultTickLabel: {
        text: '%value ct',
        color: clickableCol,
        style: { fontWeight: 'bold', fontSize: '12px'  }
      }
    },
    {
      scaleSyncWith: 'mainY',
      id: 'rightY',
      orientation: 'right',
      formatString: 'c',
      label: {  text: 'Value (USD)',  styleFontSize: 17},
      defaultTickLabel: {
        text: '{%value*11875}',
        style: { fontWeight: 'bold', fontSize: '12px'  }
      }
    }
  ]
};


  if(php_var =<?php echo json_encode($series) ?>)
  {
      chartJson.series =jQuery.parseJSON(php_var);
      chartJson.series[0].palette='default';

  }
  chart = new JSC.Chart(chartJson,populateTable);

  function LightenDarkenColor(col,amt) {
      var usePound = false;
      if ( col[0] == "#" ) {col = col.slice(1);usePound = true;}
      var num = parseInt(col,16);
      var r = (num >> 16) + amt;
      if ( r > 255 ) r = 255;
      else if  (r < 0) r = 0;
      var b = ((num >> 8) & 0x00FF) + amt;
      if ( b > 255 ) b = 255;
      else if  (b < 0) b = 0;
      var g = (num & 0x0000FF) + amt;
      if ( g > 255 ) g = 255;
      else if  ( g < 0 ) g = 0;
      return (usePound?"#":"") + (g | (b << 8) | (r << 16)).toString(16);
      }

  function pointEvent(e) {
      var col,result;
      col = this.replaceTokens('%color').replace('#','');
      col = LightenDarkenColor(col,-50);//5
      if(col.length == 4) col = '00'+col;
      if(col.length == 5) col = '0'+col;

      result = this.replaceTokens(htmTemplate.replace(/%color/g,col));
      $("#pie").hide();
      $("#div2").html(result);
      $("#div2").show();
      }

  function populateTable() {
      var ser;
      if (ser = this.getSeries()) {
      var points = ser[0].points;
      for (var i = 0; i < points.length; i++) {
      $("#sideTable tbody").append(points[i].replaceTokens(sideTableRow));
      }
      }
      }


</script>
	</body>
</html>