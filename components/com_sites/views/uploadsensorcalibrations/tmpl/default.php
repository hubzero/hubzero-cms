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

                <?php if(JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW))
                    echo '<p class="passed">' . JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
                ?>

                <?php if(JRequest::getVar('errorMsg'))
                    echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
                ?>

	   	<input type='hidden' name='submitted' value='1' />
	
		<div style="float:left; clear:both; border: thin solid #eee;">
			<img valign="middle" align="left" src="/images/excel.gif" title="Excel-Book" width="100" height="100" style="padding-right:20px;" alt="" />
			<p>To upload multiple Sensor Calibrations in this facility, download the Excel spreadsheet for the existing sensor calibrations as a starting point.</p>
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
	
		<div style="float:left; clear:both;" class='upload'>
		Upload a sensor calibration list file.
			<form method='post' enctype="multipart/form-data" >
                                <input type="hidden" name="task" value="uploadsensorcalibrations"/>
                                <input type="hidden" name="id" value="<?php echo $this->facilityid; ?>"/>
				<input type='file' id="uploadFile" name="uploadFile" size='60'/>
				<input class="btn" type="submit" name="save" value="Upload"/>
				<input class="btn" type="button" value="Cancel" onclick="history.back();" />
			</form>
		</div>
	</div>

