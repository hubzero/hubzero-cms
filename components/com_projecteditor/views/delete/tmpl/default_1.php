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
  $oDataFileArray = array();
  if(isset($_REQUEST[DataFilePeer::TABLE_NAME])){
    $oDataFileArray = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
    $strDisplayName = "List of Files";
  }


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
      <?php if(empty($oDataFileArray)){?>
        <tr>
          <td width="1">Title:</td>
          <td><?php echo $this->title; ?></td>
        </tr>
        <tr>
          <td>Description:</td>
          <td><?php echo $this->description; ?></td>
        </tr>
      <?php
        }else{
          /* @var $oDataFile DataFile */
          foreach($oDataFileArray as $iIndex=>$oDataFile){
            $strBgColor = ($iIndex%2==0) ? "even" : "odd";

          ?>
            <tr class="<?php echo $strBgColor; ?>">
              <td>
                <table style="border:0px;">
                  <tr>
                    <td width="1">Name:</td>
                    <td><?php echo $oDataFile->getName(); ?></td>
                  </tr>
                  <?php if($oDataFile->getTitle()):?>
                    <tr>
                      <td width="1">Title:</td>
                      <td><?php echo $oDataFile->getTitle(); ?></td>
                    </tr>
                  <?php endif; ?>
                  <?php if($oDataFile->getDescription()):?>  
                    <tr>
                      <td>Description:</td>
                      <td><?php echo $oDataFile->getDescription(); ?></td>
                    </tr>
                  <?php endif; ?>
                </table>
              </td>
            </tr>
          <?php
          }
        }
      ?>
    </table>
    <p align="center" class="topSpace20">
      <input type="button" value="Delete" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);"/>
    </p>
</form>