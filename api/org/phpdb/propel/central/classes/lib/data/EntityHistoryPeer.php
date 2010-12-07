<?php

  // include base peer class
  require_once 'lib/data/om/BaseEntityHistoryPeer.php';

  // include object class
  include_once 'lib/data/EntityHistory.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ENTITY_HISTORY' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class EntityHistoryPeer extends BaseEntityHistoryPeer {

  /**
   * Finds a given entity by type
   * @param int $p_iEntityId
   * @param int $p_iEntityTypeId
   * @return EntityHistory
   */
  public static function findByEntity($p_iEntityId, $p_iEntityTypeId, $p_strAction=null){
    $c = new Criteria();
    $c->add(self::ENTITY_ID, $p_iEntityId);
    $c->add(self::ENTITY_TYPE_ID, $p_iEntityTypeId);
    if($p_strAction){
      $c->add(self::ACTION, $p_strAction);
    }
    return self::doSelect($c);
  }

} // EntityHistoryPeer
