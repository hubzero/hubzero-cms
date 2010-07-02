<?php

// include base peer class
require_once 'lib/data/om/BaseAttributePeer.php';

// include object class
include_once 'lib/data/Attribute.php';


/**
 * AttributePeer
 *
 * Peer class for Attribute
 * Contains static methods to operate on the Attribute table
 *
 * @package    lib.data
 */
class AttributePeer extends BaseAttributePeer {

  /**
   * Find an Attribute object based on its ID
   *
   * @param int $id
   * @return Attribute
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all Attribute
   *
   * @return array <Attribute>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }
} // AttributePeer
?>
