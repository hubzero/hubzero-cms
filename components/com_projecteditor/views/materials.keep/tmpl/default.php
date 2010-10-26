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

$oMaterialArray = $_SESSION["materials"];
foreach($oMaterialArray as $iIndex=>$oNewMaterial){
  $oMaterial = unserialize($oNewMaterial);
?>
  <div id="material<?php echo $iIndex; ?>InputPicked" class="editorInputFloat editorInputSize">
    <input type="hidden" name="materialIndexArray[]" value="<?php echo $iIndex; ?>"/>
    <div id="materialProperty<?php echo $iIndex; ?>">
      <table style="border:0px;" id="materialTitle<?php echo $iIndex; ?>">
	    <tr>
	      <td><?php echo $oMaterial->getName(); ?></td>
	      <td align="right">
	        <a title="Click to add material properties" href="javascript:void(0);" 
	           onClick="getMootools('/warehouse/projecteditor?view=mproperties&materialIndex=<?php echo $iIndex; ?>&format=ajax', 'materialPropertyDetails<?php echo $iIndex; ?>');">
	           Add Properties
	        </a>
	      </td>
	    </tr>
	  </table>
	  <div id="materialPropertyDetails<?php echo $iIndex; ?>"></div>
	</div>
  </div>
  <div id="material<?php echo $iIndex; ?>Remove" class="editorInputFloat editorInputButton">
    <a href="javascript:void(0);" title="Remove <?php echo $oMaterial->getName(); ?>." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/removematerial?format=ajax', 'materials', <?php echo $iIndex; ?>, 'materialPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
  </div>
  <div class="clear"></div>
<?php                
}
?>