<?php

// include base peer class
require_once 'lib/data/om/BaseDAQChannelEquipmentPeer.php';

// include object class
include_once 'lib/data/DAQChannelEquipment.php';


/**
 * DAQChannelEquipmentPeer
 *
 * Peer class for DAQChannelEquipment
 * Contains static methods for manipulating the DAQChannelEquipment Table
 *
 * @package    lib.data
 */
class DAQChannelEquipmentPeer extends BaseDAQChannelEquipmentPeer {

  /**
   * Find a DAQChannelEquipment object based on its ID
   *
   * @param int $id
   * @return DAQChannelEquipment
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all DAQChannelEquipments
   *
   * @return array<DAQChannelEquipment>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find all DAQChannelEquipments for a given DAQChannel
   *
   * @param int $DAQChannelId
   * @return array<DAQChannelEquipment>
   *
   * was:SELECT * FROM DAQChannelEquipment WHERE DAQChannelId=?
   */
  public static function findByDAQChannel($DAQChannelId) {
    $c = new Criteria();
    $c->add(self::DAQCHANNEL_ID, $DAQChannelId);
    return self::doSelect($c);
  }

} // DAQChannelEquipmentPeer
?>
