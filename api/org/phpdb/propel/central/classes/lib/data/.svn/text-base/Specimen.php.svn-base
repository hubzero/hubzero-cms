<?php

require_once 'lib/data/om/BaseSpecimen.php';


/**
 * Skeleton subclass for representing a row from the 'SPECIMEN' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class Specimen extends BaseSpecimen {



  /**
   * Each specimen is associated with a directory on disk. This function returns the path of that directory for this Specimen.
   *
   * @return String $path
   */
  public function getPathname() {
    return $this->getProject()->getPathname() . '/Specimen-' . $this->getId();
  }
  
} // Specimen
