<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
?>

<?php 
/* @var $oMaterialType MaterialType */
$oMaterialType = unserialize($_REQUEST[MaterialTypePeer::TABLE_NAME]);
$oMaterialTypePropertyArray = $oMaterialType->getMaterialTypeProperties();
if(sizeof($oMaterialTypePropertyArray)==0){
  echo '<span style="color:#999999">The selected material type does not have any properties.</span>';
}else{
?>
  <table id="materialProperties" style="border: 0px; margin-left: 30px;">
  <?php
    /* @var $oMaterialTypeProperty MaterialTypeProperty */
    foreach($oMaterialTypePropertyArray as $iIndex=>$oMaterialTypeProperty){?>
      <tr>
        <td id="materialPropertyTypeName" nowrap><?php echo $oMaterialTypeProperty->getDisplayName() ?></td>
        <td id="materialPropertyTypeInput"><input type="text" id="property<?php echo $oMaterialTypeProperty->getId() ?>" name="property<?php echo $oMaterialTypeProperty->getId() ?>" value=""/></td>
        <td id="materialPropertyTypeUnits">
          <?php
            // get the category, i.e. - distance
            $oMeasurementUnitCategory = $oMaterialTypeProperty->getMeasurementUnitCategory();
            if($oMeasurementUnitCategory){
              // get the default unit for the material type property
              $strMaterialTypePropertyUnits = $oMaterialTypeProperty->getUnits();

              // get the units under the current category, i.e. mm, cm, yd, ft for distance
              $oMeasurementUnitArray = MeasurementUnitPeer::findByCategory($oMeasurementUnitCategory->getId());
            
              echo "<select name='units".$oMaterialTypeProperty->getId()."'>";
              foreach($oMeasurementUnitArray as $oMeasurementUnit){
                $strSelected = ($strMaterialTypePropertyUnits==$oMeasurementUnit->getAbbreviation()) ? "selected" : "";
              
                /* @var $oMeasurementUnit MeasurementUnit */
                echo "<option value='".$oMeasurementUnit->getId()."' $strSelected>".$oMeasurementUnit->getAbbreviation()."</option>";
              }//end foreach
              echo "</select>";
            }//end if($oMeasurementUnitCategory)
          ?>
        </td>
      </tr>

  <?php
    }//close foreach
  }//end if sizeof($oMaterialTypePropertyArray)
?>
</table>