<?php

require_once 'lib/data/Experiment.php';

require_once 'lib/data/om/BaseExperiment.php';


/**
 * Structured Experiment
 *
 * @todo document this class
 *
 * @package    lib.data
 */
class StructuredExperiment extends Experiment {

  /**
   * Constructs a new StructuredExperiment object,
   * setting the exp_type_id column to
   * ExperimentPeer::CLASSKEY_STRUCTUREDEXPERIMENT.
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

    $this->setExperimentTypeId(ExperimentPeer::CLASSKEY_STRUCTUREDEXPERIMENT);
  }
} // StructuredExperiment
