<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/templates/fresh/html/com_groups/groups.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');
?>

<form action="/warehouse/projecteditor/savesensorfile" method="post" enctype="multipart/form-data">
  <input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>"/>
  <input type="hidden" name="locationPlanId" value="<?php echo $this->iLocationPlanId; ?>"/>

<div style="margin:10px 20px 10px 15px;">
  <div>
    <p>
    <img valign="middle" align="left" src="/components/com_projecteditor/images/icons/excel.gif" title="Excel-Book" width="100" height="100" style="padding-right:20px;" alt="" /> To upload multiple Locations to Location Plan '<?= $this->lpName ?>', download the Excel spreadsheet below and enter the information for each location in the appropriate spreadsheet fields.
    <br/><br/><b>Accepted formats:</b> Excel (95, 97, 2000, 2003) (*.xls), and XML Spreadsheet files (*.xml), and comma-delimited text files (*.csv), and tab-delemited text files (*.txt).
    <br /><br />
    </p>
  </div>
  <div style="clear:both;"></div>
  
  <div>
    <p>
    <b>Note that:</b>
    <ul>
      <li>Excel 2007 (*.xlsx) is not supported, please save as a lower version before uploading.</li>
      <li>Only <i>the first worksheet</i> of Excel workbooks with multiple worksheet will be read by the system.</li>
    </ul>
    </p>
  </div>
  Download <a href="/components/com_projecteditor/downloads/<?= $this->planType ?>Location.xls" class="bluelt"><?= $this->planType ?>Location.xls</a>

  <br/><br/>
  <?= $this->alert ?>
  <br/><br/>

  <div class='upload'>
    Upload a <?= $this->planType ?> Locations file.
    <br/><br/>
    <input type='file' id="uploadFile" name="uploadFile" class="textentry" size='60'/>&nbsp;&nbsp;<input type="submit" name="save" value="Upload"/>
  </div>
  <br/><br/>

</div>
</form>




