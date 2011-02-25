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
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
?>

<?php JHTML::_('behavior.calendar'); ?>
<?php JHTML::_('behavior.modal'); ?>

<?php 
  $oUser = $this->oUser;
  $oExperimentDomainArray = unserialize($_REQUEST[ExperimentDomainPeer::TABLE_NAME]);

  $oExperiment = null;
  if(isset($_REQUEST[ExperimentPeer::TABLE_NAME])){
    $oExperiment = unserialize($_REQUEST[ExperimentPeer::TABLE_NAME]);
  }
  $oAuthorizer = Authorizer::getInstance();
?>

<form id="frmProject" action="/warehouse/projecteditor/saveabout" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="projid" value="<?php echo $this->iProjectId; ?>" />
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" />

<!--<div class="innerwrap">-->
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
      <span style="font-size:16px;font-weight:bold;"><?php echo $this->strProjectTitle; ?></span>
    </div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <?php
          if(!$this->bHasPhoto){
          ?>
            <!--
            <p style="color:#000000; border-width: 1px; border-style: solid; border-color: #cccccc; padding-left:5px;padding-right:5px;padding-top:5px;padding-bottom:5px;">
              Upload a photo that represents your experiment.  The image will appear on the
              Experiments tab of the Project Warehouse.
            </p>
            -->
          <?php
          }else{
          ?>
            <p style="font-size:11px;color:#999999;" align="center">
              <img src="<?php echo $this->strExperimentImage; ?>"/><br>
            </p>
          <?php
          }
        ?>
        <!--
        <input type="hidden" id="usage" name="usageType" value="<?php //echo $this->iUsageTypeId; ?>"/>
        <input type="text" id="txtCaption" name="desc" value="<?php //echo $this->strProjectImageCaption; ?>" style="width:210px;color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/> <br><br>
        <input type="file" id="txtPhoto" name="upload"/>
        -->
        
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>
          <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
        </div>
        
        <div id="editEntity" class="admin-options" style="margin-top:30px">
          <?php
            if($this->iExperimentId){
              $strProjectDisplay = "/warehouse/experiment/".$this->iExperimentId."/project/".$this->iProjectId;
            }
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

        <p class="experimentTitle"><?php echo $this->strTitle; ?></p>

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

            if(StringHelper::hasText($this->submitted)){ ?>
              <p class="information">Curation request submitted to curator.</p>
            <?php
            }
          ?>
          
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;margin-top:10px;">
            <tr id="title">
              <td nowrap="" width="1">
                <p class="editorParagraph">
                  <label for="txtTitle" class="editorLabel">Title:<span class="requiredfieldmarker">*</span></label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Title :: Please provide the official project title.  For NSF projects, use the title on the grant."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <input type="text" id="txtTitle" name="title" class="editorInputSize" value="<?php echo $this->strTitle; ?>" />
              </td>
            </tr>
            <tr id="domain">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboDomain" class="editorLabel">Type of Test:<span class="requiredfieldmarker">*</span></label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Type of Test :: Please provide the general type of equipment used for the experiment."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <select id="cboDomain" name="experimentDomainId">
                  <?php
                    foreach($oExperimentDomainArray as $oExperimentDomain){
                      /* @var $oExperimentDomain ExperimentDomain */

                      $strSelected = ($this->iExperimentDomainId == $oExperimentDomain->getId()) ? " selected" : "";
                    ?>
                    <option value="<?php echo $oExperimentDomain->getId(); ?>" <?php echo $strSelected; ?>><?php echo $oExperimentDomain->getName(); ?></option>
                    <?php
                    }
                  ?>
                </select>
              </td>
            </tr>
            <tr id="description">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtDescription" class="editorLabel">Description:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Description :: Please provide rich text that describes the nature of work being done.">
                       <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <textarea id="txtDescription" name="description" class="editorTextArea"><?php echo $this->strDescription; ?></textarea>
              </td>
            </tr>
            <tr id="start_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strStartDate" class="editorLabel">Start Date:<span class="requiredfieldmarker">*</span></label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Start Date :: Please provide the date you started the project."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="startDateInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="strStartDate" name="startdate" style="width:100%;" value="<?php echo $this->strStartDate; ?>" />
                </div>
                <div id="startDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strStartDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="end_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strEndDate" class="editorLabel">End Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="End Date :: Please provide the date you completed the project."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="endDateInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="strEndDate" name="enddate" style="width:100%;" value="<?php echo $this->strEndDate; ?>" />
                </div>
                <div id="endDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strEndDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="facility">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtFacility" class="editorLabel">Facility:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Facility :: Please provide the name of the NEES site(s).">
                    <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="facilityInput" class="editorInputFloat editorInputSize">
                  <input type="hidden" id="facilityIdList" name="facilityList" value="<?php echo $this->strFacilityIds; ?>"/>
                  <input type="text" id="txtFacility" name="facility[]" class="editorInputSize"
                  		 onkeyup="suggestFacility('/projecteditor/facilitysearch?format=ajax', 'facilitySearch', this.value, this.id, '/projecteditor/equipment?format=ajax', 'equipmentList')" 
                  		 style="width:100%;" value="<?php echo $this->strFacility; ?>"
                                 autocomplete="off"/>
                  <div id="facilitySearch" class="suggestResults"></div>
                </div>
                <div id="facilityAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another facility" 
                     style="border-bottom: 0px" onClick="addInputViaMootools('/projecteditor/add?format=ajax', 'facility', 'txtFacility', 'facilityPicked');">
                       <img alt="" src="/components/com_projecteditor/images/icons/addButton.png" border="0"/>
                  </a>
                </div>
                <div class="clear"></div>
                <div id="facilityPicked">
                  <?php 
                    echo $this->strFacilityPicked;
		  ?>
                </div>
              </td>
            </tr>
            <tr id="equipment">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboEquipment" class="editorLabel">Equipment:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                      class="Tips3" title="Equipment :: Please select the equipment used from the facility (NEES site) above.">
                      <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <?php
                  $strEquipmentColor = "#999999";
                  if($this->strEquipmentList != "Enter one or more facilities (NEES Sites) above."){
                    $strEquipmentColor = "#000000";
                  }
                ?>
                <input type="hidden" id="equipmentlist" name="equipmentlist" value="<?php echo $this->strEquipmentList; ?>"/>
                <div id="equipmentList" style="color:<?php echo $strEquipmentColor; ?>;"><?php //echo $this->strEquipmentList; ?><?php echo $this->strEquipmentPicked; ?></div>
                <div id="equipmentPicked"><?php //echo $this->strEquipmentPicked; ?></div>
              </td>
            </tr>
            <tr id="specimentType">
              <td nowrap>
                <p class="editorParagraph">
                  <label for="txtSpecimenType" class="editorLabel">Specimen Type:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                  	 class="Tips3" title="Specimen Type :: Please provide the specimen type used during the experiment.">
                    <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="specimenTypeInput" class="editorInputFloat editorInputSize">
                  <input type="text" id="txtSpecimenType" name="specimenType" class="editorInputSize"
                  		 onkeyup="suggest('/projecteditor/specimentypesearch?format=ajax', 'specimenTypeSearch', this.value, this.id)"
                  		 style="width:100%;" value="<?php echo $this->strSpecimenType; ?>"
                                 autocomplete="off"/>
                  <div id="specimenTypeSearch" class="suggestResults"></div>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="tags">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Tags (keywords):</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Tags :: Please provide keywords that highlight the experiment.  When users search, they will find your experiment using the tags.">
                     <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="tagInput" class="editorInputSize">
                  <?php
                    if($this->strTags) :
                      echo $this->strTags;
                    else:
                  ?>
                      <input type="text" autocomplete="off" value="" id="actags" name="tags" style="display:none;"/>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <tr id="thumbnail">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtPhoto" class="editorLabel">Photo:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Photo :: Please provide an experiment image.  The thumbnail appears in the experiment listing.">
                       <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <input type="hidden" id="usage" name="usageType" value="<?php echo $this->iUsageTypeId; ?>" />
                <input type="file" id="txtPhoto" name="upload[]"/> &nbsp; <?php echo $this->strExperimentImage; ?>
              </td>
            </tr>
            <tr id="caption">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtPhoto" class="editorLabel">Photo Caption:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Photo Caption :: Please provide rich text that describes the nature of work being done.">
                       <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <input type="text" id="txtCaption" name="desc" value="<?php echo $this->strExperimentImageCaption; ?>" class="editorInputSize" style="color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/>
              </td>
            </tr>
            <tr id="preview">
              <td></td>
              <td>
                <div class="sectheaderbtn editorInputSize">
                  <a tabindex="" href="javascript:void(0);" class="button2"  onClick="document.getElementById('frmProject').submit();">Save About</a>
                  <?php if($oExperiment && $oAuthorizer->canDelete($oExperiment)){ ?>
                    <a tabindex="" href="/warehouse/projecteditor/delete?format=ajax&eid=<?php echo $oExperiment->getId(); ?>&etid=3" class="button2 modal">Delete Experiment</a>
                  <?php } ?>
                  <a tabindex="" href="javascript:void(0);" class="button2" onClick="window.location = '/warehouse/projecteditor/project/<?php echo $this->iProjectId?>/experiments'">Cancel</a>
                  <?php if($oExperiment && $oAuthorizer->canGrant($oExperiment)){ ?>
                    <a tabindex="" href="javascript:void(0);" class="button2"  onClick="document.getElementById('frmProject').action='/warehouse/projecteditor/curaterequest';document.getElementById('frmProject').submit();">Curate Experiment</a>
                  <?php } ?>
                </div>
              </td>
            </tr>
          </table>
    
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div> 
<!--</div>-->

</form>
