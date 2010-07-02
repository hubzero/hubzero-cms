<?php

require_once 'lib/data/om/BaseLoadType.php';


/**
 * Load Type
 *
 * Characterizes the kind of input motion to an experiment
 * Could be old...
 *
 * Input Motions, field called "recorder"/"station"
 * -- either upload an input motion or give us
 * the metadata about "El Centro Earthquake from the NE Direction
 *
 * LoadType domain object, a representation of the
 * LoadType db table which is basically a lookup table of the
 * various possible types of loads used in an Experiment
 *
 * @package    lib.data
 */
class LoadType extends BaseLoadType {

  /**
   * Initializes internal state of LoadType object.
   */
  function __construct($name = "")
  {
    $this->setName($name);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/LoadType/{$this->getId()}";
  }

} // LoadType
?>
