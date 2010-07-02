<?php

ini_set("memory_limit","100M");
require_once "lib/util/FileReader.php";

$maxColumns = 8;
$defaultW = 650;
$defaultH = 200;
$minXY = 200;
$maxXY = 800;

$fileid = isset($_REQUEST['fileid']) ? $_REQUEST['fileid'] : null;

if($fileid) {
  $df = DataFilePeer::find($fileid);

  if(!$df) {
    exit("Data file not found on database!");
  }

  $filePath = $df->getFullPath();

  if(!file_exists($filePath)) {
    exit("Data file not found on disk!");
  }

  if(is_dir($filePath)) {
    exit("Data file is a directory.");
  }

  if(!is_readable($filePath)) {
    exit("Data file cannot be read.");
  }

  $filename = $df->getName();

  $graphBaseURL = "/common/graph.php?fileid=$fileid";

  $graphTitle = $df->getTitle();
  if (empty($graphTitle)) $graphTitle = $df->getName();
}
else {
  $fileurl = isset($_REQUEST['fileurl']) ? rawurldecode($_REQUEST['fileurl']) : null;

  $filename = "tmp_data_" . rand() . ".txt";
  $filePath = "/tmp/" . $filename;

  $graphTitle = basename($fileurl);

  $fileurl = str_replace(" ", "%20", $fileurl);

  try {
    $fileReader = fopen($fileurl, "r");
    $writer=fopen($filePath,"w");

    while ($line = fread($fileReader, 4096)) {
      fwrite($writer,$line);
    }
    fclose($writer);
    fclose($fileReader);

    $graphBaseURL = "/common/graph.php?fileurl=$fileurl";
  }
  // an error occured when trying to open the specified url
  catch (Exception $e) {
    exit("Cannot read data URL");
  }
}

$reader = new FileReader($filePath);

$startLineNumber = isset($_REQUEST['line']) ? $_REQUEST['line'] : null;

if($startLineNumber) {
  $reader->setStartLineNumber($startLineNumber);
  $graphBaseURL .= "&line=" . $startLineNumber;
}

$labels = $reader->getLabels();

$error = null;
if(!is_array($labels)) {
  $error = "Graphic tool cannot recognize this file as a valid data file to graph. This version of graphing tool only accept text file format. Binary file, excel file is not yet supported. " .
  "<br/><br/>If you believe this is a valid data file and can be graphed, please review the options below and make change if necessary. " .
  "<br/><br/>Specially, try to change the <b>'Start Line Number'</b> to ignore invalid header lines, then click on the <b>'Re-Validate Data File'</b> to re-try.";
}

$numCols = $reader->getNumCols();
$delim = $reader->getDelimeter();
$startPosition = $reader->getStartPosition();

$delimiter = isset($_REQUEST['delimiter']) ? $_REQUEST['delimiter'] : "tab";
$isNumeric = isset($_REQUEST['numeric']) && $_REQUEST['numeric'] == 0 ? 0 : 1;

$deltaT = 1;
if(!$isNumeric) {
  $deltaT = isset($_REQUEST['deltaT']) ? $_REQUEST['deltaT'] : 1;
}

$selectXDomain = isset($_REQUEST['xdomainStart']) && isset($_REQUEST['xdomainEnd']);

if($selectXDomain) {
  $xdomainStart = $_REQUEST['xdomainStart'];
  $xdomainEnd = $_REQUEST['xdomainEnd'];

  if(!is_numeric($xdomainStart) || !is_numeric($xdomainEnd)) {
    exit("<div class='error contentpadding'>Select X-Domain does not have valid values</div>");
  }
}
else {
  $xdomainStart = null;
  $xdomainEnd = null;
}

$doit = isset($_REQUEST['doit']) ? $_REQUEST['doit'] : false;

