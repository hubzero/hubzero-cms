<?php

require_once 'lib/data/om/BaseExperimentOrganization.php';


/**
 * ExperimentOrganization
 *
 * Each {@link Experiment} might have a number of {@link Organization}s participating in the test.
 * Conversely, each {@link Organization} participates in a number of {@link Experiment}s.
 * ExperimentOrganization is the M2M Join Table that captures this relationship
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses Organization
 *
 */
class ExperimentOrganization extends BaseExperimentOrganization {

  /**
   * Initializes internal state of ExperimentOrganization object.
   */
  public function __construct(Experiment $experiment=null,
                              Organization $organization=null)
  {
    $this->setExperiment($experiment);
    $this->setOrganization($organization);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/ExperimentOrganization/{$this->getId()}";
  }

} // ExperimentOrganization
?>
