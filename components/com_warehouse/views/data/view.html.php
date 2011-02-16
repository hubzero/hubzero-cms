<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/security/Authorizer.php';

class WarehouseViewData extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::find($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );

    $iExperimentId = JRequest::getVar('experiment', 0);
    $iTrialId = JRequest::getVar('trial', 0);
    $iRepetitionId = JRequest::getVar('repetition', 0);

    //echo "p: $iProjectId, e: $iExperimentId, t: $iTrialId, r: $iRepetitionId<br>";

    //get the main tabs to display on the page
    /* @var $oDataModel WarehouseModelData */
    $oDataModel =& $this->getModel();
    $strTabArray = $oDataModel->getTabArray();
    $strTabViewArray = $oDataModel->getTabViewArray();
    $strTabHtml = $oDataModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "data" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'tools');

    $strSubTabArray = array("Tools", "Drawings", "Photos");
    $strSubTabHtml = $oDataModel->getSubTabs( "warehouse/data", $iProjectId, $strSubTabArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $strBlankDropDown = "&nbsp;";
    $this->assignRef('strToolArray', $strBlankDropDown);
    $this->assignRef('strExperimentDropDown', $strBlankDropDown);
    $this->assignRef('strTrialDropDown', $strBlankDropDown);
    $this->assignRef('strRepetitionDropDown', $strBlankDropDown);

    $iDataFileTotal = 0;  //count for pagination
    $iPageIndex = JRequest::getVar('index', 0);

    $strDataFileArray = array();

    $strUsageType = "";
    $strTool = "";
    $iLowerLimit = 0;
    $iUpperLimit = 0;

    $oAuthorizer = Authorizer::getInstance();
    $bCanViewProject = $oAuthorizer->canView($oProject);
    if($bCanViewProject){
      $oViewableExperimentArray = $oDataModel->getViewableExperimentsByProject($oAuthorizer, $oProject);
      $oShowExperimentArray = $oViewableExperimentArray[Experiments::SHOW];
      $oHideExperimentArray = $oViewableExperimentArray[Experiments::HIDE];

      switch($strSubTab){
        case "drawings":
          $strUsageType = "Drawing";

          $iDisplay = JRequest::getVar('limit', 25);

          $iLowerLimit = $oDataModel->computeLowerLimit($iPageIndex, $iDisplay);
          $iUpperLimit = $oDataModel->computeUpperLimit($iPageIndex, $iDisplay);

          $iDataFileTotal = $oDataModel->findDataFileByUsageCount( $strUsageType, $oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId );
          if(!$iUpperLimit)$iUpperLimit = $iDataFileTotal;  //user may have selected All in pagination

          if($iDataFileTotal > 1000){
            ini_set('memory_limit', '256M');
          }

          $oDataFileArray = $oDataModel->findDataFileByUsage( $strUsageType, $oHideExperimentArray, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId );
          $oDataModel->resizePhotos($oDataFileArray);
          $strDataFileArray = $oDataModel->findDataFileByUsageHTML( $oDataFileArray );

          $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay, $iLowerLimit, $iUpperLimit);
          $oDbPagination->computePageCount();
          $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list"));

          break;
        case "videos":
          $iDisplay = JRequest::getVar('limit', 24);

          $strDataFileArray = "<p class='warning'>TODO: Add videos.</p> ";


          $strRepetitions = $this->displayTrialDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
          if($strRepetitions==""){
            $this->displayRepetitionDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
          }


          $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
          $oDbPagination->computePageCount();
          $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmData", "data-list"));

          break;
        case "photos":
          $iDisplay = JRequest::getVar('limit', 24);

          $iLowerLimit = $oDataModel->computeLowerLimit($iPageIndex, $iDisplay);
          $iUpperLimit = $oDataModel->computeUpperLimit($iPageIndex, $iDisplay);

          $iDataFileTotal = $oDataModel->findPhotoDataFilesCount($oHideExperimentArray, array("Drawing", "Drawing-Sensor", "Drawing-Setup", "Drawing-Specimen", "General Photo"), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
          if(!$iUpperLimit)$iUpperLimit = $iDataFileTotal; //user may have selected All in pagination

          if($iDataFileTotal > 1000){
            ini_set('memory_limit', '256M');
          }

          $strDataFileArray = $oDataModel->findPhotoDataFiles($oHideExperimentArray, array("Drawing", "Drawing-Sensor", "Drawing-Setup", "Drawing-Specimen", "General Photo"), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $iLowerLimit, $iUpperLimit);
          $strDataFileArray = $oDataModel->findPhotoDataFilesHTML($strDataFileArray);

          $strRepetitions = $this->displayTrialDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
          if($strRepetitions==""){
            $this->displayRepetitionDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
          }

          $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay, $iLowerLimit, $iUpperLimit);
          $oDbPagination->computePageCount();
          $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmData", "data-list"));

          break;
        default:
          $strTool = JRequest::getVar("tool", "inDEED");

          $iDisplay = JRequest::getVar('limit', 25);

          $iLowerLimit = $oDataModel->computeLowerLimit($iPageIndex, $iDisplay);
          $iUpperLimit = $oDataModel->computeUpperLimit($iPageIndex, $iDisplay);

          if($strTool != "Any" && $strTool != ""){
            $iDataFileTotal = $oDataModel->findDataFileOpeningToolsCount( $strTool, $oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId );
            if(!$iUpperLimit)$iUpperLimit = $iDataFileTotal; //user may have selected All in pagination

            if($iDataFileTotal > 1000){
              ini_set('memory_limit', '256M');
            }

            $strDataFileArray = $oDataModel->findDataFileOpeningTools( $strTool, $oHideExperimentArray, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
            $strDataFileArray = $oDataModel->findDataFileOpeningToolsHTML( $strDataFileArray );
          }else{
            $iDataFileTotal = $oDataModel->findDataFilesCount( $oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId );
            if(!$iUpperLimit)$iUpperLimit = $iDataFileTotal; //user may have selected All in pagination

            if($iDataFileTotal > 1000){
              ini_set('memory_limit', '256M');
            }
          
            $strDataFileArray = $oDataModel->findDataFiles( $oHideExperimentArray, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
            $strDataFileArray = $oDataModel->findDataFilesHTML( $strDataFileArray );
          }

          $this->displayToolDropDown($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool);

          $strRepetitions = $this->displayTrialDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
          if($strRepetitions==""){
            $this->displayRepetitionDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
          }

          $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay, $iLowerLimit, $iUpperLimit);
          $oDbPagination->computePageCount();
          $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list"));
          break;
      }//end switch
    }//end canView

    
    
    $this->assignRef( "strDataFileArray", $strDataFileArray );  //every subtab
    $this->displayExperimentDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $strTool, $strUsageType);  //every subtab

    /* @var $oHubUser JUser */
    $oHubUser = $oDataModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );
    
    /*
     * set the breadcrumbs
     */
    $bSearch = false;
    if(isset($_SESSION[Search::KEYWORDS]))$bSearch = true;
    if(isset($_SESSION[Search::SEARCH_TYPE]))$bSearch = true;
    if(isset($_SESSION[Search::FUNDING_TYPE]))$bSearch = true;
    if(isset($_SESSION[Search::MEMBER]))$bSearch = true;
    if(isset($_SESSION[Search::START_DATE]))$bSearch = true;
    if(isset($_SESSION[Search::END_DATE]))$bSearch = true;

    //set the breadcrumbs
    JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
    if($bSearch){
      JFactory::getApplication()->getPathway()->addItem("Results","/warehouse/find?keywords=".$_SESSION[Search::KEYWORDS]
                                                                                                                            . "&type=".$_SESSION[Search::SEARCH_TYPE]
                                                                                                                            . "&funding=".$_SESSION[Search::FUNDING_TYPE]
                                                                                                                            . "&member=".$_SESSION[Search::MEMBER]
                                                                                                                            . "&startdate=".$_SESSION[Search::START_DATE]
                                                                                                                            . "&startdate=".$_SESSION[Search::END_DATE]);
    }
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/project/$iProjectId");
    JFactory::getApplication()->getPathway()->addItem("Data","#");
    parent::display($tpl);
  }
  
  private function displayToolDropDown($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool){
    $strToolDropDownArray = "&nbsp;";
    if($strSubTab=="tools"){
      $strToolArray = $oDataModel->findDistinctOpeningTools($iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
      $strToolDropDownArray = $oDataModel->findDistinctOpeningToolsHTML($strToolArray, $strTool);
    }
    $this->assignRef('strToolArray', $strToolDropDownArray);
  }
  
  private function displayExperimentDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $strTool, $strUsageType){
    $strTool = ($strTool=="Any") ? "" : $strTool;

    $strExperimentArray = $oDataModel->findDistinctExperiments($iProjectId, $oHideExperimentArray, $strTool, $strUsageType);
    $strExperiments = $oDataModel->findDistinctExperimentsHTML($strExperimentArray, $iProjectId, $iExperimentId);
    $this->assignRef('strExperimentDropDown', $strExperiments);
  }
  
  private function displayTrialDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetionId, $strTool, $strUsageType){
    $strRepetitions = "";

    $strTool = ($strTool=="Any") ? "" : $strTool;

    $strTrials = $oDataModel->findDistinctTrialsHTML(array(), $iProjectId, $iExperimentId, $iTrialId);
    if($iExperimentId > 0){
      $strTrialArray = $oDataModel->findDistinctTrials($oHideExperimentArray, $iProjectId, $iExperimentId, $strTool, $strUsageType);
      $strTrials = $oDataModel->findDistinctTrialsHTML($strTrialArray, $iProjectId, $iTrialId);
      if(sizeof($strTrialArray)==1){
        $iTrialId = $strTrialArray[0]["TRIAL_ID"];
        $strRepetitionArray = $oDataModel->findDistinctRepetitions($oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
        $strRepetitions = $oDataModel->findDistinctRepetitionsHTML($strRepetitionArray, $iRepetionId);
        $this->assignRef('strRepetitionDropDown', $strRepetitions);
      }
    }
    $this->assignRef('strTrialDropDown', $strTrials);

    return $strRepetitions;
  }
  
  private function displayRepetitionDropDowns($oDataModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType){
    $strRepetitions = $oDataModel->findDistinctRepetitionsHTML(array(), $iRepetitionId);
    if($iTrialId > 0){
      $strTool = ($strTool=="Any") ? "" : $strTool;
      
      $strRepetitionArray = $oDataModel->findDistinctRepetitions($oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
      $strRepetitions = $oDataModel->findDistinctRepetitionsHTML($strRepetitionArray, $iRepetitionId);
    }
    $this->assignRef('strRepetitionDropDown', $strRepetitions);
  }
  
}

?>