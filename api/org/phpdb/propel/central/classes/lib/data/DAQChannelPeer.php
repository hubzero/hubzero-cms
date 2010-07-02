<?php

// include base peer class
require_once 'lib/data/om/BaseDAQChannelPeer.php';

// include object class
include_once 'lib/data/DAQChannel.php';


/**
 *  DAQChannelPeer
 *
 * Peer class for DAQChannel
 * Defines static methods for manipulating the DAQChannel Table
 *
 * @package    lib.data
 */
class DAQChannelPeer extends BaseDAQChannelPeer {


  /**
   * Find a DAQChannel object based on its ID
   *
   * @param int $id
   * @return DAQChannel
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all DAQChannels
   *
   * @return array<DAQChannel>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find DAQChannels for a given DAQConfig
   *
   * @param int $DAQConfigId
   * @return Array of DAQChannel Object
   *
   */
  public static function findByDAQConfig($DAQConfigId) {
    $c = new Criteria();
    $c->add(self::DAQCONFIG_ID, $DAQConfigId);
    return self::doSelect($c);
  }


  /**
   * Find DAQChannels for a given DAQConfig, ordered by (Channel,SensorLocation)
   *
   * @param int $DAQConfigId
   * @return array<DAQChannel> ordered by (ChannelOrder,SensorLocationId)
   *
   */
  public static function findOrderedChannels($DAQConfigId) {
    $c = new Criteria();
    $c->add(self::DAQCONFIG_ID, $DAQConfigId);
    $c->addAscendingOrderByColumn(self::CHANNEL_ORDER);
    $c->addAscendingOrderByColumn(self::SENSOR_LOCATION_ID);
    return self::doSelect($c);
  }


  /**
   * Find a list of DAQChannels by DAQConfigId, ChannelOrder and Label
   *
   * @param int $DAQConfigId
   * @param int $ChannelOrder
   * @param String $label
   * @return array <DAQChannel>
   */
  public static function findByDAQConfigOrderLabel($DAQConfigId, $ChannelOrder, $label) {
    $sql = "SELECT D.* FROM DAQChannel D
              JOIN SensorLocation SL ON SL.id = D.sensorLocationId
              JOIN Location L ON L.id = SL.LocationId
            WHERE D.DAQConfigId = ?
              AND D.ChannelOrder = ?
              AND L.label = ?";

    $conn = Propel::getConnection(self::DATABASE_NAME);
    $stmt = $conn->prepareStatement($sql);
    $stmt->setInt(1, $DAQConfigId);
    $stmt->setInt(2, $ChannelOrder);
    $stmt->setString(3, $label);
    $rs = $stmt->executeQuery();

    return self::populateObjects($rs);
  }

  /**
   * Find all DAQChannels by a given $trialid
   * Note that there is a tree: TRIAL -> DAQCONFIG -> DAQCHANNEL
   *
   * @param int $trialid
   * @return array <DAQChannel>
   */
  public static function findByTrial($trialid) {
    $c = new Criteria();
    $c->addJoin(self::DAQCONFIG_ID, DAQConfigPeer::ID);
    $c->add(DAQConfigPeer::TRIAL_ID, $trialid);
    return self::doSelectJoinDAQConfig($c);
  }


  public static function isChannelOrderExistsInChannelList($daqConfigId, $channelOrder) {
    $c = new Criteria();
    $c->add(self::DAQCONFIG_ID, $daqConfigId);
    $c->add(self::CHANNEL_ORDER, $channelOrder);
    return self::doCount($c) == 1 ? true:false;
  }

} // DAQChannelPeer
?>
