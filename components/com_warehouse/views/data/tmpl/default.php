<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php if ($this->referer == "repetition"){ ?>
  <a href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=<?php echo $this->strCurrentPath; ?>&format=ajax&form=<?php echo $this->strFormId; ?>&target=<?php echo $this->strTarget; ?>','<?php echo $this->strTarget; ?>');">more...</a>
<?php }else{ ?>
<form id="<?php echo $this->strFormId; ?>" action="/warehouse/download" method="post">
<input type="hidden" name="projectId" value="<?php echo $this->iProjectId; ?>"/>
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>"/>

<?php
  $strLinkText = "more...";
  $strIndeedReturn = $this->warehouseURL;
  if($this->strTarget != "dataList"){
    $strLinkText = "view";
    $strIndeedReturn = "";
  }
?>

<a id="hideDataLink<?php echo $this->strTarget; ?>" href="javascript:void(0);" onClick="javascript:document.getElementById('showDataLink<?php echo $this->strTarget; ?>').style.display='';document.getElementById('projectDocs<?php echo $this->strTarget; ?>').style.display='none';document.getElementById('hideDataLink<?php echo $this->strTarget; ?>').style.display='none';">Hide</a> <a id="showDataLink<?php echo $this->strTarget; ?>" style='display:none' href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=<?php echo $this->strCurrentPath; ?>&format=ajax&form=<?php echo $this->strFormId; ?>&target=<?php echo $this->strTarget; ?>','<?php echo $this->strTarget; ?>');"><?php echo $strLinkText; ?></a>
<?php echo $this->mod_warehousefiles; ?>
</form>
<?php } ?>