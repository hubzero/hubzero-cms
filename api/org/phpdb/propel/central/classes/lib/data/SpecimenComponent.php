<?php

require_once 'lib/data/om/BaseSpecimenComponent.php';


/**
 * Skeleton subclass for representing a row from the 'SPECIMEN_COMPONENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponent extends BaseSpecimenComponent {

  /**
   * Get a list of sub-components by a this SpecimenComponent
   *
   * @param array <SpecimenComponent>
   */
  public function findSubComponents() {
    return SpecimenComponentPeer::findSubComponents($this->getId());
  }


  public function isParent() {
    return SpecimenComponentPeer::countSubComponent($this->getId()) > 0;
  }


  /**
   * Get parent Component
   *
   * @return SpecimenComponent
   */
  public function getParent() {
    return $this->getSpecimenComponentRelatedByParentId();
  }


  /**
   * Each specimen component is associated with a directory on disk. This function returns the path of that directory for this SpecimenComponent.
   *
   * @return String $path
   */
  public function getPathname() {
    return $this->getSpecimen()->getPathname() . '/Component-' . $this->getId();
  }

} // SpecimenComponent
