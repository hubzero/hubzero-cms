<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/Experiment.php';

class ProjectEditorViewSensorList extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelSensorList */
    $oModel =& $this->getModel();

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
      echo ProjectEditor::EXPERIMENT_ERROR_MESSAGE;
      return;
    }

    //get the location plans
    $oLocationPlanArray = $oModel->getLocationPlansByExperiment($oExperiment->getId());
    $strLocationPlans = $oModel->getLocationPlansHTML($oLocationPlanArray,$oExperiment->getId());
    $this->assignRef( "strLocationPlans", $strLocationPlans );

    //get the user
    $this->assignRef( "oUser", $oModel->getCurrentUser() );

    $oLocationArray = array();
    $iLocationPlanId = JRequest::getInt('locationPlanId', 0);
    switch ($iLocationPlanId){
      case 0:
          break;
      case -1:
          break;
      default :
          $oLocationArray = $oModel->findLocationsByPlanId($iLocationPlanId);
          break;
    }
    $this->assignRef( "iLocationPlanId", $iLocationPlanId );

    $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oLocationArray);

    $strReturnUrl = $oModel->getRawReturnURL();
    $this->assignRef( "strReturnUrl", $strReturnUrl );

    parent::display($tpl);
  }
  
}
?>
