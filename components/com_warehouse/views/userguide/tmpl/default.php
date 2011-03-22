<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
//  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/demo.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/Fx.Slide/tree.js", 'text/javascript');
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');

?>

<style type="text/css">
<!--
#bordertable {	
      padding: 0px 0px 0px 0px;
      border: 0px solid #FFFFFF;
}
-->
</style>

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>

  <div id="warehouseWindow" style="padding-top:20px;margin-left:27px">


    <?php echo $this->strTabs; ?>

    <br/>

   <p>Research included in the Project Warehouse must be well documented.  At a minimum projects require information such as experimental drawings, 
   material information, sensor information, data files in ASCII format with headers and units, and a written report.<br><br> The following resources 
   provide an introduction to the Project Warehouse, as well as information regarding data upload, visualization tools, and training sessions.</p>

   <br>
   
   <font size="4.5"><b>User Guide</b></font>
   
<table id="bordertable" cellspacing="1" cellpadding="1">

<tr>
<td>
<ul>
<h3>General Information</h3>
<ul>
<li><a href="/site/resources/pdfs/Data%20Archiving%20and%20Sharing-Sep10.pdf target="blank">NEEScomm Data Sharing and Archiving Procedures</a></li>
<li><a href="/resources/2482/download/UDM_V1-0-4.docx" target="blank">User Data Model</a></li>
<!--<li><a href="/topics/DataDocumentation" target="blank">Data Documentation</a></li>  -->
<li><a href="/topics/NEESProjectDirectoryStructure" target="blank">NEES Project Directory Structure</a></li>
<li><a href="/topics/NEESCuration" target="blank">NEES Curation</a></li>
<li><a href=" /explore/knowledgebase" target="blank">NEEShub Knowledge Base</a></li>
</ul>
</td>

<td>
<h3>Data Upload</h3>
<ul>
<li><a href="/components/com_projecteditor/downloads/ProjectEditorQuickStartGuide.pdf" target="blank">Project Editor User Guide</a></li>
<li><a href="/resources/pen" target="blank">PEN Tool Page</a></li>
<li><a href="/topics/NEESGetttingStartedPEN" target="blank">PEN Quick Start</a></li>
<li><a href="/site/wiki/228/publishing_neeshub.docx" target="blank">Uploading Publications</a></li>
<li><a href="/resources/2447/download/Publishing_NEEShub.docx" target="blank">Publish research project related resources</a></li>
</ul>
</td>

<td>
<h3>Data Visualization with inDEED</h3>
<ul>
<li><a href="topics/inDEED" target="blank">What is inDEED</a></li>
<li><a href="/resources/1731/download/using_indeed.pdf" target="blank">inDEED User Guide</a></li>
<li><a href="/tools/indeed/wiki/CSVFileFormat" target="blank">inDEED data file format</a></li>
<li><a href="/resources/indeed" target="blank">inDEED Tool Page</a></li>
<li><a href="/resources/authortool" target="blank">Authoring Tool Page</a></li>
<li><a href="/tools/indeed/wiki/indeedFileFormat" target="blank">Authoring file format</a></li>
<li><a href="/resources/151" target="blank">inDEED Example Usage</a></li>
</ul>
</td>

<td>
<h3>NEEShub Tutorials</h3>
<ul>
<li><a href="/resources/2581/supportingdocs" target="blank">NEEShub BootCamp 2011 presentations</a></li>
<li><a href="/resources/2613/supportingdocs" target="blank">NEEShub BootCamp 2011 webinar</a></li>
</ul>
</td>


</strong>
</td>
</tr>

</table>
    
</div>
</div>