if($doit == "outputApplet") {
  $columnsStr = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : "";
  $title = isset($_REQUEST['title']) ? rawurldecode($_REQUEST['title']) : $filename;
  $xlabel = isset($_REQUEST['xlabel']) ? rawurldecode($_REQUEST['xlabel']) : $labels[0];
  $ylabel = isset($_REQUEST['ylabel']) ? rawurldecode($_REQUEST['ylabel']) : "Y-Label";
  $graphtype = isset($_REQUEST['graphtype']) ? $_REQUEST['graphtype'] : "line";
  $separate = isset($_REQUEST['separate']) && $_REQUEST['separate'] == 1;

  if(empty($xlabel)) $xlabel = $labels[0];
  if(empty($ylabel)) $ylabel = "Y";

  $width = $defaultW;
  if(isset($_REQUEST['w']) && is_int(0 + $_REQUEST['w'])) $width = $_REQUEST['w'] + 0;

  $height = $defaultH;
  if(isset($_REQUEST['h']) && is_int(0 + $_REQUEST['h'])) $height = $_REQUEST['h'] + 0;

  if($graphtype == "bar") {
    $typeParam = " -nl -bar";
  }
  elseif($graphtype == "dot") {
    $typeParam = "-nl -P";
  }
  elseif($graphtype == "scatter") {
    $typeParam = "-nl -p";
  }
  elseif($graphtype == "marker") {
    $typeParam = "-nl -m";
  }
  elseif($graphtype == "impulses") {
    $typeParam = "-impulses";
  }
  else {
    $typeParam = "";
  }

  $selectXDomainQuery = $selectXDomain ? "&xdomainStart=$xdomainStart&xdomainEnd=$xdomainEnd" : "";

  if($separate) {
    $columns = explode(",", $columnsStr);

    $ret = "";
    $colorIndex = 0;
    foreach($columns as $column) {

      $ret .= <<<ENDHTML

    <div style="text-align:center;">
      <applet width='$width' height='$height' code='ptolemy.plot.PlotApplet.class' archive='/ptolemy/plot/ptolemy.plotapplet.jar'>
        <param name='dataurl' value='$graphBaseURL&columns=$column&delimiter=$delimiter&doit=dataurl$selectXDomainQuery&numeric=$isNumeric&deltaT=$deltaT&colorIndex=$colorIndex'/>
        <param name='pxgraphargs' value='-t "$title" -x "$xlabel" -y "$ylabel" $typeParam -bg "EEEEEE"'/>
      </applet>
      <br/><br/><a href="$graphBaseURL&columns=$column&delimiter=$delimiter&doit=dataurl$selectXDomainQuery&download=1" class="button mini">Extract Graphical Data</a>
    </div>

    <br/><br/><br/>

ENDHTML;

      $colorIndex++;
    }
  }
  else {
    $ret = <<<ENDHTML

    <div style="text-align:center;">
      <applet width='$width' height='$height' code='ptolemy.plot.PlotApplet.class' archive='/ptolemy/plot/ptolemy.plotapplet.jar'>
        <param name='dataurl' value='$graphBaseURL&columns=$columnsStr&delimiter=$delimiter&doit=dataurl$selectXDomainQuery&numeric=$isNumeric&deltaT=$deltaT'/>
        <param name='pxgraphargs' value='-t "$title" -x "$xlabel" -y "$ylabel" $typeParam -bg "EEEEEE"'/>
      </applet>
      <br/><br/><a href="$graphBaseURL&columns=$columnsStr&delimiter=$delimiter&doit=dataurl$selectXDomainQuery&download=1" class="button mini">Extract Graphical Data</a>
    </div>

ENDHTML;
  }

  print $ret;
  exit;
}
elseif($doit == "dataurl") {
  $columnsStr = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : "";
  $download = isset($_REQUEST['download']);
  $colorIndex = isset($_REQUEST['colorIndex']) ? $_REQUEST['colorIndex'] : 0;

  $columns = explode(",", $columnsStr);

  if($delimiter == "comma") $delim = ",";
  elseif($delimiter == "space") $delim = " ";
  elseif($delimiter == "colon") $delim = ":";
  elseif($delimiter == "pipe") $delim = "|";
  else $delim = "\t";

  if($download) {
    $output = rand() . "_" . str_replace(",", "_", $columnsStr) . ".txt";
    $reader->getDataDownload($columns, $delim, $output, $xdomainStart, $xdomainEnd);
  }
  else {
    $reader->getDataURL($columns, $delim, $isNumeric, $deltaT, $xdomainStart, $xdomainEnd, $colorIndex);
  }
}
/**
 * Main page: for user input for Graph
 */
