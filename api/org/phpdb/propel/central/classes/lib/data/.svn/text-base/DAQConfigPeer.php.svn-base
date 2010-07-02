<?php

  // include base peer class
  require_once 'lib/data/om/BaseDAQConfigPeer.php';

  // include object class
  include_once 'lib/data/DAQConfig.php';


/**
 * DAQConfigPeer
 *
 * @package    lib.data
 */
class DAQConfigPeer extends BaseDAQConfigPeer {

  /**
   * Find a DAQConfig object based on its ID
   *
   * @param int $id
   * @return DAQConfig
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all DAQConfigs
   *
   * @return array <DAQConfig>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  /**
   * Get a list of DAQConfig by TrialID
   *
   * @param int $trialId
   * @return array(DAQConfig)
   */
  public static function findByTrial($trialId) {
    $c = new Criteria();
    $c->add(self::TRIAL_ID, $trialId);
    return self::doSelect($c);

    //const FINDBYTRIAL_SQL    = "SELECT * FROM DAQConfig WHERE trialId=?";
  }
} // DAQConfigPeer
?>
