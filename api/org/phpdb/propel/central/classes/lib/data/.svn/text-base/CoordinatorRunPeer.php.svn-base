<?php

  // include base peer class
  require_once 'lib/data/om/BaseCoordinatorRunPeer.php';

  // include object class
  include_once 'lib/data/CoordinatorRun.php';
  include_once 'lib/data/CoordinatorRunExperiment.php';


/**
 * Skeleton subclass for performing query and update operations on the 'COORDINATOR_RUN' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class CoordinatorRunPeer extends BaseCoordinatorRunPeer {

  /**
   * Find a CoordinatorRun object based on its ID
   *
   * @param int $id
   * @return CoordinatorRun
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all CoordinatorRuns
   *
   * @return array <CoordinatorRun>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }



  /**
   * File a list of CoordinatorRuns by CoordinatorId
   *
   * @param int $coordinatorId
   * @return array <CoordinatorRun>
   */
  public static function findByCoordinator($coordinatorId, $orderBy="") {

    if($orderBy != self::NAME && $orderBy != self::TITLE && $orderBy != self::START_DATE && $orderBy != self::END_DATE) $orderBy = self::ID;

    $c = new Criteria();
    $c->add(self::COORDINATOR_ID, $coordinatorId);
    $c->addAscendingOrderByColumn($orderBy);
    $c->setIgnoreCase(true);
    return self::doSelect($c);
  }


  /**
   * Get a list of CoordinatorRuns within Project
   *
   * @param int $projid
   * @return array <CoordinatorRun>
   */
  public static function findByProject($projid) {
    $c = new Criteria();
    $c->addJoin(CoordinatorPeer::ID, self::COORDINATOR_ID);
    $c->add(CoordinatorPeer::PROJID, $projid);
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  /**
   * File a specific CoordinatorRun by CoordinatorId and its Id
   *
   * @param int $coordinatorId
   * @param int $coordinatorRunId
   * @return CoordinatorRun
   */
  public static function findOneByIdAndCoordinator($coordinatorId, $coordinatorRunId) {
    $c = new Criteria();
    $c->add(self::COORDINATOR_ID, $coordinatorId);
    $c->add(self::ID, $coordinatorRunId);

    return self::doSelectOne($c);
  }

  /**
   * Find the CoordinatorRun that associated with Experiment
   *
   * @param int $expid
   * @return CoordinatorRun
   */
  public static function findOneBySubStructure($expid) {
    $c = new Criteria();
    $c->addJoin(self::ID, CoordinatorRunExperimentPeer::COORDINATOR_RUN_ID);
    $c->add(CoordinatorRunExperimentPeer::EXP_ID, $expid);

    return self::doSelectOne($c);
  }

  /**
   * Check if a name is duplicate to other coordinator-runs of a coordinator,
   * Ignore a CoordinatorRunId if provided
   *
   * @param String $name
   * @param int $coordinatorId
   * @param int $coordinatorRunId (optional)
   * @return boolean
   */
  public static function isDuplicateName($name, $coordinatorId, $coordinatorRunId=null) {

    $c = new Criteria();
    $c->add(self::COORDINATOR_ID, $coordinatorId);
    $c->add(self::NAME, $name);
    $c->setIgnoreCase(true);

    if($coordinatorRunId) {
      $c->add(self::ID, $coordinatorRunId, Criteria::NOT_EQUAL);
    }

    return self::doCount($c) > 0;
  }
} // CoordinatorRunPeer