else {
  $selectForm = "";

  $errormsg = "";
  if($error) {
    $errormsg = "<div class='error contentpadding'>$error</div>";

    $submitButton = "<input type='button' value='Re-Validate Data File' class='btn' onClick='javascript:revalidate=true; selectChannel(); ' />";
  }
  else {
    $selectSize = $numCols > 8 ? "size='8'" : "";
    $selectForm = "Select column(s): <span class='orange'>*</span> <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover=\"Tip('Select one more more dataset (ctrl+click) from the drop down list. If you do not see the list, make sure your data file is a valid format that can be graphed.', WIDTH, 300)\" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a><br/><select name='labels' id='labels' multiple='multiple' $selectSize>";
    for($col=1; $col < $numCols; $col++) {
      $selected = $col == 1 ? " selected='selected'" : "";
      $selectForm .= "<option value='" . $col . "'" . $selected . ">" . $labels[$col] . "</option>";
    }
    $selectForm .= "</select>";

    $submitButton = "<input type='button' value='Generate Graph' class='btn' onClick='javascript:selectChannel();' /><img src='/images/pixel.gif' width='16' height='16' id='loading' alt=''/>";
  }

  $tabSelected = $delim == "\t" ? "selected='selected'" : "";
  $commaSelected = $delim == "," ? "selected='selected'" : "";
  $xTitle = $labels[0];

  $nuremicalSelected = $isNumeric ? " checked='checked'" : "";
  $timestepSelected = $isNumeric ? "" : " checked='checked'";

  $autoGraph = isset($_REQUEST['autoGraph']) && $_REQUEST['autoGraph'] == 1 ? "<script type='text/javascript'>selectChannel();</script>" : "";

  $selectDelimiter = <<<ENDHTML

  <select name='delimiter' id='delimiter'>
    <option value='tab' $tabSelected>Tab delimited</option>
    <option value='comma' $commaSelected>Comma delimited</option>
    <option value='space'>Space delimited</option>
    <option value='colon'>Colon delimited</option>
    <option value='pipe'>Pipe delimited</option>
  </select>

ENDHTML;

  $content = <<<ENDHTML


<script type="text/javascript">
<!--

var xmlHttp;
var rename_fileid = -1;
var rename_newname = "";
var rename_formId = "";
var revalidate = false;

function selectChannel(){
  var minXY = $minXY;
  var maxXY = $maxXY;
  var defaultW = $defaultW;
  var defaultH = $defaultH;

  var numericalBox = document.getElementById('numerical');
  var xdomainAllBox = document.getElementById('xdomainAll');
  var selectLabelBox = document.getElementById('labels');
  var separateBox = document.getElementById('separate1');
  var selectTypeBox = document.getElementById('type');
  var selectDelimiterBox = document.getElementById('delimiter');
  var titleBox = document.getElementById('title');
  var xlabelBox = document.getElementById('xlabel');
  var ylabelBox = document.getElementById('ylabel');
  var wBox = document.getElementById('w');
  var hBox = document.getElementById('h');
  var startLineNumberBox = document.getElementById('line');

  var graphtype = selectTypeBox.options[selectTypeBox.selectedIndex].value;
  var del = selectDelimiterBox.options[selectDelimiterBox.selectedIndex].value;
  var isSeparated = separateBox.checked ? 1 : 0;
  var selectedArray = new Array();

  var xDomainQuery = "&numeric=1";


  if(!numericalBox.checked) {
    deltaTBox = document.getElementById('deltaT');
    deltaTValue = parseFloat(deltaTBox.value);

    if(isNaN(deltaTValue)) {
      deltaTBox.focus();
      alert("Please enter a number for delta-T if your first column (x-domain) is not in numeric format.");
      return;
    }

    if(deltaTValue == 0) {
      deltaTBox.focus();
      alert("Delta T must be a a none-zero number.");
      return;
    }

    deltaTBox.value = deltaTValue;
    xDomainQuery = "&numeric=0&deltaT=" + deltaTValue;
  }

  var xDomainSelectQuery = "";

  if(!xdomainAllBox.checked) {
    xdomainStartBox = document.getElementById('xdomainStart');
    xdomainEndBox = document.getElementById('xdomainEnd');
    xdomainStartValue = parseFloat(xdomainStartBox.value);
    xdomainEndValue = parseFloat(xdomainEndBox.value);

    if(isNaN(xdomainStartValue)) {
      xdomainStartBox.focus();
      alert("Please enter a valid number for X-Domain start value.");
      return;
    }

    if(isNaN(xdomainEndValue)) {
      xdomainEndBox.focus();
      alert("Please enter a valid number for X-Domain end value.");
      return;
    }

    if(xdomainStartValue == xdomainEndValue) {
      xdomainStartBox.focus();
      alert("Start value for X-Domain must different to end value for X-Domain");
      return;
    }

    xdomainStartBox.value = xdomainStartValue;
    xdomainEndBox.value = xdomainEndValue;

    xDomainSelectQuery = "&xdomainStart=" + xdomainStartValue + "&xdomainEnd=" + xdomainEndValue;
  }

  var width = parseInt(wBox.value);
  var height = parseInt(hBox.value);

  if(width > maxXY || width < minXY || height > maxXY || height < minXY || isNaN(width) || isNaN(height)) {
    wBox.focus();
    alert("Please change your width and height to a valid range " + minXY + " <= w, h <= " + maxXY);
    return;
  }

  wBox.value = width;
  hBox.value = height;

  startLineNumberQuery = "&line=" + startLineNumberBox.value;

  if(revalidate) {
    var url="$graphBaseURL&title=" + escape(titleBox.value) + "&xlabel=" + escape(xlabelBox.value) + "&ylabel=" + escape(ylabelBox.value) + "&graphtype=" + graphtype + "&w=" + width + "&h=" + height + "&delimiter=" + del + "&separate=" + isSeparated + xDomainQuery + xDomainSelectQuery + startLineNumberQuery;

    window.location = url;
  }
  else {
    var count = 0;
    for (i=0; i<selectLabelBox.options.length; i++) {
      if (selectLabelBox.options[i].selected) {
        selectedArray[count] = selectLabelBox.options[i].value;
        count++;
      }
    }


    if(selectedArray.length == 0 || selectedArray.length > $maxColumns) {
      selectLabelBox.focus();
      alert("Please select at least one channel and up to $maxColumns channels to view your graph");
      return;
    }

    xmlHttp=GetXmlHttpObject();

    if (xmlHttp==null)
    {
      alert ("Browser does not support HTTP Request");
      return;
    }

    var url="$graphBaseURL&columns=" + selectedArray + "&doit=outputApplet&title=" + escape(titleBox.value) + "&xlabel=" + escape(xlabelBox.value) + "&ylabel=" + escape(ylabelBox.value) + "&graphtype=" + graphtype + "&w=" + width + "&h=" + height + "&delimiter=" + del + "&separate=" + isSeparated + xDomainQuery + xDomainSelectQuery + "&sid=" + Math.random();

    xmlHttp.onreadystatechange=stateChangedOnSelect;
    xmlHttp.open("GET",url,true);
    xmlHttp.send(null);
  }
}


function stateChangedOnSelect()
{
  var loadingImg = document.getElementById('loading');

  if (xmlHttp.readyState < 4)
  {
    loadingImg.src = '/tree_browser/img/loading.gif';
  }
  else if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
  {
    loadingImg.src = '/images/pixel.gif';

    ret = xmlHttp.responseText;
    document.getElementById('graph').innerHTML = ret;

  }
}


function disableDeltaT(isDisable) {
  var deltaTBox = document.getElementById('deltaT');
  deltaTBox.disabled=isDisable;
  deltaTBox.style.backgroundColor = isDisable ? '#EEEEEE' : '#FFFFFF'
}


function disableXDomainValue(isDisable) {
  document.getElementById('xdomainStart').disabled=isDisable;
  document.getElementById('xdomainEnd').disabled=isDisable;
}

//-->
</script>

<div class="contentpadding">
  <h1 class="portlet" style="margin-bottom:10px;">NEEScentral Data Graphing Tool</h1>
  <div class="info">
  The NEEScentral Data Graphing Tool easily create multiple styles of graphs utilizing data uploaded in NEEScentral. First, select the columns you would like to display, complete the remainder of the form and click "Generate Graph". For additional information about each entry field, place your cursor over the <img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/> image to see a pop up Help Window.
  </div>
  $errormsg
  <table width="100%">
    <tr>
      <td style="text-align:center; white-space:nowrap;">
        $selectForm
      </td>
      <td width="100%">
        <table width="100%">
          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;">Graph Title: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Enter a title for this graph(s). If unspecified, by default, the title of data file metadata or the data file name will be used.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a></td>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;" width="100%"><input type="text" id="title" value="$graphTitle" maxlength="100" style="width:100%;"/></td>
          </tr>
          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;">X-Label: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Enter a label for the X-axis. If unspecified, by default, the name of the header of the first column will be used.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a></td>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;"><input type="text" id="xlabel" value="$xTitle" maxlength="50" style="width:100%;"/></td>
          </tr>
          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;">Y-Label: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Enter a label for the Y-axis. By default, this field is empty.  To graph a single data set, the header of the column will be used.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a></td>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;"><input type="text" id="ylabel" maxlength="50" style="width:100%;"/></td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;">
              X-Domain Values: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Indicate the data format of the X-domain value. To be considered a valid data file, the first column must contain X-domain values.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a> <input type="radio" name="xdomain" id="numerical" $nuremicalSelected onClick="disableDeltaT(true);"/> Numerical Values <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('X-domain values are numerical.')" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a>
              &nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="xdomain" id="timestep" onClick="disableDeltaT(false);" $timestepSelected /> Auto increase, scaled timestep &Delta;t = <input type="text" id="deltaT" maxlength="10" size="6" value="1" disabled="disabled"/> <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('X-domain values are not numerical, e.g., timestamp. User must input the timestamp deltaT.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a>


              <br/>
              Select X-Domain: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Identify the X-domain values limits.')" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a>
              <input type="radio" name="xdomainSelect" id="xdomainAll" checked="checked" onClick="disableXDomainValue(true);"/> All
              &nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;&nbsp;&nbsp;
              <input type="radio" name="xdomainSelect" id="xdomainStartEnd" onClick="disableXDomainValue(false);"/>
              Start value <input type="text" name="xdomainStart" id="xdomainStart" maxlength="10" size="6" disabled="disabled" />
              &nbsp;&nbsp;&nbsp;&nbsp;
              End value <input type="text" name="xdomainEnd" id="xdomainEnd" maxlength="10" size="6" disabled="disabled" />
            </td>
          </tr>
        </table>
        <table width="100%">
          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0;" width="50%; white-space:nowrap;">
              Graph Type: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Select graph style.')" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a>
              <select name='type' id='type'>
                <option value='line'>line graph</option>
                <option value='bar'>bar graph</option>
                <option value='impulses'>impulses graph</option>
                <option value='scatter'>scatter graph</option>
                <option value='dot'>dot graph</option>
                <option value='marker'>marker graph</option>
              </select>
            </td>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0;" width="50%; white-space:nowrap;">
              Separated Graphs: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('If graph more than one dataset, user can separate each dataset to a single graph', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a>
              <input type="radio" name="separate" id="separate1" value="1" />Yes
              <input type="radio" name="separate" id="separate0" value="0" checked="checked" />No
            </td>
          </tr>

          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;" width="50%;">
              Graph Size: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Indicate the width and height of your graph. For best display, the width and height must be between 200-800 pixels.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a> Width <input type="text" id="w" value="$defaultW" size="3" maxlength="3"/> px, Height <input type="text" id="h" value="$defaultH" size="3"/> px
            </td>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;" width="50%">
              Data Delimiter: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Identify the delimiter character between each column. The most common delimiter character are: tab (  ), comma (,), space ( ), colon (:) or pipe (|). The list of columns will be displayed in the \'Select Columns\' drop down list.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a> $selectDelimiter
            </td>
          </tr>

          <tr>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;">
              Inline Comments Indicater: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Identify the inline comment character separating the data file\'s header. <br>This program recognizes \'#\' as the standard inline comment character. In the event the data file uses a different inline comment character and the \'Select Columns\' drop down list is not generated correctly, users should replace the original inline comment character with \'#\' and revalidate the graph.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a> <input type="text" name="comment" id="comment" maxlength="10" size="6" value="" />
            </td>
            <td style="padding-bottom:2px; padding-top:2px; padding-left:0; padding-top:0; white-space:nowrap;">
              Start Line Number: <a class='helplink' tabindex='-1' href='javascript:void(0);' onmouseover="Tip('Ignore the first n-1 lines, and start with this line. You can leave it empty if you want to start from the beginning of the file.', WIDTH, 300)" onmouseout='UnTip()'><img src='/images/help_icon2.gif' width='18' height='18' style='vertical-align:middle;' alt=''/></a> <input type="text" name="line" id="line" maxlength="3" size="2" value="$startLineNumber" />
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td colspan="2" style="text-align:center;">
        $submitButton
      </td>
    </tr>

  </table>

</div>

<div id="graph" style="text-align:center;">
  <div id="blank" style="height:300px;">
  </div>
</div>

$autoGraph

ENDHTML;

print_popup("NEEScentral Data Graph", $content, 0, 800);

}

?>