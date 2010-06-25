<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once('lib/data/LocationPeer.php');
require_once('lib/data/LocationPlanPeer.php');

class WarehouseModelSensors extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  /**
   * 
   *
   */
  public function findLocationsByExperiment($p_iExpid){
  	return LocationPeer::findByExperiment($p_iExpid);
  }
  
  /**
   * 
   *
   */
  public function findByLocationPlan($p_iLocationPlanId){
  	return LocationPeer::findByLocationPlan($p_iLocationPlanId);
  }
  
  /**
   * 
   *
   */
  public function findSensorLocationPlanById($p_iLocationPlanId) {
  	return LocationPlanPeer::findSensorLocationPlanById($p_iLocationPlanId);
  }
  
  /**
   *  
   *
   */
  public function getLocationType($p_oLocation){
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
  public function getLabel($p_oLocation){
    $strLabel = $p_oLocation->getLabel();	
    return is_null($strLabel) ? "None" : $strLabel;
  }
  
  /**
   * 
   */
  public function formatCoordinate($p_oLocation, $p_strCoordinate){
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
  public function getOrientation($p_oLocation){
  	return $p_oLocation->getCorrectedOrientation();
  }
  
  public function formatOrientation(){
  	
  }
}

?>