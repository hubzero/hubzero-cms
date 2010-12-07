<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addStyleSheet($this->baseurl."/components/com_projecteditor/css/projecteditor.css",'text/css');
  $document->addStyleSheet($this->baseurl."/templates/fresh/html/com_groups/groups.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/general.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
?>

<?php
  $oAuthorizer = Authorizer::getInstance();
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<form id="frmData" name="frmData" method="get">
<input type="hidden" id="txtExperiment" name="experiment" value="0"/>
<input type="hidden" id="txtTrial" name="trial" value="0" />
<input type="hidden" id="txtRepetition" name="repetition" value="0" />
<input type="hidden" id="txtForm" name="form" value="0"/>
<input type="hidden" id="txtSearchType" name="type" value=""/>
<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="title" style="padding-bottom:1em;">
      <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
    </div>

    <div id="treeBrowser" style="float:left;width:20%;"></div>

    <div id="overview_section" class="main section" style="width:100%;float:left;">
      <?php echo TabHtml::getSearchFormWithAction( "frmData", "/warehouse/find" ); ?>

      <?php echo $this->strTabs; ?>

      <?php
        if(!$oAuthorizer->canView($oProject)){
      ?>
          <p class="error">You don't have permission to view this project.</p>
      <?php
        }else{
      ?>
      
        <div class="aside">
          <fieldset>
            <label>
                Find by	<select id="findby" name="findby">
                        <option selected="selected" value="1">Title</option>
                        <option value="2">Name</option>
                </select>
            </label>
            <label>
                Search:	<input type="text" value="" id="term" name="term">
            </label>
            <input type="button" onClick="document.getElementById('frmData').action='/warehouse/filebrowser/<?php echo $oProject->getId(); ?>';document.getElementById('txtSearchType').value='Search';document.getElementById('txtForm').value='1';document.getElementById('frmData').submit();" value="GO">
          </fieldset>

          <div id="fileByType">
            <?php echo $this->mod_warehousefiletypes; ?>
          </div>
        </div>
      
        <div class="subject">
          <div id="about" style="padding-top:1em;">
            <?php echo $this->mod_warehousefiles; ?>
          </div>
        </div>

      <?php } ?>

    </div>
    <div class="clear"></div>
    
  </div>  
</div>
</form>
