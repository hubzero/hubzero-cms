<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0"); // Date in the past
?>


<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/templates/fresh/html/com_groups/groups.css",'text/css');
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');
  
  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php JHTML::_('behavior.modal'); ?>

<?php 
  $oUser = $this->oUser;

  $iExperimentId = 0;
  $iProjectId = 0;

  /* @var $oExperiment Experiment */
  $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
  if($oExperiment){
    $iExperimentId = $oExperiment->getId();
    $iProjectId = $oExperiment->getProject()->getId();
  }
?>

<form id="frmProject" action="/warehouse/projecteditor/savesetup" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="projid" value="<?php echo $iProjectId; ?>" />
<input type="hidden" name="experimentId" value="<?php echo $iExperimentId; ?>" />

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>

  <div id="quickstart">
    <div id="pdfIcon" class="editorInputFloat">
      <img src="/components/com_projecteditor/images/icons/pdf.jpg"/>&nbsp;&nbsp;
    </div>
    <div id="helpdoc" class="editorInputFloat">
      <a href="<?php echo ProjectEditor::QUICK_START_GUIDE?>" target="peQuickStart">Quick Start Guide</a>
    </div>
    <div class="clear"></div>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;"><?php echo $oExperiment->getProject()->getTitle(); ?></span>
    </div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <!--
        <p style="font-size:11px;color:#999999; border-width: 1px; border-style: solid; border-color: #cccccc;" align="center">
          <img alt="" src="/components/com_projecteditor/images/logos/NEES-logo_grayscale.png"/><br><br>
          <span style="font-size:48px;font-weight:bold;color:#999999;">NEES</span><br><br>
        </p>
        
        <input type="text" id="txtCaption" name="caption" value="Enter photo caption" style="width:210px;color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/> <br><br>
        <input type="file" id="txtPhoto" name="thumbnail"/>
        -->
        
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>
          <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
        </div>

        <div id="editEntity" class="admin-options" style="margin-top:30px">
          <?php
            $strProjectDisplay = "/warehouse/experiment/".$oExperiment->getId()."/project/".$oExperiment->getProjectId();
          ?>
          <p class="edit"><a href="<?php echo $strProjectDisplay; ?>">View Experiment</a></p>
        </div>
      
        <div id="curation">
          <span class="curationTitle">Curation in progress:</span>
          <?php if(StringHelper::hasText($this->mod_curationprogress)){ ?>
            <p><?php echo $this->mod_curationprogress; ?></p>
          <?php }else{ ?>
            <p>No curation yet.</p>
          <?php } ?>
        </div>
        
        <div class="whatisthis">
          <h4>What's this?</h4>
          <p>
            Once the curator starts working with your submission, monitor the object's progress by reading
            the curation history.
          </p>
        </div>
      </div>
      <div class="subject">

        <p class="experimentTitle"><?php echo $oExperiment->getTitle(); ?></p>

        <?php echo $this->strSubTabs; ?>

        <div id="about" style="padding-top:1em;">
          <?php 
            if(isset($_SESSION["ERRORS"])){
              $strErrorArray = $_SESSION["ERRORS"];
              if(!empty($strErrorArray)){?> 
                <p class="error">
                  <?  
                    foreach($strErrorArray as $strError){
                  	  echo $strError."<br>";
                    }
                  ?>
                </p> 
              <?php	
              }
            }
          ?>
          
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;margin-top:20px;">
            <tr id="sensor">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSpecimenMaterial" class="editorLabel">Sensors:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                  	 class="Tips3" title="Specimen Material :: Please provide the specimen material used during the experiment.">
                  		<img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
                <p>
                  <a class="modal" href="/warehouse/projecteditor/createlocationplan?format=ajax&projid=<?php echo $iProjectId; ?>&experimentId=<?php echo $iExperimentId; ?>">New Sensor List</a><br>
                  <?php if($this->iLocationPlanId): ?>
                  <a class="modal" href="/warehouse/projecteditor/uploadsensors?format=ajax&projid=<?php echo $iProjectId; ?>&experimentId=<?php echo $iExperimentId; ?>&locationPlanId=<?php echo $this->iLocationPlanId; ?>">Upload Sensors</a>
                  <?php endif; ?>
                </p>
              </td>
              <td width="100%">
                <div id="sensorPlan" class="editorInputFloat editorInputSizeFull2">
                  <table style="border:0">
                    <tr>
                      <td colspan="3">
                        <label for="planName" class="editorLabel">Current Sensor List:</label>&nbsp;
                        <?php echo $this->strLocationPlans; ?>
                      </td>
                    </tr>
                    <tr>
                      <td colspan="3">
                        <div id="currentSensors">
                          <?php
                          $oLocationArray = unserialize($_REQUEST[LocationPeer::TABLE_NAME]);
                          if(!empty($oLocationArray)){ ?>
                            <table cellpadding="1" cellspacing="1">
                              <thead>
                                <tr>
                                  <th>Sensor ID</th>
                                  <th>Type</th>
                                  <th>Orientation</th>
                                  <th>XYZ Coordinates</th>
                                  <th>Manage</th>
                                </tr>
                              </thead>

                              <?php
                                  foreach($oLocationArray as $iLocationIndex=>$oLocation){
                                    $oOrientationArray = LocationPeer::getOrientation($oLocation);
                                    $strOrientation0 = ($oOrientationArray[0] === "") ? "-" : round($oOrientationArray[0],4);
                                    $strOrientation1 = ($oOrientationArray[1] === "") ? "-" : round($oOrientationArray[1],4);
                                    $strOrientation2 = ($oOrientationArray[2] === "") ? "-" : round($oOrientationArray[2],4);

                                    $strThisType = LocationPeer::getLocationType($oLocation);
                                    $strThisLabel = LocationPeer::getLabel($oLocation);
                                    $strThisX = LocationPeer::formatCoordinate($oLocation, "X");
                                    $strThisY = LocationPeer::formatCoordinate($oLocation, "Y");
                                    $strThisZ = LocationPeer::formatCoordinate($oLocation, "Z");

                                    $strClass = "odd";
                                    if($iLocationIndex %2 === 0 ){
                                      $strClass = "even";
                                    }
                                    ?>
                                      <tr valign="top" class="<?php echo $strClass;?>">
                                        <td><?php echo $strThisLabel; ?></td>
                                        <td><?php echo $strThisType; ?></td>
                                        <td><?php echo $strOrientation0.", ".$strOrientation1.", ".$strOrientation2; ?></td>
                                        <td><?php echo $strThisX.", ".$strThisY.", ".$strThisZ; ?></td>
                                        <td>[<a href="/warehouse/projecteditor/editlocation?format=ajax&locationId=<?php echo $oLocation->getId(); ?>&experimentId=<?php echo $iExperimentId; ?>&projectId=<?php echo $iProjectId; ?>" class="modal">Edit</a>] &nbsp;&nbsp;<!--[<a href="">Delete</a>]--></td>
                                      </tr>
                                    <?php
                                  }
                              ?>

                            </table>

                          <?php
                          }
                        ?>
                        </div>
                      </td>
                    </tr>
                  </table>
                </div>

                
                <div class="clear"></div>
                
              </td>
            </tr>
            
          </table>
    
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div> 
</div>

</form>
