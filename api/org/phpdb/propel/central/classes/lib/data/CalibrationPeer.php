<?php

// include base peer class
require_once 'lib/data/om/BaseCalibrationPeer.php';

// include object class
include_once 'lib/data/Calibration.php';


/**
 * CalibrationPeer
 *
 * Peer class for Calibration
 * Contains static methods to operate on the Calibration table
 *
 * @package    lib.data
 *
 */
class CalibrationPeer extends BaseCalibrationPeer {

  /**
   * Find a Calibration object based on its ID
   *
   * @param int $id
   * @return Calibration
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all Calibrations
   *
   * @return array <Calibration>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find list of Calibrations by a sensorId
   *
   * @param int $sensorId
   * @return array <Calibration>
   */
  public static function findBySensor($sensorId) {
    $c = new Criteria();
    $c->add(self::SENSOR_ID, $sensorId);
    return self::doSelect($c);

    //return new Finder($finderName, "SELECT * FROM Calibration WHERE sensorId=?");
  }

  /**
   * Find an unique Calibrations by a sensorId and Calibration Date
   *
   * @param int $sensorId
   * @param String $calibDate
   * @return array <Calibration>
   */
  public static function findBySensorAndDate($sensorId, $calibDate) {
    $c = new Criteria();
    $c->add(self::SENSOR_ID, $sensorId);
    $c->add(self::CALIB_DATE, $calibDate);
    return self::doSelectOne($c);

    //return new Finder($finderName, "SELECT * FROM Calibration WHERE sensorId=?");
  }


  /**
   * Find one calibration by its id and its sensorId
   *
   * @param int $sensorId
   * @param int $id
   * @return Calibration
   */
  public static function findBySensorAndId($sensorId, $calid) {
    $c = new Criteria();
    $c->add(self::SENSOR_ID, $sensorId);
    $c->add(self::CALIB_ID, $calid);
    return self::doSelectOne($c);
  }


  /**
   * Find all Calibration done by Facility
   *
   * @param $facid
   * @return array(Calibration)
   */
  public static function findByFacility($facid) {

    $c = new Criteria();
    $c->addJoin(self::SENSOR_ID, SensorPeer::SENSOR_ID);
    $c->addJoin(SensorSensorManifestPeer::SENSOR_ID, SensorPeer::SENSOR_ID);
    $c->addJoin(SensorSensorManifestPeer::MANIFEST_ID, SensorManifestPeer::ID);
    $c->addJoin(OrganizationPeer::SENSOR_MANIFEST_ID, SensorManifestPeer::ID);
    $c->add(OrganizationPeer::FACILITYID, $facid);
    $c->add(SensorPeer::DELETED, 0);
    $c->add(self::DELETED, 0);
    $c->addAscendingOrderByColumn(SensorPeer::SENSOR_MODEL_ID);
    $c->addAscendingOrderByColumn(self::SENSOR_ID);
    $c->addAscendingOrderByColumn(self::CALIB_DATE);

    return self::doSelect($c);
  }

} // CalibrationPeer
?>
