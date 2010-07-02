<?php

// include base peer class
require_once 'lib/data/om/BaseCoordinateSystemPeer.php';

// include object class
include_once 'lib/data/CoordinateSystem.php';


/**
 * CoordinateSystemPeer
 *
 * peer class for {@link CoordinateSystem}, contains static
 * methods for manipulating the CoordinateSystem table
 *
 * @package    lib.data
 */
class CoordinateSystemPeer extends BaseCoordinateSystemPeer {

  /**
   * Find a CoordinateSystem object based on its ID
   *
   * @param int $id
   * @return CoordinateSystem
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all CoordinateSystems
   *
   * @return array<CoordinateSystem>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }

  /**
   * Find one CoordinateSystem by name
   *
   * @param string $name
   * @return CoordinateSystem
   */
  public static function findByName($name) {
    $c = new Criteria();
    $c->add(self::NAME, $name);
    $c->setIgnoreCase(true);
    return self::doSelectOne($c);
  }

} // CoordinateSystemPeer
?>
