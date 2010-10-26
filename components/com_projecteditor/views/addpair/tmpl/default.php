<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

header( 'Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header( 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s' ) . ' GMT' );
header( 'Cache-Control: no-store, no-cache, must-revalidate' );
header( 'Cache-Control: post-check=0, pre-check=0', false );
header( 'Pragma: no-cache' ); 

?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
?>

<?php 
$oTupleArray = $_SESSION[$this->strName];
foreach($oTupleArray as $iIndex=>$oTuple){
  //$oTuple = unserialize($oTuple);
  $oTuple = $oTuple;
  ?>
  
  <div id="<?php echo $oTuple->getField1()."-".$iIndex; ?>Input" class="editorInputFloat editorInputSize">
    <!--<input type="hidden" name="<?php echo $oTuple->getField1(); ?>Array[]" value="<?php echo $oTuple->getName().":".$oTuple->getValue(); ?>"/>-->
    <input type="hidden" name="<?php echo $oTuple->getField1(); ?>[]" value="<?php echo $oTuple->getName(); ?>"/>
    <input type="hidden" name="<?php echo $oTuple->getField2(); ?>[]" value="<?php echo $oTuple->getValue(); ?>"/>
    <?php echo $oTuple->getName()." : ".$oTuple->getValue(); ?>
  </div>
  <div id="<?php echo $oTuple->getField1()."-".$iIndex; ?>Remove" class="editorInputFloat editorInputButton">
    <a href="javascript:void(0);" title="Remove <?php echo $oTuple->getName()." : ".$oTuple->getValue(); ?>." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', '<?php echo $oTuple->getField1(); ?>', <?php echo $iIndex; ?>, '<?php echo $oTuple->getField1(); ?>Picked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
  </div>
  <div class="clear"></div>
  
  <?php 
}
?>