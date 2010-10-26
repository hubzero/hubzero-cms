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
  //$oAuthorizer->setUser($strUsername);
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>

<form id="frmData" method="get">
<input type="hidden" id="txtExperiment" name="experiment" value="0"/>
<input type="hidden" id="txtTrial" name="trial" value="0" />
<input type="hidden" id="txtRepetition" name="repetition" value="0" />
<input type="hidden" id="txtTool" name="tool" value=""/>
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
            <input type="button" onClick="fileSearch('findby', 'term', 0, 'fileSearch');" value="GO">
        </fieldset>

        <div id="fileSearch"></div>
      </div>
      
      <div class="subject">
        <?php if($oAuthorizer->canView($oProject)){ ?>
        <div id="about" style="padding-top:1em;">
          <p style="margin-bottom:30px;" class="information">Details of each experiment may be found in the <a href="/warehouse/experiments/<?php echo $oProject->getId(); ?>">Experiments</a> tab.</p>
        
          <?php echo $this->strSubTabs; ?>

          <table style="border:0px;margin-top:20px; margin-bottom:10px;">
            <tr>
              <td id="tools"><?php echo $this->strToolArray; ?></td>
              <td align="right">
                <table id="filter" style="border:0px;width:1px;">
                  <tr>
                    <td nowrap id="experiments"><?php echo $this->strExperimentDropDown; ?></td>
                    <td nowrap id="trials"><?php echo $this->strTrialDropDown; ?></td>
                    <td nowrap id="repetitions"><?php echo $this->strRepetitionDropDown; ?></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          
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
