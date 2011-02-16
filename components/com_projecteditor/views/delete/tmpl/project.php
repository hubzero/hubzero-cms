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
  $strFormTitle = "Confirm Delete";

  $iExperiments = 0;
  $oExperimentArray = array();
  if(isset($_REQUEST[ExperimentPeer::TABLE_NAME])){
    $oExperimentArray = unserialize($_REQUEST[ExperimentPeer::TABLE_NAME]);
    $iExperiments = count($oExperimentArray);
  }

  if($iExperiments==0){
    $oProjectDataFileArray = unserialize($_REQUEST["ProjectFiles"]);
  }

  $bError = false;
  if($this->iProjectFiles > 0 || $iExperiments > 0 || count($this->strDropBoxArray) > 0){
    $bError = true;
  }

  $strFormTitle = "Confirm Delete";
  $strButtonStyle = "";
  if($bError){
    $strButtonStyle = "display:none";
    $strFormTitle = "Unable to Delete Project";
  }
?>

<form id="frmPopout" action="/warehouse/projecteditor/removeentity" method="post">
    <input type="hidden" name="format" value="ajax"/>
    <input type="hidden" name="eid" value="<?php echo $this->entityId; ?>"/>
    <input type="hidden" name="etid" value="<?php echo $this->entityTypeId; ?>"/>
    <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>
    <input type="hidden" id="return" name="return" value="<?php echo $this->strReturnUrl; ?>" />

    <div><h2><?php echo $strFormTitle; ?></h2></div>
    <?php if(count($this->strDropBoxArray) > 0 || count($oExperimentArray) > 0 || $this->iProjectFiles > 0){ ?>
      <div class="error">
        <span style="font-size: 14px; font-weight: bold;">Errors:</span>
        <ul>
          <?php if(count($this->strDropBoxArray) > 0){?>
            <li>Project group can't be deleted until dropbox is empty (<?php echo count($this->strDropBoxArray); ?> files/directories).</li>
          <?}?>

          <?php if(count($oExperimentArray) > 0){
            $strButtonStyle = "display:none";
          ?>
          <!--
          <input id="agreementCbx" type="checkbox" name="agree" value="1" onClick="showDeleteButton(this.id, 'deleteBtn');"/>&nbsp;
          I agree with deleting the experiments.  Selecting the checkbox, shows the delete button.
          -->
            <li> We are unable to delete the project until <b><u>ALL</u></b> experiment files have been removed.</li>
          <?
          }
          ?>

          <?php if($this->iProjectFiles > 0) {?>
            <li> We are unable to delete the project until <b><u>ALL</u></b> project level files have been removed.</li>
            <!--<li> We are unable to delete the project until <b><u>ALL</u></b> project level files (<?php //echo $this->iProjectFiles; ?>) have been removed.</li>-->
          <?
          }
          ?>
        </ul>
      </div>
    <?
    }
    ?>
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
      <tr>
        <td colspan="2">
          <?php if ($iExperiments > 0){ ?>
            <?php echo count($oExperimentArray) ?> experiment(s) with file(s).<br>
            <div id="deleteLog">
              <table style="border:0px">
                <thead>
                  <th>Experiment</th>
                  <th>Files</th>
                </thead>
                <?php
                /* @var $oExperiment Experiment */
                foreach($oExperimentArray as $iIndex=>$oExperiment){
                  $strBgColor = ($iIndex%2==0) ? "odd" : "even";
                ?>
                  <tr class="<?php echo $strBgColor; ?>">
                    <td><?php echo $oExperiment->getName() .": ".$oExperiment->getTitle(); ?></td>
                    <td><?php echo $oExperiment->getDataFileLinkCount(0); ?></td>
                  </tr>
                <?php
                }
                ?>
              </table>
            </div>
          <?php }else{ ?>
            <div id="deleteLog">
              <table style="border:0px">
                <thead>
                  <th>File Path</th>
                </thead>
                <?php
                /* @var $oDataFile DataFile */
                foreach($oProjectDataFileArray as $iIndex=>$oDataFile){
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
          <?php } ?>
        </td>
      </tr>
      <?php if($bError){
      ?>
      <tr>
        <td colspan="2">
          <!--
          <input id="agreementCbx" type="checkbox" name="agree" value="1" onClick="showDeleteButton(this.id, 'deleteBtn');"/>&nbsp;
          I agree with deleting the experiments.  Selecting the checkbox, shows the delete button.
          -->
        </td>
      </tr>
      <?
        }
      ?>
    </table>


    <?php if(!$bError){?>
      <div class="sectheaderbtn">
        <a tabindex="" href="javascript:void(0);" class="button2" style="<?php echo $strButtonStyle; ?>" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);">Delete</a>
      </div>
    <?}?>
</form>