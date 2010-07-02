<?php

// include base peer class
require_once 'lib/data/om/BaseCoordinateSpacePeer.php';

// include object class
include_once 'lib/data/CoordinateSpace.php';


/**
 * CoordinateSpacePeer
 *
 * peer class for CoordinateSpace: contains static methods
 * for managing ehte CoordinateSpace table
 *
 * @package    lib.data
 */
class CoordinateSpacePeer extends BaseCoordinateSpacePeer {

  /**
   * Find a CoordinateSpace object based on its ID
   *
   * @param int $id
   * @return CoordinateSpace
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all CoordinateSpaces
   *
   * @return array<CoordinateSpace>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find CoordinateSpaces for a given Experiment
   *
   * @param int $expid
   * @return array<CoordinateSpace>
   *
   */
  public static function findByExperiment($expid) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

  /**
   * Find CoordinateSpaces by a given Name
   *
   * @param String $name
   * @return CoordinateSpace
   *
   * @todo: This is not quite right, because CoordinateSpace Name is not unique ! Use findByExperimentAndName instead
   */
  public static function findByName($name) {
    $c = new Criteria();
    $c->add(self::NAME, $name);
    $c->setIgnoreCase(true);
    return self::doSelectOne($c);
  }


  /**
   * Find all CoordinateSpaces in an Experiment that is not Global CoordinateSpace
   *
   * @param int $expid
   * @return array<CoordinateSpace>
   */
  public static function findNotGlobalByExperiment($expid) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);
    $c->add(self::PARENT_ID, null, Criteria::ISNOTNULL);

    return self::doSelect($c);
  }

  /**
   * Find the parent top Global CoordinateSpaces in an Experiment
   *
   * @param int $expid
   * @return CoordinateSpace $global
   */
  public static function findGlobalCoordinateSpace($expid) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);
    $c->add(self::PARENT_ID, null, Criteria::ISNULL);

    return self::doSelectOne($c);
  }

  /**
   * Find CoordinateSpace within an experiment and a given Name
   *
   * @param int $expid: Experiment ID
   * @param String $name: Name of the CoordinateSpace to look at
   * @return CoordinateSpace
   */

  public static function findByExperimentAndName($expid, $name) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);
    $c->add(self::NAME, $name);
    return self::doSelectOne($c);

  }
} // CoordinateSpacePeer
?>
