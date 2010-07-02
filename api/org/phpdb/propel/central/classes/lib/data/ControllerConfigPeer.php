<?php

// include base peer class
require_once 'lib/data/om/BaseControllerConfigPeer.php';

// include object class
include_once 'lib/data/ControllerConfig.php';


/**
 * ControllerConfigPeer
 *
 * Peer class for ControllerConfig
 * Contains static methods to operate on the ControllerConfig table
 *
 * @package    lib.data
 *
 */
class ControllerConfigPeer extends BaseControllerConfigPeer {

  /**
   * Find a ControllerConfig object based on its ID
   *
   * @param int $id
   * @return ControllerConfig
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Return an array containing all ControllerConfigs
   *
   * @return array<ConstrollerConfig>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find controller configs for a given TrialID
   *
   * @param int trialid
   * @return array<ControllerConfig>
   *
   * was:SELECT * FROM ControllerConfig WHERE trialId=?
   */
  public static function findByTrial($trialid) {
    $c = new Criteria();
    $c->add(self::TRIAL_ID, $trialid);
    return self::doSelect($c);
  }

} // ControllerConfigPeer
?>
