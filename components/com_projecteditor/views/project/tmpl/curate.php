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
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
  $document->addScript($this->baseurl."/includes/js/joomla.javascript.js", 'text/javascript');
?>

<?php JHTML::_('behavior.calendar'); ?>

<?php 
  $oUser = $this->oUser;
?>

<form id="frmProject" action="/warehouse/projecteditor/curateconfirmproject" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="type" value="<?php echo ProjectPeer::CLASSKEY_STRUCTUREDPROJECT; ?>" checked />
<input type="hidden" name="projectId" value="<?php echo $this->iProjectId; ?>"/>
<input type="hidden" name="objectId" value="<?php echo $this->iObjectId; ?>" />

<!--<div class="innerwrap">-->
  <div class="content-header">
    <h2 class="contentheading">NEES Project Warehouse (Curate)</h2>
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
      <span style="font-size:16px;font-weight:bold;">
        <?php 
          if(StringHelper::hasText($this->strTitle)){
            echo $this->strTitle;  
          }else{
            echo "Create a New Project";  
          }
        ?>
      </span>
    </div>



    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <?php
          if(!$this->bHasPhoto){
          ?>
            <p style="font-size:11px;color:#999999; border-width: 1px; border-style: solid; border-color: #cccccc;" align="center">
              <img src="<?php echo $this->strProjectImage; ?>"/><br><br>
              <span style='font-size:48px;font-weight:bold;color:#999999;'>NEES</span><br><br>
            </p>
          <?php
          }else{
          ?>
            <p style="font-size:11px;color:#999999;" align="center">
              <img src="<?php echo $this->strProjectImage; ?>"/><br>
            </p>
          <?php
          }
        ?>

        <input type="hidden" id="usage" name="usageType" value="<?php echo $this->iUsageTypeId; ?>"/>
        <input tabindex="19" type="text" id="txtCaption" name="desc" value="<?php echo $this->strProjectImageCaption; ?>" style="width:210px;color:#999999;" onFocus="this.style.color='#000000'; this.value='';"/> <br><br>
        <input tabindex="20" type="file" id="txtPhoto" name="upload"/>

      
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>
          <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
        </div>

        <div id="editEntity" class="admin-options" style="margin-top:30px">
          <p class="edit"><a href="/warehouse/project/<?php echo $this->iProjectId; ?>">View Project</a></p>
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
          
        <p class="experimentTitle"></p>
        
        <?php echo $this->strSubTabs; ?>

        <div id="about" style="padding-top:1em;">
          <?php 
            if(isset($_REQUEST["ERRORS"])){
              $strErrorArray = $_REQUEST["ERRORS"];
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
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;">
            <tr id="people">
              <td style="width:1px">
                <p class="editorParagraph">
                  <label for="txtOwner" class="editorLabel editorRequired">PI(s):</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="PI(s) :: Please provide the name of the project owner on the grant.  Additional people are added on Team Members tab."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              <td>
                <div id="ownerInput" class="editorInputFloat editorInputSize">
                  <?php if($this->bEditProject):  ?>
                    <input tabindex="1" type="text" id="txtOwner" name="owner" class="editorInputSize" style="width:100%;color: #999999" disabled value="<?php echo $this->strPIs; ?>" />
                  <?php else:  ?>
                    <input tabindex="1" type="text" id="txtOwner" name="owner" class="editorInputSize" style="width:100%;color: #999999" onfocus="this.style.color='';this.value='';" onblur="clearSuggestion('ownerSearch');" onkeyup="suggest('/projecteditor/membersearch?format=ajax', 'ownerSearch', this.value, this.id)" value="<?php echo $this->strPIs; ?>" autocomplete="off" />
                    <div id="ownerSearch" class="suggestResults"></div>
                  <?php endif; ?>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="administrator">
              <td style="width:1px">
                <p class="editorParagraph">
                  <label for="txtOwner" class="editorLabel editorRequired">Administrator:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Administrator :: If you are creating the project on behalf of a PI, enter your name."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              <td>
                <div id="administratorInput" class="editorInputFloat editorInputSize">
                  <?php if($this->bEditProject):  ?>
                    <input tabindex="2" type="text" id="txtAdministrator" disabled name="itperson" style="width:100%;" value="<?php echo $this->strAdministrator; ?>" autocomplete="off" />
                  <?php else: ?>
                    <input tabindex="2" type="text" id="txtAdministrator" name="itperson" style="width:100%;" value="<?php echo $this->strAdministrator; ?>" onkeyup="suggest('/projecteditor/membersearch?format=ajax', 'adminSearch', this.value, this.id)" autocomplete="off" />
                    <div id="adminSearch" class="suggestResults"></div>
                  <?php endif; ?>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="title">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtTitle" class="editorLabel editorRequired">Title:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Title :: Please provide the official project title.  For NSF projects, use the title on the grant."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div class="editorInputSize" id="titleInput">
                  <input tabindex="3" type="text" id="txtTitle" name="title" class="editorInputCurate" value="<?php echo $this->strTitle; ?>" />
                </div>
              </td>
            </tr>
            <tr id="shortTitle">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtShortTitle" class="editorLabel editorRequired">Short Title:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Short Title :: Please provide a meaningful title up to 60 characters.  Prefixes such as NEESR are not allowed."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>  
              <td>
                <div id="shortTitleInput" class="editorInputSize">
                  <input tabindex="4" type="text" id="txtShortTitle" name="shortTitle" class="editorInputCurate" value="<?php echo $this->strShortTitle; ?>" maxlength="60" onBlur="setValue('txtWebsiteTitle', this.value);" />
                </div>
              </td>
            </tr>
            <tr id="start_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strStartDate" class="editorLabel editorRequired">Start Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Start Date :: Please provide the date you started the project."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="startDateInput" class="editorInputFloat editorInputSize">
                  <input tabindex="5" type="text" id="strStartDate" name="startdate" style="width:100%;" value="<?php echo $this->strStartDate; ?>" />
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
                  <input tabindex="6" type="text" id="strEndDate" name="enddate" style="width:100%;" value="<?php echo $this->strEndDate; ?>" />
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
                  		<img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <span style="color:#999999;" class="editorInputSize">Input this information under Experiments.</span>
              </td>
            </tr>
            <tr id="organization">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtOrganization" class="editorLabel">Organization:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Organization :: Please provide the name of the represented organizations.  Submit a ticket to add missing organizations.">
                       <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="organizationInput" class="editorInputFloat editorInputSize">
                  <input tabindex="7" type="text" id="txtOrganization" name="organization[]" class="editorInputSize" style="width:100%;" onblur="clearSuggestion('organizationSearch');" onkeyup="suggest('/projecteditor/organizationsearch?format=ajax', 'organizationSearch', this.value, this.id)" value="<?php echo $this->strOrganization; ?>" autocomplete="off" />
                  <div id="organizationSearch" class="suggestResults"></div>
                </div>
                <div id="organizationAdd" class="editorInputFloat editorInputButton">
                  <a href="javascript:void(0);" title="Add another organization" style="border-bottom: 0px" onClick="addInputViaMootools('/projecteditor/add?format=ajax', 'organization', 'txtOrganization', 'organizationPicked');"><img src="/components/com_projecteditor/images/icons/addButton.png" border="0"/></a>
                </div>
                <div class="clear"></div>
                <div id="organizationPicked">
                  <?php echo $this->strOrganizationPicked; ?>
                </div>
              </td>
            </tr>
            <tr id="curationState">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtShortTitle" class="editorLabel">Curation State:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Curation State :: Please provide a meaningful title up to 60 characters.  Prefixes such as NEESR are not allowed."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="curationStateInput" class="editorInputFloat editorInputSize">
                  <select name="curationState" class="editorInputCurate">
                  <?php
                    $strCurationStateArray = $this->strCurationStateArray;
                    foreach($strCurationStateArray as $strState){
                      $strStateSelected = ($strState==$this->strCurationState) ? "selected" : "";
                      ?>
                      <option value="<?php echo $strState; ?>" <?php echo $strStateSelected; ?>><?php echo $strState; ?></option>
                      <?php
                    }
                  ?>
                  </select>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="conformanceLevel">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboConformanceLevels" class="editorLabel">Conformance Level:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Conformance Level :: Please provide a meaningful title up to 60 characters.  Prefixes such as NEESR are not allowed."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="curationStateInput" class="editorInputFloat editorInputSize">
                  <select id="cboConformanceLevels" name="conformanceLevel" class="editorInputCurate">
                  <?php
                    $strConformanceArray = $this->strConformanceLevelArray;
                    foreach($strConformanceArray as $strConformance){
                      $strConformanceSelected = ($strConformance==$this->strConformanceLevel) ? "selected" : "";
                      ?>
                      <option value="<?php echo $strConformance; ?>" <?php echo $strConformanceSelected; ?>><?php echo $strConformance; ?></option>
                      <?php
                    }
                  ?>
                  </select>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="objectStatus">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboObjectStatus" class="editorLabel">Object Status:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Object Status :: Please provide a meaningful title up to 60 characters.  Prefixes such as NEESR are not allowed."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="objectStatusInput" class="editorInputFloat editorInputSize">
                  <select id="cboObjectStatus" name="objectStatus" class="editorInputCurate">
                  <?php
                    $strObjectStatusArray = $this->strObjectStatusArray;
                    foreach($strObjectStatusArray as $strObjectStatus){
                      $strObjectStatusSelected = ($strObjectStatus==$this->strObjectStatus) ? "selected" : "";
                      ?>
                      <option value="<?php echo $strObjectStatus; ?>" <?php echo $strObjectStatusSelected; ?>><?php echo $strConformance; ?></option>
                      <?php
                    }
                  ?>
                  </select>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="create_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strCreationDate" class="editorLabel">Creation Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Creation Date :: Please provide the date that the entity was created."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="createDateInput" class="editorInputFloat editorInputSize">
                  <input tabindex="6" type="text" id="strCreationDate" name="createdate" class="editorInputCurate" value="<?php echo $this->strCreationDate; ?>" />
                </div>
                <div id="createDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strCreationDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="createdBy">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtShortTitle" class="editorLabel">Created By:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Created By :: Please provide a meaningful title up to 60 characters.  Prefixes such as NEESR are not allowed."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="createdByInput" class="editorInputFloat editorInputSize">
                  <input tabindex="4" type="text" id="txtModifiedBy" name="createdBy" class="editorInputCurate" value="<?php echo $this->strCreatedBy; ?>" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="curate_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strCurationDate" class="editorLabel">Curation Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Curation Date :: Please provide the date that the entity was created."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="curateDateInput" class="editorInputFloat editorInputSize">
                  <input tabindex="6" type="text" id="strCurationDate" name="cureatedate" class="editorInputCurate" value="<?php echo $this->strCurationDate; ?>" />
                </div>
                <div id="createDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strCurationDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="modified_date">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strModifiedDate" class="editorLabel">Modified Date:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Modified Date :: Please provide the date that the entity was modified."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="modifiedDateInput" class="editorInputFloat editorInputSize">
                  <input tabindex="6" type="text" id="strModifiedDate" name="modifieddate" class="editorInputCurate" value="<?php echo $this->strModifiedDate; ?>" />
                </div>
                <div id="modifiedDateCalendar" class="editorInputFloat editorInputButton">
                  <img class="calendar" src="/components/com_warehouse/images/calendar/calendar-blue.png" alt="calendar" onclick="return showCalendar('strModifiedDate', '%m/%d/%Y');" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="modifiedBy">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtShortTitle" class="editorLabel">Modified By:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" class="Tips3" title="Short Title :: Please provide a meaningful title up to 60 characters.  Prefixes such as NEESR are not allowed."><img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" /></a>
                </p>
              </td>
              <td>
                <div id="modifiedByInput" class="editorInputFloat editorInputSize">
                  <input tabindex="4" type="text" id="txtModifiedBy" name="modifiedBy" class="editorInputCurate" value="<?php echo $this->strModifiedBy; ?>" />
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            
            <tr id="description">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtDescription" class="editorLabel">Description:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Description :: Please provide rich text that describes the nature of work being done.">
                       <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <textarea tabindex="8" id="txtDescription" name="description" class="editorTextArea"><?php echo $this->strDescription; ?></textarea>
              </td>
            </tr>
            <tr id="sponsor">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSponsor" class="editorLabel">Sponsor:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Sponsor :: Please provide any funding agencies and award numbers.">
                       <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div class="editorInputFloat editorInputSize" id="sponsorInput" style="width: 40%;">
                  <input tabindex="9" type="text" onblur="clearSuggestion('sponsorSearch');" onkeyup="suggest('/projecteditor/sponsorsearch?format=ajax', 'sponsorSearch', this.value, this.id)" style="width: 95%;color:#999999;" name="sponsor[]" id="txtSponsor" value="<?php echo $this->strSponsor; ?>" onFocus="clearValue(this.id, '<?php echo $this->strSponsor; ?>');document.getElementById(this.id).style.color='#000000'" autocomplete="off"/>
                  <div id="sponsorSearch" class="suggestResults"></div>
                </div>
                <div style="width: 30%;" class="editorInputFloat editorInputSize" id="sponsorAwardInput">
                  <input tabindex="10" type="text" style="width: 98%;color:#999999;" name="award[]" id="txtSponsorAward" value="<?php echo $this->strAward; ?>" onFocus="clearValue(this.id, '<?php echo $this->strAward; ?>');document.getElementById(this.id).style.color='#000000'"/>
                </div>
                <div class="editorInputFloat editorInputButton" id="sponsorAdd">
                  <a onclick="addInputPairViaMootools('/projecteditor/addsponsor?format=ajax', 'sponsor', 'txtSponsor', 'award', 'txtSponsorAward', 'sponsorPicked');"
                     style="border-bottom: 0px none;" title="Add another sponsor" href="javascript:void(0);">
                      <img border="0" src="/components/com_projecteditor/images/icons/addButton.png">
                  </a>
                </div>
                <div class="clear"></div>
                <div id="sponsorPicked">
                  <?php echo $this->strSponsorPicked; ?>
                </div>
              </td>
            </tr>
            <tr id="websites">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtWebsiteTitle" class="editorLabel">Website(s):</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Website(s) :: Please provide the title and URL to any additional resource highlighting the project.">
                     <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                   </a>
                </p>
              </td>
              <td>
                <div class="editorInputFloat editorInputSize" id="websiteInput" style="width: 40%;">
                  <input tabindex="11" type="text" style="width: 95%;color:#999999;" name="website[]" id="txtWebsiteTitle" value="<?php echo $this->strWebsite; ?>" onFocus="clearValue(this.id, '<?php echo $this->strWebsite; ?>');document.getElementById(this.id).style.color='#000000'"/> &nbsp;
                </div>
                <div style="width: 30%;" class="editorInputFloat editorInputSize" id="websiteAwardInput">
                  <input tabindex="12" type="text" style="width: 98%;color:#999999;" name="url[]" id="txtWebsiteUrl" value="<?php echo $this->strUrl; ?>" onFocus="clearValue(this.id, '<?php echo $this->strUrl; ?>');document.getElementById(this.id).style.color='#000000'"/>
                </div>
                <div class="editorInputFloat editorInputButton" id="websiteAdd">
                  <a onclick="addInputPairViaMootools('/projecteditor/addwebsite?format=ajax', 'website', 'txtWebsiteTitle', 'url', 'txtWebsiteUrl', 'websitePicked');" style="border-bottom: 0px none;" title="Add another website" href="javascript:void(0);"><img border="0" src="/components/com_projecteditor/images/icons/addButton.png"></a>
                </div>
                <div class="clear"></div>
                <div id="websitePicked">
                  <?php 
                    echo $this->strWebsitePicked; 
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
                      <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <div id="equipmentList" class="editorInputSize" style="color:#999999;">Input this information under Experiments.</div>
              </td>
            </tr>
            <tr id="pubs">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtPublications" class="editorLabel">Publications:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Pulications :: Click the Contribute link to add a publication as a resource.  Resources are searchable through NEEShub.">
                     <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>Publications are added with <a href="/contribute" target="nees_window">Contribute</a>.</td>
            </tr>
            <tr id="tags">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Tags (keywords):</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Tags :: Please provide keywords that highlight the project.  When users search, they will find your project using the tags.">
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
                      <input tabindex="13" type="text" autocomplete="off" value="" id="actags" name="tags" style="display:none;"/>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
            <tr>
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtStatus" class="editorLabel editorRequired">Status:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Status :: Any project that is funded by the NSF/NEES initiative should be designated as NEES.  All other projects are considered Non-NEES.">
                     <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <fieldset>
                <fieldset>
                  <legend>Initiative</legend>
                  <p>By default, the project status will be:</p>
                  <label>
                        <input tabindex="14" type="radio" <?php if($this->iNees==0)echo "checked='checked'"; ?> value="0" name="nees" class="option"/>
                        <strong>NEES</strong> <span class="indent">The project is funded by NSF/NEES.</span>
                  </label>
                  <label>
                        <input tabindex="15" type="radio" <?php if($this->iNees==1)echo "checked='checked'"; ?> value="1" name="nees" class="option"/>
                        <strong>Non-NEES</strong> <span class="indent">The project is funded by someone outside of NSF/NEES.</span>
                  </label>
                </fieldset>
                </fieldset>
              </td>
            </tr>
            <tr>
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtAccess" class="editorLabel editorRequired">Access Settings:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;" 
                     class="Tips3" title="Access Settings :: Control resources the are available to users.">
                     <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td>
                <fieldset>
                <fieldset>
                  <legend>Content Privacy</legend>
                  <p>By default, the project display (except experiments and data tabs) will be:</p>
                  <label>
                        <input tabindex="16" type="radio" <?php if($this->iAccess==0)echo "checked='checked'"; ?> value="0" name="access" class="option"/>
                        <strong>public</strong> <span class="indent">Resources are available to any user.</span>
                  </label>
                  <label>
                        <input tabindex="17" type="radio" <?php if($this->iAccess==3)echo "checked='checked'"; ?> value="3" name="access" class="option"/>
                        <strong>protected</strong> <span class="indent">Resource titles and descriptions are available to any user but the resource itself is available only to group members.</span>
                  </label>
                  <label>
                        <input tabindex="18" type="radio" <?php if($this->iAccess==4)echo "checked='checked'"; ?> value="4" name="access" class="option"/>
                        <strong>private</strong> <span class="indent">Resources are completely hidden from users outside the group.</span>
                  </label>
                </fieldset>
                </fieldset>
              </td>
            </tr>
            <tr id="preview">
              <td></td>
              <td>
                  <input tabindex="21" type="submit" value="Preview/Save Project" style="margin-top:15px"/>
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