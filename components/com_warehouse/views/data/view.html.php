<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class WarehouseViewData extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );

    $iExperimentId = JRequest::getVar('experiment', 0);
    $iTrialId = JRequest::getVar('trial', 0);
    $iRepetitionId = JRequest::getVar('repetition', 0);

    //echo $iProjectId.", ".$iExperimentId.",".$iTrialId.",".$iRepetitionId."<br>";
	
    //get the main tabs to display on the page
    $oDataModel =& $this->getModel();
    $strTabArray = $oDataModel->getTabArray();
    $strTabHtml = $oDataModel->getTabs( "warehouse", $iProjectId, $strTabArray, "data" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'tools');

    $strSubTabArray = array("Tools", "Drawings", "Photos");
    $strSubTabHtml = $oDataModel->getSubTabs( "warehouse/data", $iProjectId, $strSubTabArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $strUsageType = "";
    $strTool = "";
	
	/*
     * Find the experiment, trial, and repetition drop downs for the given project.
     * On javascript onchange event gets called when a user selects a drop down value.
     * If we have a project, find the experiment list.
     * If we have a project and experiment, find the trial list.  If only on trial, get reps.
     * If we have project, expeirment, and trial, find the rep list.  
     * 
     * We'll initialize the drop downs to blank.  
     * However, the experiment drop down should be on every subtab.
     */
    $strBlankDropDown = "&nbsp;";
    $this->assignRef('strToolArray', $strBlankDropDown);
    $this->assignRef('strExperimentDropDown', $strBlankDropDown);
    $this->assignRef('strTrialDropDown', $strBlankDropDown);
    $this->assignRef('strRepetitionDropDown', $strBlankDropDown);

    $iDataFileTotal = 0;  //count for pagination
    $iPageIndex = JRequest::getVar('index', 0);

    $strDataFileArray = array();
    switch($strSubTab){
      case "drawings":
        $strUsageType = "Drawing";

        $iDisplay = JRequest::getVar('limit', 25);

        $iLowerLimit = $oDataModel->computeLowerLimit($iPageIndex, $iDisplay);
        $iUpperLimit = $oDataModel->computeUpperLimit($iPageIndex, $iDisplay);

        $iDataFileTotal = $oDataModel->findDataFileByUsageCount( $strUsageType, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId );
        $strDataFileArray = $oDataModel->findDataFileByUsage( $strUsageType, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId );
        $strDataFileArray = $oDataModel->findDataFileByUsageHTML( $strDataFileArray );

        /*
         * Create the pagination
         */
        $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay);
        $oDbPagination->computePageCount();
        $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list"));

        break;
      case "videos":
        $iDisplay = JRequest::getVar('limit', 24);

        $strDataFileArray = "<p class='warning'>TODO: Add videos.</p> ";

        /*
         * Get the trial and repetition drop downs.
         * If strRepetitions is not blank, there's only 1 trial.
         * Thus, we don't need to find the repetitions.  It was done
         * inside the trial method.
         */
        $strRepetitions = $this->displayTrialDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
        if($strRepetitions==""){
          $this->displayRepetitionDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
        }

        /*
         * Create the pagination
         */
        $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay);
        $oDbPagination->computePageCount();
        $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmData", "data-list"));

        break;
      case "photos":
        $iDisplay = JRequest::getVar('limit', 24);

        $iLowerLimit = $oDataModel->computeLowerLimit($iPageIndex, $iDisplay);
        $iUpperLimit = $oDataModel->computeUpperLimit($iPageIndex, $iDisplay);

        $iDataFileTotal = $oDataModel->findPhotoDataFilesCount(array("Drawing", "General Photo"), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
        $strDataFileArray = $oDataModel->findPhotoDataFiles(array("Drawing", "General Photo"), $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $iLowerLimit, $iUpperLimit);
        $strDataFileArray = $oDataModel->findPhotoDataFilesHTML($strDataFileArray);
        //echo $iProjectId.", ".$iExperimentId.",".$iDataFileTotal."<br>";

        /*
         * Get the trial and repetition drop downs.
         * If strRepetitions is not blank, there's only 1 trial.
         * Thus, we don't need to find the repetitions.  It was done
         * inside the trial method.
         */
        $strRepetitions = $this->displayTrialDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
        if($strRepetitions==""){
          $this->displayRepetitionDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
        }

        /*
         * Create the pagination
         */
        $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay);
        $oDbPagination->computePageCount();
        $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmData", "data-list"));

        break;
      default:
        $strTool = JRequest::getVar("tool", "inDEED");

        $iDisplay = JRequest::getVar('limit', 25);

        $iLowerLimit = $oDataModel->computeLowerLimit($iPageIndex, $iDisplay);
        $iUpperLimit = $oDataModel->computeUpperLimit($iPageIndex, $iDisplay);

        $iDataFileTotal = $oDataModel->findDataFileOpeningToolsCount( $strTool, $iProjectId, $iExperimentId, $iTrialId );
        $strDataFileArray = $oDataModel->findDataFileOpeningTools( $strTool, $iLowerLimit, $iUpperLimit, $iProjectId, $iExperimentId, $iTrialId);
        $strDataFileArray = $oDataModel->findDataFileOpeningToolsHTML( $strDataFileArray );

        $this->displayToolDropDown($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool);

        /*
         * Get the trial and repetition drop downs.
         * If strRepetitions is not blank, there's only 1 trial.
         * Thus, we don't need to find the repetitions.  It was done
         * inside the trial method.
         */
        $strRepetitions = $this->displayTrialDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
        if($strRepetitions==""){
          $this->displayRepetitionDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType);
        }

        /*
         * Create the pagination
         */
        $oDbPagination = new DbPagination($iPageIndex, $iDataFileTotal, $iDisplay);
        $oDbPagination->computePageCount();
        $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmData", "data-list"));
        break;
    }
    $this->assignRef( "strDataFileArray", $strDataFileArray );  //every subtab
    $this->displayExperimentDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $strTool, $strUsageType);  //every subtab
	
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
  
  private function displayExperimentDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $strTool, $strUsageType){
    $strExperimentArray = $oDataModel->findDistinctExperiments($iProjectId, $strTool, $strUsageType);
    $strExperiments = $oDataModel->findDistinctExperimentsHTML($strExperimentArray, $iProjectId, $iExperimentId);
    $this->assignRef('strExperimentDropDown', $strExperiments);
  }
  
  private function displayTrialDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetionId, $strTool, $strUsageType){
    $strRepetitions = "";

    $strTrials = $oDataModel->findDistinctTrialsHTML(array(), $iProjectId, $iExperimentId, $iTrialId);
    if($iExperimentId > 0){
      $strTrialArray = $oDataModel->findDistinctTrials($iProjectId, $iExperimentId, $strTool, $strUsageType);
      $strTrials = $oDataModel->findDistinctTrialsHTML($strTrialArray, $iProjectId, $iTrialId);
      if(sizeof($strTrialArray)==1){
        $iTrialId = $strTrialArray[0]["TRIAL_ID"];
        $strRepetitionArray = $oDataModel->findDistinctRepetitions($iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
        $strRepetitions = $oDataModel->findDistinctRepetitionsHTML($strRepetitionArray, $iRepetionId);
        $this->assignRef('strRepetitionDropDown', $strRepetitions);
      }
    }
    $this->assignRef('strTrialDropDown', $strTrials);

    return $strRepetitions;
  }
  
  private function displayRepetitionDropDowns($oDataModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType){
    $strRepetitions = $oDataModel->findDistinctRepetitionsHTML(array(), $iRepetitionId);
    if($iTrialId > 0){
      $strRepetitionArray = $oDataModel->findDistinctRepetitions($iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
      $strRepetitions = $oDataModel->findDistinctRepetitionsHTML($strRepetitionArray, $iRepetitionId);
    }
    $this->assignRef('strRepetitionDropDown', $strRepetitions);
  }
  
}

?>