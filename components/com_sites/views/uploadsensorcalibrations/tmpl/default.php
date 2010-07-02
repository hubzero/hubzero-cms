<?php
 
// No direct access
 
defined('_JEXEC') or die('Restricted access'); 

require_once "lib/data/Calibration.php";
require_once "lib/data/Sensor.php";
require_once "Spreadsheet/Excel/Writer.php";

?>

	<h2><?php echo $this->facility->getName(); ?></h2>

	<?php echo $this->tabs;?>

	<div id="facility-subpage-primarycontent">

		<h2 class="portlet">Upload Sensor Calibrations</h2>
	  
	   	<input type='hidden' name='submitted' value='1' />
	
		<div style="float:left; clear:both; border: thin solid #eee;">
			<img valign="middle" align="left" src="/images/excel.gif" title="Excel-Book" width="100" height="100" style="padding-right:20px;" alt="" />
			<p>To upload multiple Sensor Calibrations in this facility, download the Excel spreadsheet below and enter the information for each calibration in the appropriate spreadsheet fields.</p>
			<b>Accepted formats:</b> Excel (95, 97, 2000, 2003) (*.xls), and XML Spreadsheet files (*.xml), and comma-delimited text files (*.csv),	and tab-delemited text files (*.txt).</p>
		</div>
	
		<div style="float:left; clear:both; width:700px; padding-top:25px;">
			<b>Note:</b>
			<ul>
				<li>Excel 2007 (*.xlsx) is not yet supported, please save as a lower version before uploading.</li>
				<li>Only <i>the first worksheet</i> of Excel workbooks with multiple worksheet will be read by the system.</li>
				<li>Please leave calibration fields blank if data is not available. Do not substitute with zero.</li>
				<li>When updating calibrations, each existing calibration appears with a unique identifier in the first column of the calibration list. Changing this identifier will cause the calibration upload to fail. To add <em>new</em> calibrations, insert a row in the spreadsheet with the requisite information but <em>no calibration id</em> in the first column.</li>
			</ul>
		</div>
	
		<!--<?= $alert ?>-->
		<!--<?= $done ?>-->
	
	
		<div style="float:left; clear:both;" class='upload'>
		Upload a calibration list file.
			<form id='frm_UploadCalibrations' method='post' enctype="multipart/form-data" >
				<input type='file' id="uploadFile" name="uploadFile" class="textentry" size='60'/>&nbsp;&nbsp;
				<input class="btn" type="submit" name="save" value="Upload"/>&nbsp;&nbsp;
				<input class="btn" type="button" value="Cancel" onclick="history.back();" />
				<input type="hidden" name="task" value="douploadsensorcalibration"></input>
			</form>
		</div>
	</div>

