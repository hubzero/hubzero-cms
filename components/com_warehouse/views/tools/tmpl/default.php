<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php $oToolFileArray = unserialize($_REQUEST["TOOL_DATA_FILES"]); ?>

<?php
    foreach($oToolFileArray as $iToolIndex=>$oToolDataFile){
      $strIndeedReturn = $this->warehouseURL;
      $strToolLink = $oToolDataFile->getPath()."/".$oToolDataFile->getName()."&$strIndeedReturn";
      $strToolTitle = $oToolDataFile->getTitle();
      $strToolDesc = $oToolDataFile->getDescription();
      if(strlen($strToolDesc)==0){
        $strToolDesc = "Click to launch tool ".$oToolDataFile->getOpeningTool().".";
      }
?>
      <a href="<?php echo NeesConfig::LAUNCH_INDEED; ?>=<?php echo $strToolLink; ?>" title="<?php echo $strToolDesc; ?>"><?php echo $strToolTitle; ?></a>
<?php
      if($iToolIndex < sizeof($oToolFileArray)-1){
        echo "<br>";
      }
    }
 ?>