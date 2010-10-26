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

<?php
  $oUser = $this->oUser;
  $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
?>

<form id="frmProject" action="/warehouse/projecteditor/savesecurity" method="post">
<input type="hidden" name="projectId" value="<?php echo $this->iProjectId; ?>" />
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" />

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
          
          <table cellpadding="1" cellspacing="1" style="border-bottom:0px;border-top:0px;">
            <tr>
              <td nowrap>
                <p class="editorParagraph">
                  <label for="txtAccess" class="editorLabel">Access Settings: <span class="requiredfieldmarker">*</span></label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Access Settings :: Control resources the are available to users.">
                     <img src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
              </td>
              <td width="100%">
                <fieldset>
                <fieldset>
                  <legend>Content Privacy</legend>
                  <p>By default, resources added to this group will be:</p>
                  <label>
                        <input type="radio" <?php if($this->iAccess==0)echo "checked='checked'"; ?> value="0" name="access" class="option"/>
                        <strong>public</strong> <span class="indent">Resources are available to any user.</span>
                  </label>
                  <label>
                        <input type="radio" <?php if($this->iAccess==3)echo "checked='checked'"; ?> value="3" name="access" class="option"/>
                        <strong>protected</strong> <span class="indent">Resource titles and descriptions are available to any user but the resource itself is available only to group members.</span>
                  </label>
                  <label>
                        <input type="radio" <?php if($this->iAccess==4)echo "checked='checked'"; ?> value="4" name="access" class="option"/>
                        <strong>private</strong> <span class="indent">Resources are completely hidden from users outside the group.</span>
                  </label>
                </fieldset>
                </fieldset>
              </td>
            </tr>
            <tr id="preview">
              <td></td>
              <td>
                  <input type="submit" value="Save Security" style="margin-top:15px"/>
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
