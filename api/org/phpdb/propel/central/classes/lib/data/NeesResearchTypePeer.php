<?php

  // include base peer class
  require_once 'lib/data/om/BaseNeesResearchTypePeer.php';

  // include object class
  include_once 'lib/data/NeesResearchType.php';


/**
 * Skeleton subclass for performing query and update operations on the 'NEES_RESEARCH_TYPE' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class NeesResearchTypePeer extends BaseNeesResearchTypePeer {

  /**
   * Find all none-deleted Project
   *
   * @return array <Project>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->add(self::ID, 0, Criteria::NOT_EQUAL);
    $c->addAscendingOrderByColumn(self::SYSTEM_NAME);

    return self::doSelect($c);
  }

} // NeesResearchTypePeer
