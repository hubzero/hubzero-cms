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
  $document->addScript($this->baseurl."/components/com_projecteditor/js/tips.js", 'text/javascript');
?>

<?php
  $strUsername = $this->strUsername;
  $oAuthorizer = Authorizer::getInstance();
  $oAuthorizer->setUser($strUsername);
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<form id="frmData" method="get">
<div class="innerwrap">
  <div class="content-header">
	<h2 class="contentheading">NEES Project Warehouse</h2>
  </div>
  
  <div id="warehouseWindow" style="padding-top:20px;">
    <div id="treeBrowser" style="float:left;width:20%;"></div>
    
    <div id="overview_section" class="main section" style="width:100%;float:left;">
      
      <div id="title" style="padding-bottom:1em;">
        <span style="font-size:16px;font-weight:bold;"><?php echo $oProject->getTitle(); ?></span>
      </div>
  
      <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>
      
      <div class="aside">
        <!--
        <fieldset>
            <label>
                Sort by	<select name="sortby">
                        <option selected="selected" value="description ASC">Title</option>
                        <option value="cn ASC">Alias</option>
                </select>
            </label>
            <label>
                    Search:	<input type="text" value="" name="search">
            </label>
            <input type="button" onClick="submitDataForm('frmData', '');" value="GO">
        </fieldset>
        -->
      </div>
      
      <div class="subject">
        <?php if($oAuthorizer->canView($oProject)){ ?>
        <div id="about" style="padding-top:1em;">
          <p style="margin-bottom:30px;" class="information">Details of each experiment may be found in the <a href="/warehouse/experiments/<?php echo $oProject->getId(); ?>">Experiments</a> tab.</p>
        
          <?php echo $this->strSubTabs; ?>
          
          <div style="margin-top:20px; margin-bottom:10px;">
            <div id="tools" style="float:left">
              <?php echo $this->strToolArray; ?>
            </div>
            
            <div id="filter" style="float:left; right: 0pt; position: absolute; margin-right:235px;">
              <div id="experiments" style="float:left; margin-right:10px;">
                <?php echo $this->strExperimentDropDown; ?>
              </div>
              <div id="trials" style="float:left; margin-right:10px;">
                <?php echo $this->strTrialDropDown; ?>
              </div>
              <div id="repetitions" style="float:left;">
                <?php echo $this->strRepetitionDropDown; ?>
              </div>
              <div class="clear"></div>
            </div>
            
            <div class="clear"></div>
          </div>
          
          <?php echo $this->strDataFileArray; ?>
          
          <?php echo $this->pagination; ?>
        </div>
        <?php
        }else{?>
          <p class="error">You don't have permission to view this project.</p>
        <?php
        }//end canView
      ?>
      </div>
      
    </div>
    <div class="clear"></div>
    
  </div>  
</div>
</form>
