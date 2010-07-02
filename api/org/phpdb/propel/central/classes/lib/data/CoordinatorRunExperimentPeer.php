<?php

  // include base peer class
  require_once 'lib/data/om/BaseCoordinatorRunExperimentPeer.php';

  // include object class
  include_once 'lib/data/CoordinatorRunExperiment.php';


/**
 * Skeleton subclass for performing query and update operations on the 'COORDINATOR_RUN_EXPERIMENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class CoordinatorRunExperimentPeer extends BaseCoordinatorRunExperimentPeer {

  /**
   * Find a CoordinatorRunExperiment object based on its ID
   *
   * @param int $id
   * @return CoordinatorRunExperiment
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all CoordinatorRunExperiments
   *
   * @return array<CoordinatorRunExperiment>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  /**
   * Find a list of CoordinatorRunExperiment objects by a coordinator
   *
   * @param int $coordinatorId
   * @return array <CoordinatorRunExperiment>
   */
  public static function findByCoordinator($coordinatorId) {
    $c = new Criteria();
    $c->addJoin(self::EXP_ID, ExperimentPeer::EXPID);
    $c->addJoin(self::COORDINATOR_RUN_ID, CoordinatorRunPeer::ID);
    $c->addJoin(CoordinatorPeer::ID, CoordinatorRunPeer::COORDINATOR_ID);
    $c->add(CoordinatorPeer::ID, $coordinatorId);
    $c->add(ExperimentPeer::DELETED, 0);
    $c->addAscendingOrderByColumn(self::COORDINATOR_RUN_ID);
    $c->addAscendingOrderByColumn(self::EXP_ID);

    return self::doSelect($c);
  }


  /**
   * Find a list of CoordinatorRunExperiment objects by a coordinator-Run
   *
   * @param int $coordinatorRunId
   * @return array <CoordinatorRunExperiment>
   */
  public static function findByCoordinatorRun($coordinatorRunId, $orderBy=ExperimentPeer::EXPID) {
    $c = new Criteria();
    $c->addJoin(self::EXP_ID, ExperimentPeer::EXPID);
    $c->add(self::COORDINATOR_RUN_ID, $coordinatorRunId);
    $c->add(ExperimentPeer::DELETED, 0);
    $c->addAscendingOrderByColumn($orderBy);

    return self::doSelect($c);
  }


  /**
   * Find one CoordinatorRunExperiment object by substructure
   *
   * @param int $expid
   * @return CoordinatorRunExperiment
   */
  public static function findBySubstructure($expid) {
    $c = new Criteria();
    $c->addJoin(self::EXP_ID, ExperimentPeer::EXPID);
    $c->add(self::EXP_ID, $expid);
    $c->add(ExperimentPeer::DELETED, 0);

    return self::doSelectOne($c);
  }


  /**
   * Find a list of CoordinatorRunExperiment type Physiscal objects by a coordinator-Run
   *
   * @param int $coordinatorRunId
   * @param String $orderBy
   * @return array <CoordinatorRunExperiment>
   */
  public static function findByCoordinatorRunAndPhysicalType($coordinatorRunId, $orderBy="") {
    if(empty($orderBy)) $orderBy = ExperimentPeer::EXPID;

    $c = new Criteria();
    $c->addJoin(self::EXP_ID, ExperimentPeer::EXPID);
    $c->add(ExperimentPeer::EXP_TYPE_ID, ExperimentPeer::CLASSKEY_STRUCTUREDEXPERIMENT);
    $c->add(self::COORDINATOR_RUN_ID, $coordinatorRunId);
    $c->add(ExperimentPeer::DELETED, 0);
    $c->addAscendingOrderByColumn($orderBy);

    return self::doSelect($c);
  }


  /**
   * Find a list of CoordinatorRunExperiment type Analytical objects by a coordinator-Run
   *
   * @param int $coordinatorRunId
   * @param String $orderBy
   * @return array <CoordinatorRunExperiment>
   */
  public static function findByCoordinatorRunAndAnalyticalType($coordinatorRunId, $orderBy="") {
    if(empty($orderBy)) $orderBy = ExperimentPeer::EXPID;

    $c = new Criteria();
    $c->addJoin(self::EXP_ID, ExperimentPeer::EXPID);
    $c->add(ExperimentPeer::EXP_TYPE_ID, ExperimentPeer::CLASSKEY_SIMULATION);
    $c->add(self::COORDINATOR_RUN_ID, $coordinatorRunId);
    $c->add(ExperimentPeer::DELETED, 0);
    $c->addAscendingOrderByColumn($orderBy);

    return self::doSelect($c);
  }

} // CoordinatorRunExperimentPeer
