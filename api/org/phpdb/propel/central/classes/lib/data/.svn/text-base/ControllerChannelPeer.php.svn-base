<?php

// include base peer class
require_once 'lib/data/om/BaseControllerChannelPeer.php';

// include object class
include_once 'lib/data/ControllerChannel.php';


/**
 * ControllerChannelPeer
 *
 * Peer class for ControllerChannel
 * Contains static methods to operate on the ControllerChannel table
 *
 * @package    lib.data
 *
 */
class ControllerChannelPeer extends BaseControllerChannelPeer {

  /**
   * Find a ControllerChannel object based on its ID
   *
   * @param int $id
   * @return ControllerChannel
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all ControllerChannels
   *
   * @return array <ControllerChannel>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find all ControllerChannel that belongs to a ControllerConfig
   *
   * @param int $controllerConfigId
   * @return array <ControllerChannel>
   */
  public static function findByControllerConfig($controllerConfigId) {
    $c = new Criteria();
    $c->add(self::CONTROLLER_CONFIG_ID, $controllerConfigId);
    return self::doSelect($c);
  }

  /**
   * Find all ControllerChannel that belongs to a ControllerConfig with order by Name
   *
   * @param int $controllerConfigId
   * @return array[ControllerChannel]
   */
  public static function findOrderedChannels($controllerConfigId) {
    $c = new Criteria();
    $c->add(self::CONTROLLER_CONFIG_ID, $controllerConfigId);
    $c->addAscendingOrderByColumn(self::NAME);
    return self::doSelect($c);
  }


  /**
   * Find all ControllerChannels by a given $trialid
   * Note that there is a tree: TRIAL -> CONTROLLER_CONFIG -> CONTROLLER_CHANNEL
   *
   * @param int $trialid
   * @return array <ControllerChannel>
   */
  public static function findByTrial($trialid) {
    $c = new Criteria();
    $c->addJoin(self::CONTROLLER_CONFIG_ID, ControllerConfigPeer::ID);
    $c->add(ControllerConfigPeer::TRIAL_ID, $trialid);
    return self::doSelectJoinControllerConfig($c);

  }

} // ControllerChannelPeer
?>
