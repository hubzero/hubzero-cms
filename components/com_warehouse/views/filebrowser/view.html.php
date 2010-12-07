<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/security/Authorizer.php';

class WarehouseViewFileBrowser extends JView{

  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );

    $iExperimentId = JRequest::getVar('experiment', 0);
    $iTrialId = JRequest::getVar('trial', 0);
    $iRepetitionId = JRequest::getVar('repetition', 0);

    //echo "p: $iProjectId, e: $iExperimentId, t: $iTrialId, r: $iRepetitionId<br>";

    //get the main tabs to display on the page
    /* @var $oModel WarehouseModelFileBrowser */
    $oModel =& $this->getModel();
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strTabHtml = $oModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "filebrowser" );
    $this->assignRef( "strTabs", $strTabHtml );

    $strReturnURL = $oModel->getReturnURL();
    $this->assignRef( "warehouseURL", $strReturnURL );
    JRequest::setVar('warehouseURL', $strReturnURL);

    $this->assignRef( "mod_warehousefiles", ComponentHtml::getModule("mod_warehousefiles") );
    $this->assignRef( "mod_warehousefiletypes", ComponentHtml::getModule("mod_warehousefiletypes") );



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
    JFactory::getApplication()->getPathway()->addItem("File Browser","#");
    parent::display($tpl);
  }

  private function displayToolDropDown($oModel, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool){
    $strToolDropDownArray = "&nbsp;";
    if($strSubTab=="tools"){
      $strToolArray = $oModel->findDistinctOpeningTools($iProjectId, $iExperimentId, $iTrialId, $iRepetitionId);
      $strToolDropDownArray = $oModel->findDistinctOpeningToolsHTML($strToolArray, $strTool);
    }
    $this->assignRef('strToolArray', $strToolDropDownArray);
  }

  private function displayExperimentDropDowns($oModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $strTool, $strUsageType){
    $strTool = ($strTool=="Any") ? "" : $strTool;

    $strExperimentArray = $oModel->findDistinctExperiments($iProjectId, $oHideExperimentArray, $strTool, $strUsageType);
    $strExperiments = $oModel->findDistinctExperimentsHTML($strExperimentArray, $iProjectId, $iExperimentId);
    $this->assignRef('strExperimentDropDown', $strExperiments);
  }

  private function displayTrialDropDowns($oModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetionId, $strTool, $strUsageType){
    $strRepetitions = "";

    $strTool = ($strTool=="Any") ? "" : $strTool;

    $strTrials = $oModel->findDistinctTrialsHTML(array(), $iProjectId, $iExperimentId, $iTrialId);
    if($iExperimentId > 0){
      $strTrialArray = $oModel->findDistinctTrials($oHideExperimentArray, $iProjectId, $iExperimentId, $strTool, $strUsageType);
      $strTrials = $oModel->findDistinctTrialsHTML($strTrialArray, $iProjectId, $iTrialId);
      if(sizeof($strTrialArray)==1){
        $iTrialId = $strTrialArray[0]["TRIAL_ID"];
        $strRepetitionArray = $oModel->findDistinctRepetitions($oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
        $strRepetitions = $oModel->findDistinctRepetitionsHTML($strRepetitionArray, $iRepetionId);
        $this->assignRef('strRepetitionDropDown', $strRepetitions);
      }
    }
    $this->assignRef('strTrialDropDown', $strTrials);

    return $strRepetitions;
  }

  private function displayRepetitionDropDowns($oModel, $oHideExperimentArray, $strSubTab, $iProjectId, $iExperimentId, $iTrialId, $iRepetitionId, $strTool, $strUsageType){
    $strRepetitions = $oModel->findDistinctRepetitionsHTML(array(), $iRepetitionId);
    if($iTrialId > 0){
      $strTool = ($strTool=="Any") ? "" : $strTool;

      $strRepetitionArray = $oModel->findDistinctRepetitions($oHideExperimentArray, $iProjectId, $iExperimentId, $iTrialId, $strTool, $strUsageType);
      $strRepetitions = $oModel->findDistinctRepetitionsHTML($strRepetitionArray, $iRepetitionId);
    }
    $this->assignRef('strRepetitionDropDown', $strRepetitions);
  }

}

?>