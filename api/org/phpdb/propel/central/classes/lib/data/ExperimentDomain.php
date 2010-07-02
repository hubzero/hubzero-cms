<?php

require_once 'lib/data/om/BaseExperimentDomain.php';

/**
 * Experiment Domain
 *
 * E.g., shake table test, centrifuge, field test, etc.
 *
 * Should impact the similitude laws and other aspects of the data model.
 *
 * Helps with searching
 *
 *
 * @package    lib.data
 */
class ExperimentDomain extends BaseExperimentDomain {

  /**
   * Initializes internal state of ExperimentDomain object.
   */
  function __construct($name = "",
                       $system_name = null,
                       $description = null)
  {
    $this->setDisplayName($name);
    $this->setSystemName($system_name);
    $this->setDescription($description);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/ExperimentDomain/{$this->getId()}";
  }

  /**
   * Get the Display Name of this Experiment Domain...
   * Backward compatible to NEEScentral 1.7
   *
   * String $name
   */
  public function getName() {
    return $this->getDisplayName();
  }

  /**
   * Set the Name of this Experiment Domain
   * Backward compatible to NEEScentral 1.7
   *
   * @param String $name
   */
  public function setName($name) {
    return $this->setDisplayName($name);
  }

} // ExperimentDomain
?>
