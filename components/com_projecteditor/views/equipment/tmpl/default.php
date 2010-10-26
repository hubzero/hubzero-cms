<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

header( 'Expires: 0' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' ); 

?>

<p style="height: 100px; width:70%; overflow: auto; border: 1px solid #999999; background: none repeat scroll 0% 0%; color: rgb(0, 0, 0); margin-bottom: 1.5em;">

<?php
$iSelectedEquipmentIdArray = $this->iSelectedEquipmentArray;
$oEquipmentArray = unserialize($_SESSION["SUGGESTED_FACILITY_EQUIPMENT"]);
foreach($oEquipmentArray as $oEquipment){
  $strChecked = (in_array($oEquipment->getId(), $iSelectedEquipmentIdArray)) ? "checked" : "";
  $iParentId = $oEquipment->getParentId();
  if($iParentId > 0){
    echo "<label><input type='checkbox' name='equipment[]' $strChecked value='".$oEquipment->getId()."' onClick=\"appendEquipment('equipmentlist', ".$oEquipment->getId().");\"> ".$oEquipment->getNote()." :: ".$oEquipment->getName()."</label><br>";
    //echo "<option value='".$oEquipment->getId()."'>".$oEquipment->getNote()."::".$oEquipment->getName()."</option>";
  }else{
    echo "<label><input type='checkbox' name='equipment[]' value='".$oEquipment->getId()."' onClick=\"appendEquipment('equipmentlist', this.value);\"> ".$oEquipment->getNote()." :: ".$oEquipment->getName()."</label><br>";
    //echo "<option value='".$oEquipment->getId()."'>".$oEquipment->getNote()."::".$oEquipment->getName()."</option>";
  }
}
?>

</p>