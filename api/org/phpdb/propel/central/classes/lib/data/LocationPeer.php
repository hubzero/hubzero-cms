<?php

// include base peer class
require_once 'lib/data/om/BaseLocationPeer.php';

// include object class
include_once 'lib/data/Location.php';


/**
 * LocationPeer
 *
 * Peer class for {@link Location}
 * Static methods for accessing the Location table
 *
 * Location table rows may be Locations, SensorLocations, or SourceLocations
 * based on the location_type_id field.
 *
 * @package    lib.data
 */
class LocationPeer extends BaseLocationPeer {

  /**
   * Find a Location by ID
   *
   * @param int $id
   * @return Location
   */
  /*
  public static function find($id) {
  return self::retrieveByPK($id);
  }
  */
  /**
   * Find a {@link SourceLocation} by ID
   *
   * @param int $id
   * @return SourceLocation
   */
  public static function findSourceLocationById($id) {
    $c = new Criteria();
    $c->add(self::ID, $id);
    $c->add(self::LOCATION_TYPE_ID, self::CLASSKEY_SOURCELOCATION);

    return self::doSelectOne($c);
  }


  /**
   * Find a {@link SensorLocation} by ID
   *
   * @param int $id
   * @return SensorLocation
   */
  public static function findSensorLocationById($id) {
    $c = new Criteria();
    $c->add(self::ID, $id);
    $c->add(self::LOCATION_TYPE_ID, self::CLASSKEY_SENSORLOCATION);

    return self::doSelectOne($c);
  }



  /**
   * Find all locations
   *
   * @return array<Location>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find all {@link SourceLocation}s
   *
   * @return array<SourceLocation>
   */
  public static function findAllSourceLocations() {
    $c = new Criteria();
    $c->add(self::LOCATION_TYPE_ID, self::CLASSKEY_SOURCELOCATION);

    return self::doSelect($c);
  }


