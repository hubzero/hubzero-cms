<?php

  // include base peer class
  require_once 'lib/data/om/BaseNeesAwardTypePeer.php';

  // include object class
  include_once 'lib/data/NeesAwardType.php';


/**
 * Skeleton subclass for performing query and update operations on the 'NEES_AWARD_TYPE' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class NeesAwardTypePeer extends BaseNeesAwardTypePeer {

  /**
   * Find a NeesAwardType object based on its ID
   *
   * @param int $id
   * @return NeesAwardType
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all NeesAwardType
   *
   * @return array <NeesAwardType>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

} // NeesAwardTypePeer
