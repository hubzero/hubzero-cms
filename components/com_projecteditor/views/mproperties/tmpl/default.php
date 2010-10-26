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

  $this->materialIndex;
  
?>

<div id="propertyList<?php echo $this->materialIndex; ?>" style="height: 100px; width:100%; overflow: auto; border: 1px solid #999999; background: none repeat scroll 0% 0%; color: rgb(0, 0, 0); margin-bottom: 1.5em;">
  <?php 
    $oMeasurementUnitArray = unserialize($_REQUEST['MEASUREMENT_UNITS']);
    
    $iIndex = 0;
    while($iIndex < 10){
      $strPropertyTypeName = "materialPropertyTypeName".$this->materialIndex."[]";	
      $strPropertyTypeValue = "materialPropertyTypeValue".$this->materialIndex."[]";
      $strPropertyTypeUnit = "materialPropertyTypeUnit".$this->materialIndex."[]";	
      $strSearchDivId = "materialPropertyTypeSearch".$this->materialIndex."_".$iIndex;
      
      $iCount = $iIndex+1;
    ?>
      
      <div style="float:left;margin-left:10px;margin-right:10px;">
        <?php echo $iCount; ?>) 
      </div>
      
      <div style="float:left;">
      <table style="border:0px;">
        <tr>
          <td>Name:</td>
          <td>
            <input id="txtMaterialPropertyType" type="text" name="<?php echo $strPropertyTypeName; ?>" value="" 
               style="width:200px;" onFocus="this.value='';"
               onkeyup="suggest('/projecteditor/materialpropertytypesearch?format=ajax', '<?php echo $strSearchDivId; ?>', this.value, this.id)"/>
        	<div id="<?php echo $strSearchDivId; ?>" class="suggestResults"></div>
          </td>
        </tr>
        <tr>
          <td>Value:</td>
          <td>
            <input name="<?php echo $strPropertyTypeValue; ?>" value = "" style="width:200px;"/>
          </td>
        </tr>
        <tr>
          <td>Units:</td>
          <td>
            <select name="<?php echo $strPropertyTypeUnit; ?>">
	          <option value="0">-Select Measurement Unit-</option>
	          <?php 
	            foreach($oMeasurementUnitArray as $oMeasurementUnit){?>
	              <option value="<?php echo $oMeasurementUnit->getId(); ?>"><?php echo $oMeasurementUnit->getAbbreviation(); ?></option>
	            <?php 
	            }
	          ?>
	        </select>
          </td>
        </tr>
      </table>
      </div>
      <div class="clear"></div>
      
      
    <?php 
      ++$iIndex;
    }
  ?>
</div>