<?php

require_once 'lib/data/om/BaseCoordinateDimension.php';

/**
 * CoordinateDimension
 *
 * In a {@link CoordinateSpace} each basis has a CoordinateDimension
 * which establishes its name and the type/category of its measurements
 *
 * Measurement basis of a coordinate space.
 *
 * @package    lib.data
 *
 * @uses MeasurementUnitCategory
 */

class CoordinateDimension extends BaseCoordinateDimension {

  /**
   * Initializes internal state of CoordinateDimension object.
   *
   * @param string $name
   * @param MeasurementUnitCategory $unitCategory
   */
  public function __construct($name='',
                              MeasurementUnitCategory $measurementUnitCategory = null)
  {
    $this->setName($name);
    $this->setMeasurementUnitCategory($measurementUnitCategory);
  }

  /**
   * Wrap {@link BaseCoordinateDimension::getMeasurementUNitCategory} for
   * interface compatibility
   *
   * @return MeasurementUnitCategory
   * @deprecated
   */
  public function getUnitCategory() {
    return $this->getMeasurementUnitCategory();
  }

  /**
   * Wrap {@link BaseCoordinateDimension::setMeasurementUNitCategory} for
   * interface compatibility
   *
   * @param MeasurementUnitCategory
   * @deprecated
   */
  public function setUnitCategory(MeasurementUnitCategory $muc) {
    return $this->setMeasurementUnitCategory($muc);
  }

} // CoordinateDimension
?>
