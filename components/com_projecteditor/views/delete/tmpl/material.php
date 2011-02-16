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
  $strDisplayName = $this->name;
  $strButtonStyle = "";
?>

<form id="frmPopout" action="/warehouse/projecteditor/removeentity" method="post">
    <input type="hidden" name="format" value="ajax"/>
    <input type="hidden" name="eid" value="<?php echo $this->entityId; ?>"/>
    <input type="hidden" name="etid" value="<?php echo $this->entityTypeId; ?>"/>
    <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>
    <input type="hidden" id="return" name="return" value="<?php echo $this->strReturnUrl; ?>" />

    <div><h2>Confirm Delete</h2></div>
    <div class="information"><b><?php echo $this->className; ?>:</b> <?php echo $strDisplayName; ?></div>

    <table style="border:0px">
      <tr>
        <td width="1">Title:</td>
        <td><?php echo $this->title; ?></td>
      </tr>
      <tr>
        <td>Description:</td>
        <td><?php echo $this->description; ?></td>
      </tr>

    </table>

    <?php
      echo $this->strMaterialProperties;
      echo $this->strMaterialFiles;
    ?>

    <div class="sectheaderbtn">
      <a tabindex="" href="javascript:void(0);" class="button2" style="<?php echo $strButtonStyle; ?>" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);">Delete</a>
    </div>
</form>