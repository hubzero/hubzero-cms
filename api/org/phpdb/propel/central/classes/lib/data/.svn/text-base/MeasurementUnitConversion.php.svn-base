<?php

require_once 'lib/data/om/BaseMeasurementUnitConversion.php';


/**
 * MeasurementUnitConversion
 *
 * Keeps track of Conversions between {@link MeasurementUnit}s of the same {@link MeasurementUnitCategory}
 *
 * @package    lib.data
 *
 * @uses MeasurementUnit
 * @uses MeasurementUnitCategory
 *
 */
class MeasurementUnitConversion extends BaseMeasurementUnitConversion {

  /**
   * Initializes internal state of MeasurementUnitConversion object.
   */
  public function __construct( $from=null,
                               $to=null,
                               $k0=0.0,
                               $k1=1.0) {
    $this->setFrom($from);
    $this->setTo($to);
    $this->setK0($k0);
    $this->setK1($k1);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/MeasurementUnitConversion/{$this->getId()}";
  }

  /**
   * Get the MeasurementUnit that related by FromId
   *
   * @return MeasurementUnit
   */
  public function getFrom() {
    return $this->getMeasurementUnitRelatedByFromId();
  }

  /**
   * Set the MeasurementUnit that related by FromId
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnit $mu
   */
  public function setFrom( $mu) {
    $this->setMeasurementUnitRelatedByFromId($mu);
  }


  /**
   * Get the MeasurementUnit that related by ToId
   *
   * @return MeasurementUnit
   */
  public function getTo() {
    return $this->getMeasurementUnitRelatedByToId();
  }


  /**
   * Set the MeasurementUnit that related by ToId
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnit $mu
   */
  public function setTo( $mu) {
    $this->setMeasurementUnitRelatedByToId($mu);
  }
} // MeasurementUnitConversion
?>
