<?php

require_once 'lib/data/Experiment.php';
require_once 'lib/data/ExperimentDomain.php';
require_once 'lib/data/TrialPeer.php';

require_once 'lib/data/om/BaseExperiment.php';


/**
 * Simulation
 *
 * Subclass of Experiment
 *
 *
 * @package    lib.data
 * @uses Experiment
 * @uses ExperimentDomain
 * @uses Trial
 */
class Simulation extends Experiment {

  /**
   * Constructs a new Simulation class,
   *  setting the exp_type_id column to ExperimentPeer::CLASSKEY_SIMULATION.
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
    parent::__construct($project,
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

    $this->setExperimentTypeId(ExperimentPeer::CLASSKEY_SIMULATION);

    // Extra work, check to make sure we are creating a new simulation
    if(!$domain || $domain->getSystemName() != 'simulation') {
      $domain = ExperimentDomainPeer::findSimulationDomain();
      $this->setExperimentDomain($domain);
    }
  }



  /**
   * Return the Runs associated with this Simulation
   * Note:  To get all the children SimulationRuns we can call $this->getTrials()
   *        However, $this->getTrials() from base class returned all SimulationRuns
   *        included any that marked with 'deleted' in this Simulation)
   *
   * @return array <SimulationRun>
   */
  public function getSimulationRuns() {
    return TrialPeer::findSimulationRuns($this->getId());
  }


  /**
   * Get a SimulationRun belong to this Simulation given by Run Name
   *
   * @param String $runName_or_id
   * @return SimulationRun
   */
  function getSimulationRun($runName_or_id) {
    return TrialPeer::findRunBySimulationAndNameId($this->getId(), $runName_or_id);
  }


  /**
   * Add a SimulationRun to this Simulation
   *
   * @param SimulationRun $run
   */
  function addSimulationRun(SimulationRun $run ) {

    if ($run) {
      $this->collTrials[] = $run;
      $run->setSimulation( $this );
    }
  }



  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $proj = $this->getProject();
    return $proj->getRESTURI() . "/Simulation/{$this->getId()}";
  }

} // Simulation
