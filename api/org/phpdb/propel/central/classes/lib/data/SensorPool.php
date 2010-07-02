<?php

require_once 'lib/data/om/BaseSensorPool.php';


/**
 * SensorPool
 *
 * Links {@link Experiment}s to {@link SensorManifest}s
 * @todo document this class
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses SensorManifest
 */
class SensorPool extends BaseSensorPool {

  /**
   * Constructs a new SensorPool
   */
  public function __construct(Experiment $experiment=null,
                              SensorManifest $manifest=null) {
    $this->setExperiment($experiment);
    $this->setSensorManifest($manifest);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $exp = $this->getExperiment();
    return $exp->getRESTURI() . "/SensorPool/{$this->getId()}";
  }

  /**
   * Get the SensorManifest from this SensorPool
   * Backward compatible with NEEScentral 1.7
   *
   * @return SensorManifest
   */
  public function getManifest() {
    return $this->getSensorManifest();
  }

  /**
   * Set the SensorManifest for this SensorPool
   * Backward compatible with NEEScentral 1.7
   *
   * @param SensorManifest
   */
  public function setManifest(SensorManifest $sm) {
    $this->setSensorManifest($sm);
  }

  /**
   * Get the List of Sensors from this SensorPool
   *
   * @return array <Sensor>
   */
  public function getSensors() {
    return $this->getSensorManifest()->getSensors();
  }

} // SensorPool
?>
