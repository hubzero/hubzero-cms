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
$strInputArray = $_SESSION[$this->strName];
foreach($strInputArray as $iIndex=>$strInput){?>
  <div id="<?php echo $this->strName."-".$iIndex; ?>Input" class="editorInputFloat editorInputSize">
    <!--<input type="hidden" name="<?php //echo $this->strName; ?>Array[]" value="<?php //echo $strInput; ?>"/>-->
    <input type="hidden" name="<?php echo $this->strName; ?>[]" value="<?php echo $strInput; ?>"/>
    <?php echo $strInput; ?>
  </div>
  <div id="<?php echo $this->strName."-".$iIndex; ?>Remove" class="editorInputFloat editorInputButton">
    <a href="javascript:void(0);" title="Remove <?php echo $strInput; ?>." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', '<?php echo $this->strName; ?>', <?php echo $iIndex; ?>, '<?php echo $this->strName; ?>Picked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
  </div>
  <div class="clear"></div>
<?php                
}
?>