<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewSensors extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelSensors */
    $oModel =& $this->getModel();

    $iProjectId = JRequest::getVar('projid');
    $this->assignRef( "iProjectId", $iProjectId );

    /* @var $oExperiment Experiment */
    $oExperiment = null;
    $iExperimentId = JRequest::getInt('experimentId',0);
    if($iExperimentId){
      $oExperiment = $oModel->getExperimentById($iExperimentId);
      $_SESSION[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);
    }else{
      if(isset($_SESSION[ExperimentPeer::TABLE_NAME])){
        $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
      }
    }

    if(!$oExperiment){
      echo ComponentHtml::showError(ProjectEditor::EXPERIMENT_ERROR_MESSAGE);
      return;
    }else{
      $oAuthorizer = Authorizer::getInstance();
      if(!$oAuthorizer->canEdit($oExperiment)){
        echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_EXPERIMENT_EDIT_ERROR);
        return;
      }
    }

    //get the location plans
    $oLocationPlanArray = $oModel->getLocationPlansByExperiment($oExperiment->getId());

    $iLocationPlanId = 0;
    $oLocationArray = array();
    if(!empty($oLocationPlanArray)){
      /* @var $oLocationPlan LocationPlan */
      $oLocationPlan = $oLocationPlanArray[0];
      $iLocationPlanId = $oLocationPlan->getId();
      $oLocationArray = $oModel->findLocationsByPlanId($iLocationPlanId);
    }
    $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oLocationArray);
    $this->assignRef('iLocationPlanId', $iLocationPlanId);

    $strLocationPlans = $oModel->getLocationPlansHTML($oLocationPlanArray, $iProjectId, $oExperiment->getId(), $iLocationPlanId);
    $this->assignRef( "strLocationPlans", $strLocationPlans );

    //get the user
    $this->assignRef( "oUser", $oModel->getCurrentUser() );
    
    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'sensors');

    //$strSubTabArray = $oModel->getExperimentsSubTabArray();
    //$strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTab );
    //$this->assignRef( "strSubTabs", $strSubTabHtml );

    $strSubTabArray = $oModel->getExperimentsSubTabArray();
    $strSubTabViewArray = $oModel->getExperimentsSubTabViewArray();
    $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTabViewArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $iEntityViews = $oModel->getEntityPageViews(3, $oExperiment->getId());
    $iEntityDownloads = $oModel->getEntityDownloads(3, $oExperiment->getId());

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    JFactory::getApplication()->getPathway()->addItem($oExperiment->getProject()->getName(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId());
    JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiments");
    JFactory::getApplication()->getPathway()->addItem($oExperiment->getTitle(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId());
    JFactory::getApplication()->getPathway()->addItem("Sensors","javascript:void(0)");

    if($oExperiment){
      $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }
    
    $strReturnUrl = $oModel->getRawReturnURL();
    $this->assignRef( "strReturnUrl", $strReturnUrl );

    parent::display($tpl);
  }
  
}
?>
