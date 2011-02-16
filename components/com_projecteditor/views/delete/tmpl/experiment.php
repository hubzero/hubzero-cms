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

  $iTrials = 0;
  $oTrialArray = array();
  if(isset($_REQUEST[TrialPeer::TABLE_NAME])){
    $oTrialArray = unserialize($_REQUEST[TrialPeer::TABLE_NAME]);
    $iTrials = count($oTrialArray);
  }

  if($iTrials==0){
    $oExperimentDataFileArray = unserialize($_REQUEST["ExperimentFiles"]);
  }

  $bError = false;
  if($iTrials > 0 || $this->iExperimentFiles){
    $bError = true;
  }

  $strFormTitle = "Confirm Delete";
  $strButtonStyle = "";
  if($bError){
    $strButtonStyle = "display:none";
    $strFormTitle = "Unable to Delete Experiment";
  }


?>

<form id="frmPopout" action="/warehouse/projecteditor/removeentity" method="post">
    <input type="hidden" name="format" value="ajax"/>
    <input type="hidden" name="eid" value="<?php echo $this->entityId; ?>"/>
    <input type="hidden" name="etid" value="<?php echo $this->entityTypeId; ?>"/>
    <input type="hidden" name="path" value="<?php echo $this->path; ?>"/>
    <input type="hidden" id="return" name="return" value="<?php echo $this->strReturnUrl; ?>" />

    <div><h2><?php echo $strFormTitle; ?></h2></div>
    <?php if($bError){ ?>
      <div class="error">
        <span style="font-size: 14px; font-weight: bold;">Errors:</span>
        <ul>
          <?php if($iTrials > 0) {?>
            <li>We are unable to delete the experiment until <b><u>ALL</u></b> trials have been removed.</li>
          <?
          }
          ?>

          <?php if($this->iExperimentFiles > 0) {?>
            <li> We are unable to delete until <b><u>ALL</u></b> experiment level files have been removed.</li>
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
          <?php if($iTrials > 0){ ?>
            <?php echo $iTrials; ?> trial(s) with file(s).<br>
            <div id="deleteLog">
              <table style="border:0px">
                <thead>
                  <th>Trial</th>
                  <th>Files</th>
                </thead>
                <?php
                /* @var $oTrial Trial */
                foreach($oTrialArray as $iIndex=>$oTrial){
                  $strBgColor = ($iIndex%2==0) ? "odd" : "even";
                ?>
                  <tr class="<?php echo $strBgColor; ?>">
                    <td><?php echo $oTrial->getName() .": ".$oTrial->getTitle(); ?></td>
                    <td><?php echo $oTrial->getDataFileLinkCount(0); ?></td>
                  </tr>
                <?php
                }
                ?>
              </table>
            </div>
          <?php }else{ ?>
            Showing <?php echo count($oExperimentDataFileArray); ?> experiment level files.<br>
            <div id="deleteLog">
              <table style="border:0px">
                <thead>
                  <th>File Path</th>
                </thead>
                <?php
                /* @var $oDataFile DataFile */
                foreach($oExperimentDataFileArray as $iIndex=>$oDataFile){
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
      <tr>
        <td colspan="2">
          <?php if($bError){
          ?>
            <!--
            <input id="agreementCbx" type="checkbox" name="agree" value="1" onClick="showDeleteButton(this.id, 'deleteBtn');"/>&nbsp;
            I agree with deleting the repetitions.  Selecting the checkbox, shows the delete button.
            -->
          <?
            }
          ?>
        </td>
      </tr>
    </table>

    <?php if(!$bError){?>
      <div class="sectheaderbtn">
        <a tabindex="" href="javascript:void(0);" class="button2" style="<?php echo $strButtonStyle; ?>" onClick="document.getElementById('frmPopout').submit();window.top.setTimeout('window.parent.document.getElementById(\'sbox-window\').close()', 700);">Delete</a>
      </div>
    <?}?>
</form>