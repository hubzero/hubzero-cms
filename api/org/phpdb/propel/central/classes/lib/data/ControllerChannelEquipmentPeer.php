<?php

// include base peer class
require_once 'lib/data/om/BaseControllerChannelEquipmentPeer.php';

// include object class
include_once 'lib/data/ControllerChannelEquipment.php';


/**
 * ControllerChannelEquipmentPeer
 *
 * Peer class for ControllerChannelEquipment
 * Contains static methods to operate on the ControllerChannelEquipment table
 *
 * @package    lib.data
 *
 */
class ControllerChannelEquipmentPeer extends BaseControllerChannelEquipmentPeer {

  /**
   * Find a ControllerChannelEquipment object based on its ID
   *
   * @param int $id
   * @return ControllerChannelEquipment
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all ControllerChannelEquipments
   *
   * @return array <ControllerChannelEquipment>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * find a list of ControllerChannelEquipment by channelId
   *
   * @param int $controllerChannelId
   * @return array <ControllerChannelEquipment>
   */
  public static function findByControllerChannel($controllerChannelId) {
    $c = new Criteria();
    $c->add(self::CONTROLLER_CHANNEL_ID, $controllerChannelId);
    return self::doSelect($c);

  }

  public static function findControllerChannelEquipment(ControllerChannel $cc, Equipment $e) {
    $c = new Criteria();
    $c->add(self::CONTROLLER_CHANNEL_ID, $cc->getId());
    $c->add(self::EQUIPMENT_ID, $e->getId());

    return self::doSelect($c);
  }

  public static function countControllerChannelEquipment(ControllerChannel $cc, Equipment $e) {
    $c = new Criteria();
    $c->add(self::CONTROLLER_CHANNEL_ID, $cc->getId());
    $c->add(self::EQUIPMENT_ID, $e->getId());

    return self::doCount($c, true);
  }

} // ControllerChannelEquipmentPeer
?>
