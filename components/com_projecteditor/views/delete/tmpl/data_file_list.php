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
  $oDataFileArray = array();
  if(isset($_REQUEST[DataFilePeer::TABLE_NAME])){
    $oDataFileArray = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
  }

  $strButtonStyle = "";
?>

<form id="frmPopout" action="/warehouse/projecteditor/removeentity" method="post">
    <input type="hidden" name="format" value="ajax"/>
    <input type="hidden" name="eid" value="<?php echo $this->entityId; ?>"/>
    <input type="hidden" name="etid" value="<?php echo $this->entityTypeId; ?>"/>
    <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>
    <input type="hidden" id="return" name="return" value="<?php echo $this->strReturnUrl; ?>" />

    <div><h2>Confirm Delete</h2></div>
    <div class="information"><b><?php echo $this->className; ?>:</b> List of Directories/Files</div>

    <table style="border:0px">
      <tr>
        <td colspan="2">
          Showing <?php echo $this->iDataFileCount; ?> directories or files.
          <div id="deleteLog">
            <table style="border:0px">
              <thead>
                <th>File Path</th>
              </thead>
              <?php
              /* @var $oDataFile DataFile */
              foreach($oDataFileArray as $iIndex=>$oDataFile){
                $strBgColor = ($iIndex%2==0) ? "odd" : "even";

              ?>
                <tr class="<?php echo $strBgColor; ?>">
                  <td><?php echo $oDataFile->getFriendlyPath(); ?></td>
                </tr>
              <?php
              }
              ?>
            </table>
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2">
          <?php if($this->iDataFileCount > 0){
            $strButtonStyle = "display:none";
          ?>
            <input id="agreementCbx" type="checkbox" name="agree" value="1" onClick="showDeleteButton(this.id, 'deleteBtn');"/>
          <? } ?>
          I agree with deleting the files.  Selecting the checkbox, shows the delete button.
        </td>
      </tr>
    </table>


    <div id="deleteBtn" class="sectheaderbtn" style="<?php echo $strButtonStyle; ?>">
      <a tabindex="" href="javascript:void(0);" class="button2" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);">Delete</a>
    </div>
</form>