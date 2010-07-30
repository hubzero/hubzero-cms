<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php $oToolFileArray = unserialize($_REQUEST["TOOL_DATA_FILES"]); ?>

<?php
  foreach($oToolFileArray as $iToolIndex=>$oToolDataFile){
    $strToolLink = $oToolDataFile->getPath()."/".$oToolDataFile->getName();
    $strToolTitle = $oToolDataFile->getTitle();
    $strToolDesc = $oToolDataFile->getDescription();
    if(strlen($strToolDesc)==0){
      $strToolDesc = "Click to launch tool ".$oToolDataFile->getOpeningTool().".";
    }
    
    $strLink = InDEED::LAUNCH ."?list=".$strToolLink;
?>
    <a href="<?php echo $strLink; ?>" title="<?php echo $strToolDesc; ?>"><?php echo $strToolTitle; ?></a>
<?php
    if($iToolIndex < sizeof($oToolFileArray)-1){
      echo "<br>";
    }
  }
 ?>