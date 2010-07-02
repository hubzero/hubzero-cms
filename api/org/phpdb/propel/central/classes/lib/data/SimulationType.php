<?php

require_once 'lib/data/om/BaseSimulationType.php';


/**
 * SimulationType
 *
 * Lookup table for {@link Simulation} types
 *
 *
 * @package    lib.data
 *
 */
class SimulationType extends BaseSimulationType {

  /**
   * Constructs a new SimulationType object
   *
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
    return "/SimulationType/{$this->getId()}";
  }


} // SimulationType
?>
