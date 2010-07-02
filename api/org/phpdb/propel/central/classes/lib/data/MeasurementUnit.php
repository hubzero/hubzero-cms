<?php

require_once 'lib/data/om/BaseMeasurementUnit.php';


/**
 * MeasurementUnit
 *
 * @todo document this
 *
 * @package    lib.data
 *
 * @uses MeasurementUnitCategory
 * @uses MeasurementUnitConversion
 *
 */
class MeasurementUnit extends BaseMeasurementUnit {

  /**
   * Initializes internal state of MeasurementUnit object.
   */
  public function __construct($name='',
                              MeasurementUnit $baseUnit=null,
                              $abbreviation='',
                              MeasurementUnitCategory $category=null,
                              $comment="") {
    $this->setName($name);
    $this->setMeasurementUnitRelatedByBaseUnitId($baseUnit);
    $this->setAbbreviation($abbreviation);
    $this->setMeasurementUnitCategory($category);
    $this->setComment($comment);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/MeasurementUnit/{$this->getId()}";
  }


  /**
   * Get the MeasurementUnitCategory for this MeasurementUnit
   * Backward compatible to NEEScentral 1.7
   *
   * @return MeasurementUnitCategory object
   */
  public function getCategory() {
    return $this->getMeasurementUnitCategory();
  }


  /**
   * Set the MeasurementUnitCategory for this MeasurementUnit
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnitCategory $category
   */
  public function setCategory(MeasurementUnitCategory $category) {
    return $this->setMeasurementUnitCategory($category);
  }


  /**
   * Get the Default MeasurementUnit for this MeasurementUnit
   * Backward compatible to NEEScentral 1.7
   *
   * @return MeasurementUnit object
   */
  public function getBaseUnit() {
    return $this->getMeasurementUnitRelatedByBaseUnitId();
  }


  /**
   * Set the Default MeasurementUnit for this MeasurementUnit
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnit $baseunit
   */
  public function setBaseUnit(MeasurementUnit $baseunit) {
    return $this->setMeasurementUnitRelatedByBaseUnitId($baseunit);
  }



  /**
   * Check if this Unit is a baseUnit
   *
   * @return boolean value
   */
  public function isBaseUnit() {
    if( $this->getMeasurementUnitRelatedByBaseUnitId() ) {
      return false;
    }
    return true;
  }

} // MeasurementUnit
?>
