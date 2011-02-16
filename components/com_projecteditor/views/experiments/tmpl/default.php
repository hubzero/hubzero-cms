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

<form id="frmProject" action="/warehouse/projecteditor/saveabout" method="post">
<!--<input type="hidden" name="username" value="<?php //echo $oUser->username; ?>" />-->
<input type="hidden" name="projid" value="<?php echo $this->iProjectId; ?>" />

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
      
      
      <div class="subject-full">

        <div id="about" style="padding-top:1em;">

          <div class="createLink" style="padding-bottom:1em;">
            <div class="editorInputFloat" style="margin-left:20px;"><img src="/components/com_projecteditor/images/icons/e.gif" alt="New Experiment"/></div>
            <div class="editorInputFloat" style="margin-left:5px;"><a style="font-size: 15px;" href="/warehouse/projecteditor/project/<?php echo $this->iProjectId; ?>/experiment/0/about">Create New Experiment</a></div>
            <div class="clear"></div>
          </div>


          <!--
          <div class="information" style="padding-bottom:1em;">
            <a href="/warehouse/projecteditor/project/<?php //echo $this->iProjectId; ?>/experiment/0/about">Create New Experiment</a>
          </div>
          -->
            
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

            $iViewed = 0;
            $strExperimentThumbnailArray = $_REQUEST[Experiments::THUMBNAILS];

            $oAuthorizer = Authorizer::getInstance();

            /* @var $oExperiment Experiment */
            /* @var $oIndeedDataFile DataFile */
            $oExperimentArray = unserialize($_REQUEST[Experiments::EXPERIMENT_LIST]);
            foreach($oExperimentArray as $iExperimentIndex=>$oExperiment){
              if($oAuthorizer->canView($oExperiment)){
          	  $iExperimentId = $oExperiment->getId();
        	  $strTitle = $oExperiment->getTitle();
        	  $strStartDate = strftime("%B %d, %Y", strtotime($oExperiment->getStartDate()));
        	  $oDescriptionClob = StringHelper::neat_trim($oExperiment->getDescription(), 250);
        	  $strName = $oExperiment->getName();
        	  $strThumbnail = $strExperimentThumbnailArray[$iExperimentIndex];


                  //added on July 1,2010.  Users want to see the "Main" inDEED file in experiment list
                  $oIndeedDataFileArray = $oExperiment->getExperimentIndeedFile("inDEED",
                                                                                $oExperiment->getProjectId(),
                                                                                $oExperiment->getId());
                  if(!empty($oIndeedDataFileArray)){
                    $oIndeedDataFile = end($oIndeedDataFileArray);
                  }

                  if($iViewed > 0){ ?>
                    <hr size="1" color="#cccccc"/>
                    <br>
                  <?php
                  }
                ++$iViewed;

              ?>
              <table id="Experiment<?php echo $iExperimentId; ?>" style="width:100%; border: 0px;">
                <tr>
                  <td>
                    <div id="ExperimentInfo<?php echo $iExperimentId; ?>" style="float:left;width:85%;">
                      <table style="width:100%;border-bottom:0px;border-top:0px;">
                        <tr>
                          <td><b><?php echo $strName.":"; ?></b></td>
                          <td><a href="/warehouse/projecteditor/project/<?php echo $this->iProjectId; ?>/experiment/<?php echo $iExperimentId; ?>/about" style="font-size: 15px;"><?php echo $strTitle; ?></a></td>
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
                        if(sizeof($oIndeedDataFileArray) != 0){
                          $strIndeedPath = $oIndeedDataFile->getPath();
                          $strIndeedName = $oIndeedDataFile->getName();

                          $strLaunchInEED = NeesConfig::LAUNCH_INDEED;
                          
                          $strIndeedReturn = $this->warehouseURL;

                          echo "<a href='$strLaunchInEED=$strIndeedPath/$strIndeedName&$strIndeedReturn'>Launch Data File</a>";
                        }
                      ?>
                   </div>
                   <div class="clear"></div>
                  </td>
                </tr>
              </table>

           <?php
              }
            }//end foreach
           ?>
    
        </div>
      </div>
    </div>
    <div class="clear"></div>
  </div> 

</form>
