<?php

require_once 'lib/data/om/BaseSensorSensorManifest.php';


/**
 * SensorSensorManifest
 *
 * m2m relationship between {@link Sensor} and {@link SensorManifest}
 *
 * @package    lib.data
 *
 * @uses Sensor
 * @uses SensorManifest
 *
 */
class SensorSensorManifest extends BaseSensorSensorManifest {

  public function __construct(Sensor $sensor=null,
                              SensorManifest $manifest=null) {
    $this->setSensor($sensor);
    $this->setSensorManifest($manifest);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $sem = $this->getManifest();
    return $sem->getRESTURI() . "/SensorSensorManifest/{$this->getId()}";
  }

  /**
   * Get SensorManifest from this SensorSensorManifest
   * Backward compatible with NEEScentral 1.7
   *
   * @return Sensormanifest
   */
  public function getManifest() {
    return $this->getSensorManifest();
  }


  /**
   * Set the SensorManifest for this SensorSensorManifest
   * Backward compatible with NEEScentral 1.7
   *
   * @param Sensormanifest
   */
  public function setManifest(SensorManifest $sm) {
    $this->setSensorManifest($sm);
  }


} // SensorSensorManifest
?>
