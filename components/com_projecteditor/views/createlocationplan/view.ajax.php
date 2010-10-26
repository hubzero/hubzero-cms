<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/Experiment.php';

class ProjectEditorViewCreateLocationPlan extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelCreateLocationPlan */
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
      echo ProjectEditor::EXPERIMENT_ERROR_MESSAGE;
      return;
    }

    $units = $oModel->getXYZUnits();
    $unitId = $oModel->getXYZUnitId();

    if(!is_null($unitId)) {
      $default_unit = MeasurementUnitPeer::find($unitId);
    }else {
      $oMeasurementUnitCategory = MeasurementUnitCategoryPeer::findByName("Distance");
      $oExperiment->getUnit($oMeasurementUnitCategory);
      $default_unit = $oExperiment->getUnit($oMeasurementUnitCategory);
    }

    // Last change to find default unit
    if( is_null($default_unit) ) {
      $default_unit = MeasurementUnitPeer::findBaseUnitByCategory($oMeasurementUnitCategory->getId());
    }
    $_REQUEST["UNITS"] = serialize($units);
    $_REQUEST["DEFAULT_UNIT"] = serialize($default_unit);

    $this->assignRef( "iExperimentId", $iExperimentId );
    
    parent::display($tpl);
  }


  
}
?>
