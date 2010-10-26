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

  		<h2 class="portlet">Upload Sensors</h2>


                <?php if(JRequest::getVar('msg'))
                    echo '<p class="passed">' . JRequest::getVar('msg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
                ?>

                <?php if(JRequest::getVar('errorMsg'))
                    echo '<p class="failed">' . JRequest::getVar('errorMsg', '', 'get', 'string', JREQUEST_ALLOWRAW) . '</p>';
                ?>

	   	<input type='hidden' name='submitted' value='1' />
	
		<div style="float:left; clear:both; border: thin solid #eee;">
			<img valign="middle" align="left" src="/images/excel.gif" title="Excel-Book" width="100" height="100" style="padding-right:20px;" alt="" />
			<p>To upload multiple Sensors in this facility, download the Excel spreadsheet for the existing sensors as a starting point.</p>
			<b>Accepted formats:</b> Excel (95, 97, 2000, 2003) (*.xls), and XML Spreadsheet files (*.xml), and comma-delimited text files (*.csv),	and tab-delemited text files (*.txt).</p>
		</div>
	
		<div style="float:left; clear:both; width:700px; padding-top:25px;">
			<b>Note:</b>
			<ul>
				<li>Excel 2007 (*.xlsx) is not yet supported, please save as a lower version before uploading.</li>
				<li>Only <i>the first worksheet</i> of Excel workbooks with multiple worksheet will be read by the system.</li>
				<li>Please leave sensor fields blank if data is not available. Do not substitute with zero.</li>
			</ul>
		</div>
	
		<div style="float:left; clear:both;" class='upload'>
		Upload a sensors list file.
			<form method='post' enctype="multipart/form-data" >
                                <input type="hidden" name="task" value="uploadsensors"/>
                                <input type="hidden" name="id" value="<?php echo $this->facilityid; ?>"/>
				<input type='file' id="uploadFile" name="uploadFile" size='60'/>
				<input class="btn" type="submit" name="save" value="Upload"/>
				<input class="btn" type="button" value="Cancel" onclick="history.back();" />
			</form>
		</div>
	</div>

