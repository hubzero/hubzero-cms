<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/MeasurementUnitCategoryPeer.php';
require_once 'lib/data/MeasurementUnitPeer.php';
require_once 'lib/data/Location.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Authorization.php';
require_once 'lib/data/AuthorizationPeer.php';
require_once 'lib/data/CoordinateSpacePeer.php';

class ProjectEditorViewEditLocation extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iLocationId = JRequest::getInt("locationId", 0);
    if(!$iLocationId){
      echo "Please select sensor.";
      return;
    }

    $iProjectId = JRequest::getInt("projectId", 0);
    if(!$iProjectId){
      echo "Project not provided.";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not provided";
      return;
    }


    $this->assignRef("locationId", $iLocationId);
    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);

    /* @var $oModel ProjectEditorModelEditLocation */
    $oModel =& $this->getModel();

    $oExperiment = $oModel->getExperimentById($iExperimentId);

    $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oModel->findLocationById($iLocationId));

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

    $_REQUEST[CoordinateSpacePeer::TABLE_NAME] = serialize($oModel->getCoodinateSpaces($oExperiment));

    parent::display();
  }
  
}

?>