<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('base.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/LocationPlan.php';
require_once 'lib/data/Location.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/MeasurementUnitPeer.php';
require_once 'lib/data/MeasurementUnit.php';
require_once 'lib/data/MeasurementUnitCategory.php';
require_once 'lib/data/MeasurementUnitCategoryPeer.php';
require_once 'lib/data/CoordinateSpacePeer.php';

class ProjectEditorModelSensors extends ProjectEditorModelBase {

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct() {
        parent::__construct();
    }

    /**
     *
     * @param int $p_iExperimentId
     * @param string $p_strName
     * @return array <LocationPlan>
     */
    public function suggestLocationPlans($p_iExperimentId, $p_strName){
      return LocationPlanPeer::suggestLocationPlans($p_iExperimentId, $p_strName);
    }

    /**
     *
     * @param int $p_iExperimentId
     * @return array
     */
    public function getLocationPlansByExperiment($p_iExperimentId){
      return LocationPlanPeer::findAllByExperiment($p_iExperimentId);
    }

    /**
     *
     * @param array $p_oLocationPlanArray
     * @return string
     */
    public function getLocationPlansHTML($p_oLocationPlanArray, $p_iProjectId, $p_iExperimentId, $p_iLocationPlanId=0){
      $strHTML = <<< ENDHTML
	      <select name="locationPlanId" id="cboLpName" onChange="window.location ='/warehouse/projecteditor/sensorlist?locationPlanId='+this.value+'&projid='+$p_iProjectId+'&experimentId='+$p_iExperimentId;">
                  <option value="-1">-Select Sensor List-</option>
ENDHTML;

      /* @var $oLocationPlan LocationPlan */
      foreach($p_oLocationPlanArray as $oLocationPlan){
        $strThisLocationPlanName = $oLocationPlan->getName();
        $iThisLocationPlanId = $oLocationPlan->getId();
        $strSelected = ($iThisLocationPlanId==$p_iLocationPlanId) ? "selected" : "";
        $strHTML .= <<< ENDHTML
	      <option value="$iThisLocationPlanId" $strSelected>$strThisLocationPlanName</option>
ENDHTML;
      }
      

      return $strHTML;
    }
    
    /**
     *
     * @param string $p_strName
     * @return array <SensorType> 
     */
    public function suggestSensorTypes($p_strName){
      return SensorTypePeer::findByNameStartsWith($p_strName);
    }

    /**
     *
     * @param int $p_iLocationPlanId
     * @return array Location
     */
    public function findLocationsByPlanId($p_iLocationPlanId){
      return LocationPeer::findByLocationPlan($p_iLocationPlanId);
    }

    public function findLocationById($p_iLocationId){
      return LocationPeer::retrieveByPK($p_iLocationId);
    }

    public function findLocationPlanById($p_iLocationPlanId){
      return LocationPlanPeer::retrieveByPK($p_iLocationPlanId);  
    }

    /**
     *
     * @return array 
     */
    public function getXYZUnits(){
      return MeasurementUnitPeer::findByCategoryName('Distance');
    }

    /**
     *
     * @param LocationPlan $p_oLocationPlan
     */
    public function getXYZUnitId($p_oLocationPlan=null){
      if( $p_oLocationPlan ) {
        $oLocationArray = LocationPeer::findByLocationPlan($p_oLocationPlan->getId());

        /* @var $oLocation Location */
        foreach($oLocationArray as $oLocation) {
          /* @var $oMeasurementUnit MeasurementUnit */
          $oMeasurementUnit = $oLocation->getMeasurementUnitRelatedByZUnit();
          if($oMeasurementUnit ) {
            return $oMeasurementUnit->getId();
          }
        }
      }
      return null;
    }

    /**
     *
     * @param int $p_iUnitId
     * @param Experiment $p_oExperiment
     * @return MeasurementUnit
     */
    public function getDefaultUnit($p_iUnitId, $p_oExperiment, $p_strCategoryName="Distance"){
      $default_unit = null;

      /* @var $oMeasurementUnitCategory MeasurementUnitCategory */
      $oMeasurementUnitCategory = MeasurementUnitCategoryPeer::findByName($p_strCategoryName);

      if(!is_null($p_iUnitId)) {
        $default_unit = MeasurementUnitPeer::find($p_iUnitId);
      }else{
        $default_unit = $p_oExperiment->getUnit($oMeasurementUnitCategory);
      }

      if( is_null($default_unit) ) {
        $default_unit = MeasurementUnitPeer::findBaseUnitByCategory($oMeasurementUnitCategory->getId());
      }

      return $default_unit;
    }

    public function findDefaultUnit($p_oLocationArray, $p_oExperiment){
      $locs = $p_oLocationArray;

      if(count($locs) > 0) {
        foreach($locs as $loc) {
          $default_unit = $loc->getMeasurementUnitRelatedByZUnit();
          if($default_unit) return $default_unit;
        }
      }

      // If Unit is not found, get the Experiment Base Unit for Distance.
      $cat = MeasurementUnitCategoryPeer::findByName('Distance');
      $catId = $cat->getId();

      $exp_unit = ExperimentMeasurementPeer::findByExperimentAndCategory($p_oExperiment->getId(), $catId);
      if($exp_unit) {
        return $exp_unit->getDefaultUnit();
      }

      // Last change to find default unit
      return MeasurementUnitPeer::findBaseUnitByCategory($catId);
    }

    public function getLocationUnitsHTML(){

    }

    /**
     *
     * @param Experiment $p_oExperiment
     * @return array
     */
    public function getCoodinateSpaces($p_oExperiment){
      return CoordinateSpacePeer::findByExperiment($p_oExperiment->getId());
    }
}
?>