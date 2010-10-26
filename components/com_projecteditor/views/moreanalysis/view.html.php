<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'api/org/nees/oracle/util/DbPagination.php';

class ProjectEditorViewMoreAnalysis extends JView{

  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[ProjectPeer::TABLE_NAME] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );

    $iExperimentId = JRequest::getVar('experiment', 0);
    $iTrialId = JRequest::getVar('trial', 0);
    $iRepetitionId = JRequest::getVar('repetition', 0);

    //get the tabs to display on the page
    /* @var $oMoreModel ProjectEditorModelMoreAnalysis */
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

      $oDocumentDataFileArray = $oMoreModel->findDataFileDocumentsByDirectory("Analysis", $oHideExperimentArray, $oProject->getId(), $iLowerLimit, $iUpperLimit, $iExperimentId, $iTrialId, $iRepetitionId);
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDocumentDataFileArray);

      $iResultsCount = $oMoreModel->findDataFileDocumentsByDirectoryCount("Analysis", $oHideExperimentArray, $oProject->getId(), $iExperimentId, $iTrialId, $iRepetitionId);
      $this->assignRef("documentCount", $iResultsCount);

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

    //set the breadcrumbs
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/projecteditor/project/$iProjectId");
    JFactory::getApplication()->getPathway()->addItem("More","javascript:void(0)");
    parent::display($tpl);
  }

}

?>