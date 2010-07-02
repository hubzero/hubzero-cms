<?php

require_once 'lib/data/LocationPlan.php';
require_once 'lib/data/om/BaseLocationPlan.php';


/**
 * SourceLocationPlan
 *
 * A SourceLocationPlan is a named set of source locations for a given experiment.
 *
 * fields:
 * experiment -- Experiment, the experiment that owns the SLP
 * name -- String, Human-readable name for the SLP
 * sourceLocations -- Collection of source locations
 *
 * @package    lib.data
 *
 * @uses Experiment
 *
 */
class SourceLocationPlan extends LocationPlan {

  /**
   * Constructs a new SourceLocationPlan class,
   * setting the plan_type_id column to LocationPlanPeer::CLASSKEY_2.
   */

  public function __construct($experiment=null,
                              $name="",
                              $sourceLocations=null)
  {
    parent::__construct( $experiment, $name);

    $this->setPlanTypeId(LocationPlanPeer::CLASSKEY_SOURCELOCATIONPLAN);
    $this->setSourceLocations($sourceLocations);
  }

  public function setSourceLocations($l) {
    if(is_null($l)) $l = array();
    $this->collLocations = $l;
  }


  public function getSourceLocations() {
    return $this->getLocations();
  }


  public function removeLocation($loc) {
    $this->removeSourceLocation($loc);
  }



  public function addSourceLocation(SourceLocation $sourceLocation) {
    $this->addLocation($sourceLocation);
  }


  public function removeSourceLocation(SourceLocation $sourceLocation) {

    if(is_null($sourceLocation) || is_null($sourceLocation->getId())) return;

  	$sourceLocations = $this->getSourceLocations();
  	$newSourceLocations = array();

  	foreach($sourceLocations as $sl) {
  	  if($sourceLocation->getId() != $sl->getId()) {
        $newSourceLocations[] = $sl;
  	  }
  	}

  	$this->collLocations = $newSourceLocations;
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $exp = $this->getExperiment();
    return $exp->getRESTURI() . "/SourceLocationPlan/{$this->getId()}";
  }
} // SourceLocationPlan

?>
