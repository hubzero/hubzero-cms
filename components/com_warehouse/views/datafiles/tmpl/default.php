<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php $oDataFileArray = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]); ?>

<?php if ($this->referer == "repetition"){ ?>
  <a href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=<?php echo $this->strCurrentPath; ?>&format=ajax','dataList');">more...</a>
<?php }else{ ?>
<form id="frmData" action="/warehouse/download" method="post">
<input type="hidden" name="projectId" value="<?php echo $this->iProjectId; ?>"/>
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>"/>

<a id="hideDataLink" href="javascript:void(0);" onClick="javascript:document.getElementById('showDataLink').style.display='';document.getElementById('projectDocs').style.display='none';document.getElementById('hideDataLink').style.display='none';">Hide</a> <a id="showDataLink" style='display:none' href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=<?php echo $this->strCurrentPath; ?>&format=ajax','dataList');">more...</a>
<div style="border: 1px solid rgb(102, 102, 102); overflow: auto; width: 100%; padding: 0px; margin: 0px;" id="projectDocs">
  <table cellpadding="1" cellspacing="1" style="width:100%;border-bottom:0px;border-top:0px;font-size:11px;">
    <tr valign="top" style="background: #CCCCCC;">
      <td colspan="6" style="font-size:11px;"><b>Current Path:</b>&nbsp;&nbsp;<?php echo $this->strCurrentPath; ?></td>
    </tr>
    <tr style="background: #EFEFEF;">
      <th style="font-weight:bold;"></th>
      <th style="font-weight:bold;"></th>
      <th style="font-weight:bold;">Name</th>
      <th style="font-weight:bold;">Timestamp</th>
      <th style="font-weight:bold;">Application</th>
      <th style="font-weight:bold;"></th>
    </tr>
    <tr valign="top">
      <?php if(!StringHelper::endsWith($this->strCurrentPath, ".groups")): ?>
        <td colspan="6"><a onclick="getMootools('/warehouse/data?path=<?php echo $this->strBackPath; ?>&format=ajax','dataList');" href="javascript:void(0);">...go back</a></td>
      <?php else: ?>
        <td colspan="6"></td>
      <?php endif; ?>
    </tr>
    <?php if ( !empty($oDataFileArray) ): ?>
    
      <?php
            /* @var $oDataFile DataFile */
            foreach($oDataFileArray as $iIndex => $oDataFile){
    	      $strRowBackgroundColor = "";
    	      if($iIndex %2 === 0){
    	        $strRowBackgroundColor = "#EFEFEF;";
    	      }

              $strFileLink = $oDataFile->get_url();
              $strDirLink = $this->strCurrentPath."/".$oDataFile->getName();
      ?>
        <tr style="background: <?php echo $strRowBackgroundColor; ?>">
          <td><input type="checkbox" id="cbxDataFile<?php echo $iIndex;?>" name="cbxDataFile[]" value="<?php echo $oDataFile->getId(); ?>"/></td>
          <td>
            <?php if( $oDataFile->getDirectory()==1 ): ?>
              <input type="image" src="/components/com_warehouse/images/icons/folder.gif" onClick=""/>
            <?php endif; ?>
          </td>
          <td>
            <?php if($oDataFile->getDirectory()==1): ?>
              <a href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=<?php echo $strDirLink; ?>&format=ajax','dataList');"><?php echo $oDataFile->getName(); ?></a>
            <?php else: ?>
              <a href="<?php echo $strFileLink; ?>" target="data_file"><?php echo $oDataFile->getName(); ?></a>
            <?php endif; ?>
          </td>
          <td><?php echo $oDataFile->getCreated(); ?></td>
          <td>
            <?php 
              $strToolLink = "";
              
              $strOpeningTool = $oDataFile->getOpeningTool();
              if(strlen($strOpeningTool)!=0){
              	switch($strOpeningTool){
              	  case 'inDEED':
                        $strLaunchInEED = NeesConfig::LAUNCH_INDEED;
              		$strToolLink = "<a href='$strLaunchInEED=".$strLink."'>".$strOpeningTool."</a>";
              	  break;
                }
              }
              
              echo $strToolLink;
            ?>
          </td>
          <td></td>
        </tr>
      <?php }?>
      <tr>
        <td colspan="6"><input type="button" onClick="validateFileBrowser('frmData', 'cbxDataFile', <?php echo sizeof($oDataFileArray); ?>);" value="Download" /></td>
      </tr>
    <?php else: ?>
      <tr style="background: #EFEFEF;">
        <td colspan="6" align="center">0 files found.</td>
      </tr>
    <?php endif; ?>  
  </table>
</div>
</form>
<?php } ?>