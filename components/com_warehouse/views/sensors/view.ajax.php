<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewSensors extends JView{

  function display($tpl = null){
    $iLocationPlanId = JRequest::getVar("locationPlanId");

    $iProjectId = JRequest::getVar("projectId");
    $this->assignRef('projectId', $iProjectId);

    $iExperimentId = JRequest::getVar("experimentId");
    $this->assignRef('experimentId', $iExperimentId);
	
    //get the trial
    $oSensorsModel =& $this->getModel();

    $strLocationArray = array();
    $oLocationArray = $oSensorsModel->findByLocationPlan($iLocationPlanId);
    foreach($oLocationArray as $oLocation){
      $oOrientationArray = $oSensorsModel->getOrientation($oLocation);
      $strOrientation0 = ($oOrientationArray[0] === "") ? "-" : round($oOrientationArray[0],4);
      $strOrientation1 = ($oOrientationArray[1] === "") ? "-" : round($oOrientationArray[1],4);
      $strOrientation2 = ($oOrientationArray[2] === "") ? "-" : round($oOrientationArray[2],4);

//      echo "type=".$oSensorsModel->getLocationType($oLocation)."; ".
//      	   "label=".$oSensorsModel->getLabel($oLocation)."; ".
//      	   "x=".$oSensorsModel->formatCoordinate($oLocation, "X")."; ".
//      	   "y=".$oSensorsModel->formatCoordinate($oLocation, "Y")."; ".
//      	   "z=".$oSensorsModel->formatCoordinate($oLocation, "Z")."; ".
//      	   "1=".$strOrientation0.
//      	   "2=".$strOrientation1.
//      	   "3=".$strOrientation2.
//       	   "<br>";

      $strThisLocationArray = array();
      $strThisLocationArray["TYPE"] = $oSensorsModel->getLocationType($oLocation);
      $strThisLocationArray["LABEL"] = $oSensorsModel->getLabel($oLocation);
      $strThisLocationArray["X"] = $oSensorsModel->formatCoordinate($oLocation, "X");
      $strThisLocationArray["Y"] = $oSensorsModel->formatCoordinate($oLocation, "Y");
      $strThisLocationArray["Z"] = $oSensorsModel->formatCoordinate($oLocation, "Z");
      $strThisLocationArray["ORIENTATION0"] = $strOrientation0;
      $strThisLocationArray["ORIENTATION1"] = $strOrientation1;
      $strThisLocationArray["ORIENTATION2"] = $strOrientation2;

      array_push($strLocationArray, $strThisLocationArray);
    }

    $this->assignRef("locationArray", $strLocationArray);

    /* @var $oLocationPlan LocationPlan */
    $oLocationPlan = $oSensorsModel->findSensorLocationPlanById($iLocationPlanId);
    $_REQUEST[LocationPlanPeer::TABLE_NAME] = serialize($oLocationPlan);

    parent::display($tpl);
  }//end display

}

?>