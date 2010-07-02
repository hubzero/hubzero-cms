<?php

// include base peer class
require_once 'lib/data/om/BaseCoordinateDimensionPeer.php';

// include object class
include_once 'lib/data/CoordinateDimension.php';


/**
 * CoordinateDimensionPeer
 *
 * CoordinateDimension peer class, contains static methods
 * for managing the CoordinateDimension Table
 *
 * @package    lib.data
 */
class CoordinateDimensionPeer extends BaseCoordinateDimensionPeer {

  /**
   * Find a CoordinateDimension object based on its ID
   *
   * @param int $id
   * @return CoordinateDimension
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Return an array of all Coordinate Dimensions
   *
   * @return array<CoordinateDimension>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  /**
   * Find a coordinate dimension by name
   *
   * @param string $name
   * @return array<CoordinateDimension>
   *
   */
  public static function findByName($name) {
    $c = new Criteria();
    $c->add(self::NAME, $name);
    $c->setIgnoreCase(true);
    return self::doSelect($c);
  }

} // CoordinateDimensionPeer
?>
