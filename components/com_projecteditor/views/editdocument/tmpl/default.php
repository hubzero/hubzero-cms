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

  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php
 $strName = "";
 $strDescription = "";
 $strTitle = "";
 $iEntityTypeId = 0;

 /* @var $oDataFile DataFile */
 $oDataFile = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
 if($oDataFile){
   $strName = $oDataFile->getName();
   $strDescription = $oDataFile->getDescription();
   $strTitle = $oDataFile->getTitle();
   $iEntityTypeId = $oDataFile->getUsageTypeId();
 }

 /* @var $oProjectHomepage ProjectHomepage */
 $oProjectHomepage = null;
 if(isset($_REQUEST[ProjectHomepagePeer::TABLE_NAME])){
   $oProjectHomepage = unserialize($_REQUEST[ProjectHomepagePeer::TABLE_NAME]);
 }
 

?>

<form id="frmPopup" action="/warehouse/projecteditor/savedocument" method="post" enctype="multipart/form-data">
  <input type="hidden" name="projectId" value="<?php echo $this->projectId; ?>"/>
  <input type="hidden" name="experimentId" value="<?php echo $this->experimentId; ?>"/>
  <input type="hidden" name="dataFileId" value="<?php echo $this->dataFileId; ?>"/>
  <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>
  <input type="hidden" name="requestType" value="<?php echo $this->requestType; ?>"/>

  <div><h2>Edit Data File</h2></div>
  <div class="information"><b>Destination:</b> <?php echo $this->path; ?></div>
  
  <table style="margin-left: 20px; margin-top: 20px; border: 0px; width: 90%">
    <?php if( $this->dataFileId ): ?>
      <tr id="filename">
        <td nowrap="" width="1">
          <label for="name" class="editorLabel">Name:</label>
        </td>
        <td>
          <input class="editorInputSize" id="name" type="text" name="name" disabled value="<?php echo $strName; ?>"/>
        </td>
      </tr>
    <?php endif; ?>
    <tr id="filetitle">
      <td nowrap="" width="1">
        <label for="title" class="editorLabel">Title:</label>
      </td>
      <td>
          <input class="editorInputSize" id="title" type="text" name="title" value="<?php echo $strTitle; ?>" />
      </td>
    </tr>
    <tr id="filedesc">
      <td nowrap="">
        <label for="desc" class="editorLabel">Description:</label>
      </td>
      <td>
        <textarea id="desc" name="desc" class="editorInputSize" style="height: 100px;"><?php echo $strDescription; ?></textarea>
      </td>
    </tr>

    <!--
    Only show citation fields if on project level.
    -->
    <?php if(!$this->experimentId){  ?>
      <tr id="authorCheck">
        <td><label for="author" class="editorLabel">Citation:</label></td>
        <td>
          Are any of the authors a member of NEEShub?
          <input id="membershipCheck" type="radio" name="author" value="0" onClick="document.getElementById('citation').style.display=''"/> No &nbsp;&nbsp;
          <input type="radio" name="author" value="1"  onClick="document.getElementById('citation').style.display='none';window.open('https://<?php echo $_SERVER['SERVER_NAME']?>/contribute/?step=1&type=3','neesContribute');"/> Yes
        </td>
      </tr>
      <tr id="pubInfo">
        <td></td>
        <td>
          <div id="citation" style="display: none;">
            <textarea id="citationTextArea" name="citation" class="editorInputSize" style="height: 100px;"><?php if($oProjectHomepage) {echo $oProjectHomepage->getDescription();}?></textarea>
            <br>
            <span style="font-size: 9px; color: #8D8D8D">Cheng Chen; James Ricles, "Servo-Hydraulic Actuator Control for Real-Time Hybrid Simulation"</span>
          </div>
        </td>
      </tr>
    <? } ?>
    <!--
    <tr id="save">
      <td colspan="2">
        <input type="submit" value="Save File"/>
      </td>
    </tr>
    -->
  </table>
  <div id="save" class="sectheaderbtn">
      <a href="javascript:void(0);" class="button2"  onClick="document.getElementById('frmPopup').submit()">Save File</a>
  </div>
</form>

