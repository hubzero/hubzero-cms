<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/ExperimentDomainPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewExperiments extends JView{

  function display($tpl = null){
    if(isset($_SESSION["ERRORS"])){
      unset($_SESSION["ERRORS"]);
    }

    $iProjectId = JRequest::getInt('projid', 0);

    /*
     * if we don't have a project id from the request, go to session.
     */
    if(!$iProjectId){
      //if not in session, return error
      if(!isset($_SESSION[ProjectEditor::ACTIVE_PROJECT])){
        echo ComponentHtml::showError(ProjectEditor::PROJECT_ERROR_MESSAGE);
        return;
      }

      //if session value is 0, return error
      $iProjectId = $_SESSION[ProjectEditor::ACTIVE_PROJECT];
      if($iProjectId===0){
        echo ComponentHtml::showError(ProjectEditor::PROJECT_ERROR_MESSAGE);
        return;
      }
    }else{
      //we got a valid request, store in session
      $_SESSION[ProjectEditor::ACTIVE_PROJECT] = $iProjectId;
    }

    $this->assignRef( "iProjectId", $iProjectId );

    /* @var $oModel ProjectEditorModelExperiments */
    $oModel =& $this->getModel();

    /* @var $oProject Project */
    $oProject = $oModel->getProjectById($iProjectId);
    $this->assignRef( "strProjectTitle", $oProject->getTitle() );

    $oAuthorizer = Authorizer::getInstance();
    if(!$oAuthorizer->canCreate($oProject)){
      echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_EXPERIMENT_CREATE_ERROR);
      return;
    }

    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );


    $oExperimentArray = $oModel->findByProject($iProjectId);
    $_REQUEST[Experiments::EXPERIMENT_LIST] = serialize($oExperimentArray);
    $_REQUEST[Experiments::COUNT] = sizeof($oExperimentArray);

    //create thumbnails if needed
    $strExperimentIconArray = array();

    /* @var $oExperiment Experiment */
    foreach($oExperimentArray as $oExperiment){
      $strThumbnail =  $oExperiment->getExperimentThumbnailHTML();
      array_push($strExperimentIconArray, $strThumbnail);
    }
    $_REQUEST[Experiments::THUMBNAILS] = $strExperimentIconArray;

    $strReturnURL = $oModel->getReturnURL();
    $this->assignRef( "warehouseURL", $strReturnURL );

    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/projecteditor/project/".$oProject->getId());
    JFactory::getApplication()->getPathway()->addItem("Experiments","javascript:void(0)");

    parent::display($tpl);
  }

  private function clearSession(){
    unset($_SESSION["facility"]);
    unset($_SESSION["organization"]);
    unset($_SESSION["sponsor"]);
    unset($_SESSION["website"]);
    unset($_SESSION["SUGGESTED_FACILITY_EQUIPMENT"]);
    unset($_SESSION[ProjectGrantPeer::TABLE_NAME]);
    unset($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
    unset($_SESSION[ResearcherKeywordPeer::TABLE_NAME]);
    unset($_SESSION[ProjectOrganizationPeer::TABLE_NAME]);
  }
}
?>
