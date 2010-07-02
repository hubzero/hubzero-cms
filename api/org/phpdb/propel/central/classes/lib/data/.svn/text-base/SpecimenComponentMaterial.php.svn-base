<?php

require_once 'lib/data/om/BaseSpecimenComponentMaterial.php';


/**
 * Skeleton subclass for representing a row from the 'SPECIMEN_COMPONENT_MATERIAL' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentMaterial extends BaseSpecimenComponentMaterial {

  /**
   * Set the PrototypeMaterial for this Material
   *
   * @param SpecimenComponentMaterial $prototype
   */
  public function setPrototype($prototype) {
    return $this->setSpecimenComponentMaterialRelatedByPrototypeMaterialId($prototype);
  }


  /**
   * Get the material basis of this material
   *
   * @return Material
   */
  public function getPrototype() {
    return $this->getSpecimenComponentMaterialRelatedByPrototypeMaterialId();
  }


  /**
   * Get the list of SpecimenComponentMaterialProperty
   *
   * @return array<SpecimenComponentMaterialProperty>
   */
  public function getMaterialProperties() {
    return $this->getSpecimenComponentMaterialPropertys();
  }


  /**
   * Set the collection of MaterialProperties for this Material
   *
   * @param $props: array <MaterialProperty>
   */
  public function setMaterialProperties($props) {
    if(is_null($props)) $props = array();
    $this->collSpecimenComponentMaterialPropertys = $props;
  }


  /**
   * Get the material files for this Material
   *
   * @return array <MaterialFile>
   */
  public function getFiles() {
    return $this->getSpecimenComponentMaterialFiles();
  }


  /**
   * Each Material is associated with a directory on disk.
   * This function returns the path of that directory for
   * this Material.
   *
   */
  public function getPathname() {
    return $this->getSpecimenComponent()->getPathname() . "/" . "Material" . $this->getId();
  }
} // SpecimenComponentMaterial
