<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
?>

<?php
  $oProject = unserialize($_REQUEST[ProjectPeer::TABLE_NAME]);
  $oAuthorizer = Authorizer::getInstance();
?>

<form id="frmProject" method="get">
<input type="hidden" id="txtExperiment" name="experiment" value="0"/>
<input type="hidden" id="txtTrial" name="trial" value="0" />
<input type="hidden" id="txtRepetition" name="repetition" value="0" />

  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>

  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="treeBrowser" style="float:left;width:20%;"></div>

    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <div id="title" style="padding-bottom:1em;">
        <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
      </div>

      <?php echo TabHtml::getSearchFormWithAction( "frmProject", "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>

      <div class="withleft">
        <div class="aside">
          <ul>
            <li><a href="/warehouse/projecteditor/project/<?php echo $oProject->getId(); ?>/more" class="sent"><span>Images</span></a></li>
            <li class="active"><a href="/warehouse/projecteditor/project/<?php echo $oProject->getId(); ?>/moreanalysis" class="new-message"><span>Analysis</span></a></li>
            <li><a href="/warehouse/projecteditor/project/<?php echo $oProject->getId(); ?>/moredocs" class="new-message"><span>Documents</span></a></li>
          </ul>
        </div><!-- / .aside -->
        <div class="subject">
          <?php if($this->bCanViewProject){ ?>
          <div id="documents" style="padding-top:1em;">
            <table summary="Filter options." style="border:0px;">
              <tr>
                <td>
                  <div style="float:left;margin-right: 10px;"><?php echo $this->strExperimentDropDown; ?></div>
                  <div style="float:left;margin-right: 10px;"><?php echo $this->strTrialDropDown; ?></div>
                  <div style="float:left;margin-right: 10px;"><?php echo $this->strRepetitionDropDown; ?></div>
                  <div class="clear"></div>
                </td>
              </tr>
            </table>

            <?php
              $oDocumentDataFileArray = unserialize($_REQUEST[DataFilePeer::TABLE_NAME]);
            ?>
            <table summary='A list of tool files.' id='fileList'>
              <thead>
                <tr>
                  <th width='55%'>Document</th>
                  <th width='15%'>Experiment</th>
                  <th width='15%'>Trial</th>
                  <th width='15%'>Repetition</th>
                </tr>
              </thead>
              <?php
                /* @var $oDataFile DataFile */
                foreach($oDocumentDataFileArray as $iFileIndex=>$oDataFile){
                  $strRow = "odd";
                  if($iFileIndex%2==0){
                    $strRow = "even";
                  }

                  $iDataFileId = $oDataFile->getId();
                  $strTitle = $oDataFile->getTitle();
                  $strName = $oDataFile->getName();
                  $strDescription = $oDataFile->getDescription();
                  $strFriendlyPath = $oDataFile->getFriendlyPath();

                  $strDisplay = (strlen($strTitle)==0) ? $strName : $strTitle;
                  $strFileTooltip = (strlen($strDescription) > 0) ? $strDescription ." ::: ". $strFriendlyPath : $strFriendlyPath;
                  $strLink = $oDataFile->get_url();

                  $oDataFileLink = DataFileLinkPeer::retrieveByPK($iDataFileId);
                  $oExperiment = $oDataFileLink->getExperiment();
                  $oTrial = $oDataFileLink->getTrial();
                  $oRepetition = $oDataFileLink->getRepetition();
                  ?>

                  <tr class="<?php echo $strRow; ?>">
                    <td><a href="<?php echo $strLink; ?>" title="<?php echo $strFileTooltip; ?>"><?php echo $strDisplay; ?></a></td>
                    <td>
                      <?php if($oExperiment): ?>
                        <a href="/warehouse/experiment/<?php echo $oExperiment->getId(); ?>/project/<?php echo $oProject->getId(); ?>" class="Tips3" title="<?php echo $oExperiment->getName(); ?> :: <?php echo $oExperiment->getTitle(); ?>"><?php echo $oExperiment->getName(); ?></a>
                      <?php endif; ?>
                    </td>
                    <td>
                      <?php if($oTrial){
                         echo $oTrial->getName();
                      }?>
                    </td>
                    <td>
                      <?php if($oRepetition){
                         echo $oRepetition->getName();
                      }?>
                    </td>
                  </tr>
                  <?php
                }
              ?>
            </table>

          <?php echo $this->pagination; ?>

        </div>
            <?php
        }else{?>
          <p class="error" style="margin-top:10px;">You don't have permission to view this project.</p>
        <?php
        }//end canView
      ?>
		</div><!-- / .subject -->
	</div>

    </div>
    <div class="clear"></div>
  </div>

</form>


