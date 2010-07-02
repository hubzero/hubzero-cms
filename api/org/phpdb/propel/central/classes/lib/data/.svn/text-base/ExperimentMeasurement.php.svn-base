<?php

require_once 'lib/data/om/BaseExperimentMeasurement.php';


/**
 * ExperimentMeasurement
 *
 *  @todo document this class
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses MeasurementUnitCategory
 * @uses MeasurementUnit
 *
 */
class ExperimentMeasurement extends BaseExperimentMeasurement {

  /**
   * Initializes internal state of ExperimentMeasurement object.
   */
  public function __construct(Experiment $exp=null,
                              MeasurementUnitCategory $category=null,
                              MeasurementUnit $default_unit=null)
  {
    $this->setExperiment($exp);
    $this->setMeasurementUnitCategory($category);
    $this->setMeasurementUnit($default_unit);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  public function getRESTURI() {
    return "/ExperimentMeasurement/{$this->getId()}";
  }


  /**
   * Get the MeasurementUnitCategory for this ExperimentMeasurement
   * Backward compatible to NEEScentral 1.7
   *
   * @return MeasurementUnitCategory object
   */
  public function getCategory() {
    return $this->getMeasurementUnitCategory();
  }


  /**
   * Set the MeasurementUnitCategory for this ExperimentMeasurement
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnitCategory $category
   */
  public function setCategory(MeasurementUnitCategory $category) {
    return $this->setMeasurementUnitCategory($category);
  }


  /**
   * Get the Default MeasurementUnit for this ExperimentMeasurement
   * Backward compatible to NEEScentral 1.7
   *
   * @return MeasurementUnit object
   */
  public function getDefaultUnit() {
    return $this->getMeasurementUnit();
  }


  /**
   * Set the Default MeasurementUnit for this ExperimentMeasurement
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnit $default_unit
   */
  public function setDefaultUnit(MeasurementUnit $default_unit) {
    return $this->setMeasurementUnit($default_unit);
  }
} // ExperimentMeasurement
?>
