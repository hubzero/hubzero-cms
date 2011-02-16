<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
$oThisFilterArray = $this->strSelectedFilterArray;
$strAction = $this->strSelectedAction;
$strTarget = $this->strSelectedTarget;
$strType = $this->strSelectedType;
$strField = $this->strSelectedField;
?>

<?php
  if($strAction=="show"){
    foreach($oThisFilterArray as $iIndex=>$strFilterArray){
      $strValue = urlencode($strFilterArray["MONIKER"]);
      if(isset($strFilterArray["ID"])){
        $strValue = $strFilterArray["ID"];
      }

?>
      <a href="/warehouse/filter?<?php echo $strField; ?>=<?php echo $strValue; ?>"><?php echo $strFilterArray["MONIKER"] ." (".$strFilterArray["TOTAL"].")"; ?></a>
<?
      if($iIndex < count($oThisFilterArray)){
        echo "<br>";
      }
    }
  ?>
  <a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=<?php echo $strType; ?>&action=hide&format=ajax&target=<?php echo $strTarget; ?>&field=<?php echo $strField; ?>', '<?php echo $strTarget; ?>');">less...</a>
  <?php
?>

<?}else{
    foreach($oThisFilterArray as $iIndex=>$strFilterArray){
      $strValue = urlencode($strFilterArray["MONIKER"]);
      if(isset($strFilterArray["ID"])){
        $strValue = $strFilterArray["ID"];
      }    
    ?>
      <a href="/warehouse/filter?<?php echo $strField; ?>=<?php echo $strValue; ?>"><?php echo $strFilterArray["MONIKER"] ." (".$strFilterArray["TOTAL"].")"; ?></a>
    <?
      if($iIndex > 2){
        if(count($oThisFilterArray) > 4){
        ?>
          <br><a href="javascript:void(0);" onClick="getMootools('/warehouse/searchfilter?type=<?php echo $strType; ?>&action=show&format=ajax&target=<?php echo $strTarget; ?>&field=<?php echo $strField; ?>', '<?php echo $strTarget; ?>');">more...</a>
        <?php
        }
        break;
      }else{
        echo "<br>";
      }
    }
    ?>
<?}?>