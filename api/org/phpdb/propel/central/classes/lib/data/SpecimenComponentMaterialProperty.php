<?php

require_once 'lib/data/om/BaseSpecimenComponentMaterialProperty.php';


/**
 * Skeleton subclass for representing a row from the 'SPECCOMP_MATERIAL_PROPERTY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentMaterialProperty extends BaseSpecimenComponentMaterialProperty {

  /**
   * Wrap {@link BaseMaterialProperty::getMeasurementUnit}
   * Backward compatible to NEEScentral 1.7
   *
   * @return MeasurementUnit
   */
  public function getUnit() {
    return $this->getMeasurementUnit();
  }

} // SpecimenComponentMaterialProperty
