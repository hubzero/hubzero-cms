<?php

  // include base peer class
  require_once 'lib/data/om/BaseCoordinatorPeer.php';

  // include object class
  include_once 'lib/data/Coordinator.php';


/**
 * Skeleton subclass for performing query and update operations on the 'COORDINATOR' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class CoordinatorPeer extends BaseCoordinatorPeer {

  /**
   * Find a Coordinator object based on its ID
   *
   * @param int $id
   * @return Coordinator
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all Coordinators
   *
   * @return array <Coordinator>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  /**
   * Find a unique Coordinator for a project.
   *
   * @param int $projid
   * @return Coordinator
   */
  public static function findByProject($projid) {

    $c = new Criteria();
    $c->add(self::PROJID, $projid);
    return self::doSelectOne($c);
  }


  /**
   * Find an array map for projid->coordinatorId.
   *
   * @return array(projid->coordinatorId)
   */
  public static function getProjectCoordinatorMap() {
    $coordinators = self::findAll();

    $map = array();
    foreach($coordinators as $coordinator) {
      $map[$coordinator->getProjectId()] = $coordinator->getId();
    }

    return $map;
  }


} // CoordinatorPeer