  /**
   * Find all {@link SourceLocation}s
   *
   * @return array<SourceLocation>
   */
  public static function findAllSensorLocations() {
    $c = new Criteria();
    $c->add(self::LOCATION_TYPE_ID, self::CLASSKEY_SENSORLOCATION);
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  /*
  "find" => array("
  SELECT SL.LocationId AS id, SL.planId, SL.sensorTypeId, L.label,
  L.x, L.y, L.z, L.i, L.j, L.k, L.xUnit, L.yUnit, L.zUnit, L.iUnit, L.jUnit, L.kUnit,
  L.coordinateSpaceId, L.comments
  FROM   SensorLocation SL
  JOIN   Location L ON L.id = SL.LocationId
  WHERE  SL.LocationId = ?", false),
  */
  public static function find($locationId) {

    $c = new Criteria();
    $c->addJoin(self::LOCATIONID, LocationPeer::ID);
    $c->clearSelectColumns();
    $c->addAsColumn("id", self::LOCATIONID);
    $c->addSelectColumn(self::PLANID);
    $c->addSelectColumn(self::SENSORTYPEID);
    $c->addSelectColumn(LocationPeer::LABEL);
    $c->addSelectColumn(LocationPeer::X);
    $c->addSelectColumn(LocationPeer::Y);
    $c->addSelectColumn(LocationPeer::Z);
    $c->addSelectColumn(LocationPeer::I);
    $c->addSelectColumn(LocationPeer::J);
    $c->addSelectColumn(LocationPeer::K);
    $c->addSelectColumn(LocationPeer::X_UNIT);
    $c->addSelectColumn(LocationPeer::Y_UNIT);
    $c->addSelectColumn(LocationPeer::Z_UNIT);
    $c->addSelectColumn(LocationPeer::I_UNIT);
    $c->addSelectColumn(LocationPeer::J_UNIT);
    $c->addSelectColumn(LocationPeer::K_UNIT);
    $c->addSelectColumn(LocationPeer::COORDINATE_SPACE_ID);
    $c->addSelectColumn(LocationPeer::COMMENTS);

    $c->add(self::LOCATIONID, $locationId);

    return self::doSelectRS($c);
  }



  /**
   * Find all Locations by Experiment
   *
   * @param int $expid
   * @return array <Location>
   */
  public static function findByExperiment($expid, $planTypeId = null) {

    include_once 'lib/data/LocationPlan.php';

    $c = new Criteria();
    $c->addJoin(self::PLAN_ID, LocationPlanPeer::ID);
    $c->add(LocationPlanPeer::EXPID, $expid);
    $c->add(LocationPlanPeer::TRIAL_ID, null, Criteria::ISNULL);

    if( ! is_null($planTypeId)) {
      $c->add(LocationPlanPeer::PLAN_TYPE_ID, $planTypeId);
    }

    $c->addAscendingOrderByColumn(LocationPeer::PLAN_ID);
    $c->addAscendingOrderByColumn(LocationPeer::LABEL);

    return self::doSelect($c);
  }




  /**
   * Find all Locations by Trial
   *
   * @param int $trialid
   * @return array <Location>
   */
  public static function findByTrial($trialid, $planTypeId = null) {

    include_once 'lib/data/LocationPlan.php';

    $c = new Criteria();
    $c->addJoin(self::PLAN_ID, LocationPlanPeer::ID);
    $c->add(LocationPlanPeer::TRIAL_ID, $trialid);

    if( ! is_null($planTypeId)) {
      $c->add(LocationPlanPeer::PLAN_TYPE_ID, $planTypeId);
    }

    $c->addAscendingOrderByColumn(LocationPeer::PLAN_ID);
    $c->addAscendingOrderByColumn(LocationPeer::LABEL);

    return self::doSelectJoinLocationPlan($c);
  }



  /**
   * Get count number of all Locations by Experiment
   * select count(*) from Location where PLAN_ID in (select ID from Location_Plan where expid = 1118))
   *
   * @param int $expid
   * @return int
   */
  public static function countByExperiment($expid, $planTypeId = null) {

    include_once 'lib/data/LocationPlan.php';

    $c = new Criteria();
    $c->addJoin(self::PLAN_ID, LocationPlanPeer::ID);
    $c->add(LocationPlanPeer::EXPID, $expid);
    $c->add(LocationPlanPeer::TRIAL_ID, null, Criteria::ISNULL);

    if( ! is_null($planTypeId)) {
      $c->add(LocationPlanPeer::PLAN_TYPE_ID, $planTypeId);
    }
    return self::doCount($c);
  }


  /**
   * Find all Locations given by a LocationPlan ID
   *
   * @param int $lpid: LocationPlan ID
   * @return array <Location>
   */
  public static function findByLocationPlan($lpid) {
    $c = new Criteria();
    $c->add(self::PLAN_ID, $lpid);
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  /**
   * Find count number of Locations given by a LocationPlan ID
   *
   * @param int $lpid: LocationPlan ID
   * @return int
   */
  public static function countByLocationPlan($lpid) {
    $c = new Criteria();
    $c->add(self::PLAN_ID, $lpid);
    return self::doCount($c);
  }

  /**
   *
   *
   */
  public static function getLocationType($p_oLocation){
    $oSensorType = $p_oLocation->getSensorType();
    if($oSensorType){
      return $oSensorType->getName();
    }
    return null;
  }

  /**
   *
   *
   */
  public static function getLabel($p_oLocation){
    $strLabel = $p_oLocation->getLabel();
    return is_null($strLabel) ? "None" : $strLabel;
  }

  /**
   *
   */
  public static function formatCoordinate($p_oLocation, $p_strCoordinate){
  	$strGetterMethod = "get{$p_strCoordinate}";
  	$strValue = $p_oLocation->$strGetterMethod();
  	$strGetterMethod .= "Unit";
  	$iUnitId =  $p_oLocation->$strGetterMethod();
  	if($iUnitId){
  	  $oUnit = MeasurementUnitPeer::find($iUnitId);
  	  $strValue .= "&nbsp;" .$oUnit->getAbbreviation();
  	}
  	return $strValue;
  }

  /**
   *
   */
  public static function getCoordinateValue($p_oLocation, $p_strCoordinate){
    $strGetterMethod = "get{$p_strCoordinate}";
    $strValue = $p_oLocation->$strGetterMethod();
    return $strValue;
  }

  /**
   *
   */
  public static function getOrientation($p_oLocation){
    return $p_oLocation->getCorrectedOrientation();
  }

} // LocationPeer
?>
