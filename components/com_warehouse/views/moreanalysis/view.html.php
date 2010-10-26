<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class WarehouseViewMoreAnalysis extends JView{

  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );

    $iExperimentId = JRequest::getVar('experiment', 0);
    $iTrialId = JRequest::getVar('trial', 0);
    $iRepetitionId = JRequest::getVar('repetition', 0);

    //get the tabs to display on the page
    /* @var $oMoreModel WarehouseModelMoreAnalysis */
    $oMoreModel =& $this->getModel();
    $strTabArray = $oMoreModel->getTabArray();
    $strTabViewArray = $oMoreModel->getTabViewArray();
    $strTabHtml = $oMoreModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "more" );
    $this->assignRef( "strTabs", $strTabHtml );

    /* @var $oHubUser JUser */
    $oHubUser = $oMoreModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );

    $oProject = $oMoreModel->getProjectById($iProjectId);
    if(!$oProject){
      echo ComponentHtml::showError("Project not found.");
      return;
    }

    $bCanViewProject = false;
    $iLowerLimit = 0;
    $iUpperLimit = 0;

    $oAuthorizer = Authorizer::getInstance();
    if($oAuthorizer->canView($oProject)){
      $bCanViewProject = true;

      $oViewableExperimentArray = $oMoreModel->getViewableExperimentsByProject($oAuthorizer, $oProject);
      $oShowExperimentArray =$oViewableExperimentArray[Experiments::SHOW];
      $oHideExperimentArray =$oViewableExperimentArray[Experiments::HIDE];

      //get the photos
      $iDisplay = JRequest::getVar('limit', 25);
      $iPageIndex = JRequest::getVar('index', 0);

      $iLowerLimit = $oMoreModel->computeLowerLimit($iPageIndex, $iDisplay);
      $iUpperLimit = $oMoreModel->computeUpperLimit($iPageIndex, $iDisplay);

      $iResultsCount = $oMoreModel->findDataFileDocumentsByDirectoryCount("Analysis", $oHideExperimentArray, $oProject->getId(), $iExperimentId, $iTrialId, $iRepetitionId);
      $this->assignRef("documentCount", $iResultsCount);

      if($iResultsCount > 1000){
        ini_set('memory_limit', '256M');
      }

      $oDocumentDataFileArray = $oMoreModel->findDataFileDocumentsByDirectory("Analysis", $oHideExperimentArray, $oProject->getId(), $iLowerLimit, $iUpperLimit, $iExperimentId, $iTrialId, $iRepetitionId);
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDocumentDataFileArray);

      /*
       * grab the nees pagination object.  joomla's
       * pagination object doesn't handle the proper uri.
       */
      $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
      $oDbPagination->computePageCount();
      $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmProject", "project-list"));

      $oDistinctExperimentArray = $oMoreModel->findDistinctExperimentsByDirectory("Analysis", $oProject->getId(), $oHideExperimentArray);
      $strExperimentDropDown = $oMoreModel->findDistinctExperimentsByDirectoryHTML($oDistinctExperimentArray, $iExperimentId);

      $strTrialDropDown = $oMoreModel->findDistinctTrialsHTML(array(), $iTrialId);
      if($iExperimentId){
        $oDistinctTrialArray = $oMoreModel->findDistinctTrialsByDirectory("Analysis", $oProject->getId(), $iExperimentId, $oHideExperimentArray);
        $strTrialDropDown = $oMoreModel->findDistinctTrialsHTML($oDistinctTrialArray, $iTrialId);
      }

      $strRepetitionDropDown = $oMoreModel->findDistinctRepetitionsHTML(array(), $iRepetitionId);
      if($iTrialId){
        $oDistinctRepetitionArray = $oMoreModel->findDistinctRepetitionsByDirectory("Analysis", $oProject->getId(), $iExperimentId, $iTrialId, $oHideExperimentArray);
        $strRepetitionDropDown = $oMoreModel->findDistinctRepetitionsHTML($oDistinctRepetitionArray, $iRepetitionId);
      }

      $this->assignRef('strExperimentDropDown', $strExperimentDropDown);
      $this->assignRef('strTrialDropDown', $strTrialDropDown);
      $this->assignRef('strRepetitionDropDown', $strRepetitionDropDown);
    }
    $this->assignRef( 'bCanViewProject', $bCanViewProject );

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
    JFactory::getApplication()->getPathway()->addItem("More","#");
    parent::display($tpl);
  }

}

?>