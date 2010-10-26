<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
?>

<form action="/warehouse/projecteditor/makedirectory" method="post">
<input type="hidden" name="projectId" value="<?php echo $this->iProjectId; ?>" id="project"/>
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" id="experiment"/>
<input type="hidden" name="path" value="<?php echo $this->strPath; ?>" id="path"/>

<div class="information"><b>Destination:</b> <?php echo $this->strPathFriendly; ?></div>

<table style="border:0px;">
  <tr>
    <td width="1" nowrap="" class="editorLabel">
      Directory Name:
    </td>
    <td><input type="text" name="newdir" class="editorInputSize"/></td>
  </tr>
  <tr>
    <td colspan="2">
      <input type="submit" value="Create Directory"/>
    </td>
  </tr>
</table>
</form>

