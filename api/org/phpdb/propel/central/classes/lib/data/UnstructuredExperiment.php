<?php

require_once 'lib/data/Experiment.php';
require_once 'lib/data/om/BaseExperiment.php';


/**
 * UnstructuredExperiment
 *
 * @todo describe this
 *
 * @package    lib.data
 */
class UnstructuredExperiment extends Experiment {

  /**
   * Constructs a new UnstructuredExperiment class,
   * setting the exp_type_id column to
   * ExperimentPeer::CLASSKEY_UNSTRUCTUREDEXPERIMENT
   *
   */

  function __construct( Project $project = null,
                        $title = "",
                        $objective = "",
                        $description = "",
                        $startDate = null,
                        $endDate = null,
                        $status = "unpublished",
                        $view = "MEMBERS",
                        ExperimentDomain $domain = null,
                        $curationstatus="Uncurated",
                        $deleted = 0)
  {
    parent::__construct(
      $project,
      $title,
      $objective,
      $description,
      $startDate,
      $endDate,
      $status,
      $view,
      $domain,
      $curationstatus,
      $deleted
    );

    $this->setExperimentTypeId(ExperimentPeer::CLASSKEY_UNSTRUCTUREDEXPERIMENT);
  }

  /**
   * Return FALSE indicating this is an
   * unstructured experiment
   *
   * @return boolean false
   */
  public function getStructure() {
    return FALSE;
  }

} // UnstructuredExperiment
