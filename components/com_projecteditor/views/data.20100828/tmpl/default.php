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
  $document->addScript($this->baseurl."/components/com_projecteditor/js/general.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/textboxlist.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/observer.js", 'text/javascript');
  $document->addScript($this->baseurl."/plugins/tageditor/autocompleter.js", 'text/javascript');
?>

<?php JHTML::_('behavior.modal'); ?>

<?php 
  $oUser = $this->oUser;
  $strPath = $_REQUEST['CURRENT_DIRECTORY'];
?>

<form id="frmProject" name="frmProject" action="" method="post" enctype="multipart/form-data">
<input type="hidden" name="username" value="<?php echo $oUser->username; ?>" />
<input type="hidden" name="projid" value="<?php echo $this->iProjectId; ?>" />
<input type="hidden" name="experimentId" value="<?php echo $this->iExperimentId; ?>" />
<input type="hidden" id="path" name="path" value="<?php echo $strPath; ?>" />

<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;">Create a New Experiment</span>
    </div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
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
            <tr id="drawings">
              <td nowrap="">
                <p class="editorParagraph">
                  <label for="actags" class="editorLabel">Data:</label>
                  <a style="border-bottom:0px;" href="#" onclick="return false;"
                     class="Tips3" title="Data :: Please provide data files.">
                     <img alt="" src="<?php echo $this->baseurl."/templates/fresh/images/icons/helptab.png" ?>" />
                  </a>
                </p>
                <br>
                <a class="modal" href="/warehouse/projecteditor/createtrial?format=ajax&projectId=<?php echo $this->iProjectId ?>&experimentId=<?php echo $this->iExperimentId; ?>">Create Trial</a><br>
                <a class="modal" href="/warehouse/projecteditor/createrepetition?format=ajax&projectId=<?php echo $this->iProjectId ?>&experimentId=<?php echo $this->iExperimentId; ?>">Create Repetition</a>
              </td>
              <td width="100%">
                <div id="browser" >
                  <?php echo $this->mod_warehouseupload_drawings; ?>
                </div>
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
