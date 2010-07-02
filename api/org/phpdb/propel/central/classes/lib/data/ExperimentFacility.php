<?php

require_once 'lib/data/om/BaseExperimentFacility.php';

/**
 * ExperimentFacility
 *
 * Each {@link Experiment} might involve zero or more {@link Faility}s participating in the test.
 * Conversely, each {@link Facility} may host a number of {@link Experiment}s.
 * ExperimentFacility is the M2M Join Table that captures this relationship
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses Facility
 *
 */
class ExperimentFacility extends BaseExperimentFacility {

  /**
   * Initializes internal state of ExperimentFacility object.
   */
  public function __construct(Experiment $experiment=null,
                              Facility $facility=null)
  {
    $this->setExperiment($experiment);
    $this->setFacility($facility);
  }


  /**
   * Set the Facility for this ExperimentFacility
   *
   * @param Facility $facility
   */
  public function setFacility($facility) {
    $this->setFacilityId($facility ? $facility->getId() : null);
  }


  /**
   * Get the Facility Object
   *
   * @return Facility Object
   */
  public function getFacility() {
    $facid = $this->getFacilityId();
    return is_null($facid) ? null : FacilityPeer::find($facid);
  }



  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/ExperimentFacility/{$this->getId()}";
  }

} // ExperimentFacility
?>
