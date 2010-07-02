<?php

require_once 'lib/data/om/BaseLocationPlan.php';


/**
 * LocationPlan
 *
 * Base Class for {@link SensorLocationPlan} and {@link SourceLocationPlan}
 *
 * "Location plans" are basically named lists of locations
 *  for sensors our sources
 *
 * @package    lib.data
 */
abstract class LocationPlan extends BaseLocationPlan {

  /**
   * Construct an LocationPlan Object
   *
   * @param Experiment $experiment
   * @param String $name
   */
  public function __construct($experiment,
                              $name = null)
  {
    $this->setExperiment($experiment);
    $this->setName($name);
  }
} // LocationPlan
