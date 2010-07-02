<?php

require_once 'lib/data/om/BaseMaterialTypeProperty.php';


/**
 * MaterialTypeProperty
 *
 *  represents a building material's available properties.
 *
 *  @todo document this better
 *
 * @package    lib.data
 *
 * @uses MaterialType
 * @uses MeasurementUnitCategory
 * @uses MeasurementUnit
 */
class MaterialTypeProperty extends BaseMaterialTypeProperty {

  /**
   * Initializate the internal state of a MaterialTypeProperty object
   */
  function __construct( MaterialType $materialType=null,
                        $name = "",
                        $datatype = "",
                        $units = "",
                        $required = 0,
                        $options = "",
                        MeasurementUnitCategory $unitCategory = null)
  {
    $this->setMaterialType($materialType);
    $this->setDisplayName($name);
    $this->setDataType($datatype);
    $this->setUnits($units);
    $this->setRequired($required);
    $this->setOptions($options);
    $this->setMeasurementUnitCategory($unitCategory);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/MaterialTypeProperty/{$this->getId()}";
  }

  /**
   * Wrap {@link BaseMaterialTypeProperty::getDisplayName()}
   * Backward compatible to NEEScentral 1.7
   *
   * @return string
   */
  public function getName() {
    return $this->getDisplayName();
  }


  /**
   * Set the Display Name for this MaterialTypeProperty
   * Backward compatible to NEEScentral 1.7
   *
   * @param String $name
   */
  public function setName($name) {
    return $this->setDisplayName($name);
  }


  /**
   * Wrap {@link BaseMaterialTypeProperty::getMeasurementUnitCategory() }
   * Backward compatible to NEEScentral 1.7
   *
   * @return MeasurementUnitCategory
   */
  public function getUnitCategory() {
    return $this->getMeasurementUnitCategory();
  }


  /**
   * set the MeasurementUnitCategory for this MaterialTypeProperty
   * Backward compatible to NEEScentral 1.7
   *
   * @param MeasurementUnitCategory $unitCategory
   */
  public function setUnitCategory(MeasurementUnitCategory $unitCategory) {
    return $this->setMeasurementUnitCategory($unitCategory);
  }



} // MaterialTypeProperty
?>
