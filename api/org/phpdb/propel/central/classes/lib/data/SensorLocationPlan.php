<?php

require_once 'lib/data/LocationPlan.php';
require_once 'lib/data/om/BaseLocationPlan.php';

/**
 * SensorLocationPlan
 *
 * A SensorLocationPlan is a named set of sensor locations
 * for a given {@link Experiment}.
 *
 * experiment -- Experiment, the experiment that owns the SLP
 *
 * name -- String, Human-readable name for the SLP
 *
 * sensorLocations -- Collection of sensor locations
 *
 * @package    lib.data
 *
 * @uses Experiment
 *
 */
class SensorLocationPlan extends LocationPlan {

  /**
   * Constructs a new SensorLocationPlan class,
   * setting the plan_type_id column to LocationPlanPeer::CLASSKEY_SENSORLOCATIONPLAN.
   *
   */
  public function __construct(Experiment $experiment=null,
                              $name="",
                              $sensorLocations=null)
  {
    parent::__construct( $experiment, $name);

    $this->setSensorLocations($sensorLocations);
    $this->setPlanTypeId(LocationPlanPeer::CLASSKEY_SENSORLOCATIONPLAN);
  }

  /**
   * Set the list of SensorLocations for this SensorLocationPlan
   *
   * @param $collsl: array <SensorLocation>
   */
  public function setSensorLocations($collsl) {
    if(is_null($collsl)) $collsl = array();
    $this->collLocations = $collsl;
  }


  /**
   * Get the list of SensorLocations for this SensorLocationPlan
   *
   * @return  array <SensorLocation>
   */
  public function getSensorLocations() {
    return $this->getLocations();
  }


  /**
   * Remove a SensorLocation from a list of SensorLocations of this SensorLocationPlan
   * An alias of removeSensorLocation()
   *
   * @param  SensorLocation
   */
  public function removeLocation(Location $loc) {
    $this->removeSensorLocation($loc);
  }


  /**
   * Remove a SensorLocation from a list of SensorLocations of this SensorLocationPlan
   *
   * @param  SensorLocation
   */
  public function removeSensorLocation(SensorLocation $sensorLocation) {

    if(is_null($sensorLocation) || is_null($sensorLocation->getId())) return;

  	$sensorLocations = $this->getSensorLocations();
  	$newSensorLocations = array();

  	foreach($sensorLocations as $sl) {
  	  if($sensorLocation->getId() != $sl->getId()) {
        $newSensorLocations[] = $sl;
  	  }
  	}

  	$this->collLocations = $newSensorLocations;
  }


  /**
   * Add a SensorLocation to a list of SensorLocations of this SensorLocationPlan
   *
   * @param  SensorLocation
   */
  public function addSensorLocation(SensorLocation $sensorLocation) {
    $this->addLocation($sensorLocation);
  }


  /**
   * get all SensorTypes for this SensorLocationPlan
   *
   * @return array <SensorType>
   */
  public function getAvailableSensorTypes() {
    return SensorTypePeer::findAvailableByExperiment($this->getExperiment()->getId());
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  public function getRESTURI() {
    $exp = $this->getExperiment();
    return $exp->getRESTURI() . "/SensorLocationPlan/{$this->getId()}";
  }

} // SensorLocationPlan

?>
