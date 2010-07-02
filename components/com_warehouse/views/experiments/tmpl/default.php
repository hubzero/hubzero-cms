<?php 
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<?php 
  $document =& JFactory::getDocument();
  $document->addStyleSheet($this->baseurl."/components/com_warehouse/css/warehouse.css",'text/css');
  $document->addScript($this->baseurl."/components/com_warehouse/js/ajax.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/warehouse.js", 'text/javascript');
  $document->addScript($this->baseurl."/components/com_warehouse/js/resources.js", 'text/javascript');
?>

<?php $oProject = unserialize($_REQUEST[Search::SELECTED]); ?>
 
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
      <?php echo TabHtml::getSearchForm( "/warehouse/find" ); ?>
      <?php echo $this->strTabs; ?>
        
      <form id="frmExperiments" style="margin:0px;padding:0px;">
	    <input type="hidden" name="task" value="find"/>
	    <input type="hidden" name="keywords" value="<?php echo $_REQUEST[Search::KEYWORDS]; ?>"/>
        <div id="experiment-list" class="subject-full">
          <?php
            /* @var $oExperiment Experiment */
            /* @var $oIndeedDataFile DataFile */
            $oExperimentArray = unserialize($_REQUEST[Experiments::EXPERIMENT_LIST]);        
            foreach($oExperimentArray as $iExperimentIndex=>$oExperiment){
          	  $iExperimentId = $oExperiment->getId();
        	  $strTitle = $oExperiment->getTitle();
        	  $strStartDate = strftime("%B %d, %Y", strtotime($oExperiment->getStartDate()));
        	  $oDescriptionClob = StringHelper::neat_trim($oExperiment->getDescription(), 250);
        	  $strName = $oExperiment->getName();
        	  $strThumbnail = $oExperiment->getExperimentThumbnailHTML();

                  //added on July 1,2010.  Users want to see the "Main" inDEED file in experiment list
                  $oIndeedDataFileArray = $oExperiment->getExperimentIndeedFile("inDEED",
                                                                                $oExperiment->getProjectId(),
                                                                                $oExperiment->getId());
                  if(!empty($oIndeedDataFileArray)){
                    $oIndeedDataFile = end($oIndeedDataFileArray);
                  }
          ?>
            <div id="Experiment<?php echo $iExperimentId; ?>" style="width:100%;">
              <div id="ExperimentInfo<?php echo $iExperimentId; ?>" style="float:left;width:90%;">
                <table style="width:100%;border-bottom:0px;border-top:0px;">
                  <tr>
                    <td><b>Experiment<?php //echo $strName; ?>:</b></td>
                    <td><a href="/warehouse/experiment/<?php echo $iExperimentId; ?>/project/<?php echo $this->projid ?>" style="font-size: 15px;"><?php echo $strTitle; ?></a></td>
                  </tr>
                  <tr>  
                    <td><b>Start Date:</b></td>
                    <td><span style="color: #666666"><?php echo $strStartDate; ?></span></td>
                  </tr>
                  <tr>
                    <td><b>Description:</b></td>
                    <td width="85%"><?php echo $oDescriptionClob; ?></td>
                  </tr>
                </table>	
              </div>
              <div align="right" id="thumbnail">
                <?php 
                  if( strlen($strThumbnail) > 0 ){
                    echo $strThumbnail;
                  }

                  //display inDEED file below thumbnail.
                  if($oIndeedDataFile){
                    $strIndeedPath = $oIndeedDataFile->getPath();
                    $strIndeedName = $oIndeedDataFile->getName();
                    echo "<a href='/indeed?task=process&list=$strIndeedPath/$strIndeedName'>Launch Data File</a>";
                  }
                ?>
              </div>
              <div class="clear"></div>
            </div>
            
            <?php if($iExperimentIndex < (sizeof($oExperimentArray)-1)): ?>
              <hr size="1" color="#cccccc"/>
              <br>
            <?php endif; ?>
            
          <?php 
            }
          ?>
      
          <?php 
            $lim   = $mainframe->getUserStateFromRequest("$option.limit", 'limit', 25, 'int'); //I guess getUserStateFromRequest is for session or different reasons
		    $lim0  = JRequest::getVar('limitstart', 0, '', 'int');
            $iExperimentsCount = $_REQUEST[Experiments::COUNT];

            jimport('joomla.html.pagination');
		    $pageNav = new JPagination( $iExperimentsCount, $lim0, $lim );
		  
		    echo ViewHtml::fixPaginationLinks("warehouse", $_SERVER["REQUEST_URI"], $pageNav->getListFooter());
		  ?>
        </div>
      </form>
    </div>
    <div class="clear"></div>
  </div>
  
</div>


