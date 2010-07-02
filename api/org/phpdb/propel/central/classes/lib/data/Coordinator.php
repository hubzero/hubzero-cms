<?php

require_once 'lib/data/om/BaseCoordinator.php';


/**
 * Skeleton subclass for representing a row from the 'COORDINATOR' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class Coordinator extends BaseCoordinator {

  /**
   * Each Coordinator is associated with a directory on disk. This function returns the path of that directory for this Coordinator.
   *
   * @return String $path
   */
  public function getPathname() {
    return $this->getProject()->getPathname() . '/Coordinator-' . $this->getId();
  }

  /**
   * Get the participating Facility
   *
   * @return Facility
   */
  public function getFacility() {
    return $this->getOrganization();
  }

} // Coordinator
