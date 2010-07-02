<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php $oDataFileArray = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]); ?>

<div style="border: 1px solid rgb(102, 102, 102); overflow: auto; width: 100%; padding: 0px; margin: 0px;" id="projectDocs">
  <table cellpadding="1" cellspacing="1" style="width:100%;border-bottom:0px;border-top:0px;font-size:11px;">
    <tr valign="top" style="background: #CCCCCC;">
      <td colspan="6" style="font-size:11px;"><b>Current Path:</b>&nbsp;&nbsp;<?php echo $strPath; ?></td>
    </tr>
    <tr style="background: #EFEFEF;">
      <th style="font-weight:bold;"></th>
      <th style="font-weight:bold;"></th>
      <th style="font-weight:bold;">Name</th>
      <th style="font-weight:bold;">Timestamp</th>
      <th style="font-weight:bold;">Program</th>
      <th style="font-weight:bold;"></th>
    </tr>
    <tr valign="top">
      <td colspan="6">
        <?php if(strlen($strBackPath)>0): ?>
          <a onclick="getMootools('/warehouse/data?path=<?php echo $strBackPath; ?>&format=ajax','dataList');" href="javascript:void(0);">...go back</a>
        <?php endif; ?>
      </td>
    </tr>
    <?php if ( !empty($oDataFileArray) ): ?>
    
      <?php foreach($oDataFileArray as $iIndex => $oDataFile){ 
    		$strRowBackgroundColor = "";
    		if($iIndex %2 === 0){
    		  $strRowBackgroundColor = "#EFEFEF;";	
    		}
    		
    		$strLink = $strPath."/".$oDataFile->getName();
      ?>
        <tr style="background: <?php echo $strRowBackgroundColor; ?>">
          <td><input type="checkbox" id="cbxDataFile" name="cbxDataFile" value="<?php echo $oDataFile->getId(); ?>"/></td>
          <td>
            <?php if( $oDataFile->getDirectory()==1 ): ?>
            <input type="image" src="/components/com_warehouse/images/icons/folder.gif" onClick="#">
            <!-- <a href="#"><img src="/components/com_warehouse/images/icons/folder.gif"/></a> -->
            <?php endif; ?>
          </td>
          <td><a href="javascript:void(0);" onClick="getMootools('/warehouse/data?path=<?php echo $strLink; ?>&format=ajax','dataList');"><?php echo $oDataFile->getName(); ?></a></td>
          <td><?php echo $oDataFile->getCreated(); ?></td>
          <td>[Default]</td>
          <td></td>
        </tr>
      <?php }?>
      <tr>
        <td colspan="6">[Buttons]</td>
      </tr>
    <?php else: ?>
      <tr style="background: #EFEFEF;">
        <td colspan="6" align="center">0 files found.</td>
      </tr>
    <?php endif; ?>  
  </table>
</div>