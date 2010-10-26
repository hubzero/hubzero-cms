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
 $strCurrentTool = "";
 $bIsDirectory = false;

 /* @var $oDataFile DataFile */
 $oDataFile = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
 if($oDataFile){
   $strName = $oDataFile->getName();
   $strDescription = $oDataFile->getDescription();
   $strTitle = $oDataFile->getTitle();
   $iEntityTypeId = $oDataFile->getUsageTypeId();
   $bIsDirectory = $oDataFile->isDirectory();
   $strCurrentTool = (StringHelper::hasText($oDataFile->getOpeningTool())) ? $oDataFile->getOpeningTool() : "";
 }
 

?>

<form id="frmProject" action="/warehouse/projecteditor/savedatafile" method="post" enctype="multipart/form-data">
  <input type="hidden" name="projectId" value="<?php echo $this->projectId; ?>"/>
  <input type="hidden" name="experimentId" value="<?php echo $this->experimentId; ?>"/>
  <input type="hidden" name="dataFileId" value="<?php echo $this->dataFileId; ?>"/>
  <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>

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

    <?php if(!$bIsDirectory): ?>
    <tr id="filetype">
      <td nowrap="" width="1">
        <label for="tool" class="editorLabel">Opening Tool:</label>
      </td>
      <td>
        <?php
          $strToolArray = $this->strToolArray;
        ?>
        <select name="tool" id="tool">
          <option value="">Not Applicable</option>
          <?php
            foreach($strToolArray as $strTool){
              ?>
              <option value="<?php echo $strTool; ?>" <?php if($strCurrentTool==$strTool)echo "selected"; ?>><?php echo $strTool; ?></option>
              <?php
            }
          ?>
        </select>
      </td>
    </tr>
    <?php endif; ?>

    <tr id="save">
      <td colspan="2">
        <input type="submit" value="Save File"/>
      </td>
    </tr>
  </table>
</form>

