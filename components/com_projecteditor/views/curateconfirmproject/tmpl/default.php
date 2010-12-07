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
  $document->addStyleSheet($this->baseurl."/plugins/tageditor/autocompleter.css",'text/css');
  
  $document->addScript($this->baseurl."/components/com_projecteditor/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/projecteditor.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php 
  $oUser = $this->oUser;
?>

<form id="frmProject" action="/warehouse/projecteditor/saveproject" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="edit" value="<?php echo $this->bEditProject; ?>" />


<!--<div class="innerwrap">-->
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;">Confirm Project</span>
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
              <img src="<?php echo $this->strProjectImage; ?>" width="210"/><br>
              <?php echo $this->strProjectCaption; ?>
            </p>
          <?php
          }
        ?>
      
        <div id="stats" style="margin-top:30px; border-width: 1px; border-style: dashed; border-color: #cccccc; ">
          <p style="margin-left:10px; margin-top:10px;"><?php echo $this->iEntityActivityLogViews; ?> Views</p>

          <p style="margin-left:10px;"><?php echo $this->iEntityActivityLogDownloads; ?> Downloads</p>
        </div>
        
      
        <div id="curation">
          <span class="curationTitle">Curation in progress:</span>
          <?php //echo $this->mod_curationprogress; ?>
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
                  <label for="txtOwner" class="editorLabel">PI(s):</label>
                </p>
              <td>
                <div id="ownerInput" class="editorInputFloat editorPreviewSize">
                  <?php echo $this->strPIs; ?>
                </div>
                <div class="clear"></div>
              </td>
            </tr>
            <tr id="title">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtTitle" class="editorLabel">Title:</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php echo $this->strTitle; ?></td>
            </tr>
            <tr id="shortTitle">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtShortTitle" class="editorLabel">Short Title:</label>
                </p>
              </td>  
              <td class="editorPreviewSize"><?php echo $this->strShortTitle; ?></td>
            </tr>
            <tr id="dates">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="strStartDate" class="editorLabel">Dates:</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php echo $this->strDates; ?></td>
            </tr>
            <tr id="facility">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtFacility" class="editorLabel">Facility:</label>
                </p>
              </td>
              <td class="editorPreviewSize">
                <span style="color:#999999;">Appears after adding a NEES facility to an experiment.</span>
              </td>
            </tr>
            <tr id="organization">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtOrganization" class="editorLabel">Organization:</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php echo $this->strOrganization; ?></td>
            </tr>
            <tr id="description">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtDescription" class="editorLabel">Description:</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php echo nl2br($this->strDescription); ?></td>
            </tr>
            <tr id="sponsor">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtSponsor" class="editorLabel">Sponsor:</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php echo $this->strSponsor; ?></td>
            </tr>
            <tr id="websites">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtWebsiteTitle" class="editorLabel">Website(s):</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php echo $this->strWebsite; ?></td>
            </tr>
            <tr id="equipment">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="cboEquipment" class="editorLabel">Equipment:</label>
                </p>
              </td>
              <td class="editorPreviewSize">
                <span style="color:#999999;">Appears after adding equipment to an experiment.</span>
              </td>
            </tr>
            <tr id="pubs">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtPublications" class="editorLabel">Publications:</label>
                </p>
              </td>
              <td class="editorPreviewSize">Publications are added with <a href="/contribute" target="nees_window">Contribute</a>.</td>
            </tr>
            <tr id="tags">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="txtTags" class="editorLabel">Ontology:</label>
                </p>
              </td>
              <td class="editorPreviewSize"><?php //echo $this->strTags; ?></td>
            </tr>
            <tr id="submit">
              <td></td>
              <td>
                <?php
                  $strLink = "/warehouse/projecteditor";
                  $iProjectId = $this->iProjectId;
                  if($iProjectId){
                    $strLink .= "/project/".$iProjectId;
                  }

                  $strParams = "?owner=" . rawurlencode($this->strOwner) . "&" .
                               "itperson=" . rawurlencode($this->strAdmin) . "&" .
                               "title=" . rawurlencode($this->strTitle) . "&" .
                               "shortTitle=" . rawurlencode($this->strShortTitle) ."&" .
                               "startdate=" . rawurlencode($this->strStartDate) ."&" .
                               "enddate=" . rawurlencode($this->strEndDate) ."&" .
                               "description=" . rawurlencode($this->strDescription) ."&" .
                               "access=" . $this->iAccess . "&" .
                               "nees=" . $this->iNees . "&" .
                               "continue=1&".
                               "tags=" . rawurlencode($this->strTagsRaw);
                  
//                JRequest::setVar("tags", $this->strTags);
//                $strSpecimenType = StringHelper::EMPTY_STRING;
//                $strOrganization = StringHelper::EMPTY_STRING;
//                $strOrganizationPicked = StringHelper::EMPTY_STRING;
//                $strSponsor = ProjectEditor::DEFAULT_SPONSOR;
//                $strSponsorPicked = StringHelper::EMPTY_STRING;
//                $strAward = ProjectEditor::DEFAULT_AWARD_NUMBER;
//                $strWebsite = ProjectEditor::DEFAULT_WEBSITE_TITLE;
//                $strWebsitePicked = StringHelper::EMPTY_STRING;
//                $strUrl = ProjectEditor::DEFAULT_PROJECT_URL;
                ?>
                <input type="button" value="Edit Project" style="margin-top:15px" onClick="window.location='<?php echo $strLink . $strParams; ?>'"/>
                <input type="submit" value="Save Project" style="margin-top:15px"/>
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