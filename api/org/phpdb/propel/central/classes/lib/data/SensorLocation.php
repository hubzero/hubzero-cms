<?php

require_once 'lib/data/Location.php';
require_once 'lib/data/om/BaseLocation.php';

/**
 * SensorLocation
 *
 * SensorLocation specifies the location (x,y,z) and orientation (i,j,k)
 * of a sensor within a SensorLocationPlan.
 * DAQ Channels refer to a particular SensorLocation.
 *
 * SensorLocationPlan, The "owner" of this sensor location
 *
 * SensorType, specifies the type of sensor @ this location
 *
 * CoordinateSpace, Reference to the coordinate space
 * used for orientation and coordinates
 *
 * label == String, User-understanable label for this location
 *
 * (x,y,z) -- Floats, Coordinates of this location
 *
 * (i, j, k) -- Floats, Orientation of the sensor
 *
 * comment -- Notes.
 *
 * @package    lib.data
 *
 * @uses SensorLocationPlan
 * @uses SensorType
 *
 */
class SensorLocation extends Location {

  /**
   * Constructs a new SensorLocation class,
   * setting the location_type_id column to LocationPeer::CLASSKEY_SENSORLOCATION
   */
  public function __construct( SensorLocationPlan $sensorLocationPlan = null,
                               SensorType $sensorType = null, //SensorType::UNKNOWN,
                               $label = "",
                               $x = 0.0,
                               $y = 0.0,
                               $z = 0.0,
                               $i = 0.0,
                               $j = 0.0,
                               $k = 0.0,
                               CoordinateSpace $coordinateSpace = null,
                               $comment = "",
                               $xUnit = null,
                               $yUnit = null,
                               $zUnit = null,
                               $iUnit = null,
                               $jUnit = null,
                               $kUnit = null)
  {
    parent::__construct();

    $this->setLocationTypeId(LocationPeer::CLASSKEY_SENSORLOCATION);
    $this->setLocationPlan($sensorLocationPlan);
    $this->setSensorType($sensorType);
    $this->setLabel($label);
    $this->setX($x);
    $this->setY($y);
    $this->setZ($z);
    $this->setI($i);
    $this->setJ($j);
    $this->setK($k);
    $this->setCoordinateSpace($coordinateSpace);
    $this->setComment($comment);
    $this->setXUnit($xUnit);
    $this->setYUnit($yUnit);
    $this->setZUnit($zUnit);
    $this->setIUnit($iUnit);
    $this->setJUnit($jUnit);
    $this->setKUnit($kUnit);
  }

  /**
   * Wrap {@link Location::setLocationPlan()} to match DM API
   *
   * @param SensorLocationPlan $slp
   */
  public function setSensorLocationPlan(SensorLocationPlan $slp) {
      $this->setLocationPlan($slp);
  }


  /**
   * Get the SensorLocationPlan for this SensorLocation
   * Backward compatible with NEEScentral 1.7
   *
   * @return SensorLocationPlan
   */
  public function getSensorLocationPlan() {
    return $this->getLocationPlan();
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $slp = $this->getSensorLocationPlan();
    return $slp->getRESTURI() . "/SensorLocation/{$this->getId()}";
  }

} // SensorLocation
?>
