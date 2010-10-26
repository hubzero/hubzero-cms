<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ProjectEditorViewEquipment extends JView{
	
  function display($tpl = null){
  	$strFacilityName = JRequest::getVar('term');
  	$this->assignRef( "strName", $strFacilityName );

        $strSelectedEquipmentIds = JRequest::getVar('equipmentId');
        $iEquipmentIdArray = explode(",", $strSelectedEquipmentIds);
  	
  	$oThisEquipmentArray = array();
  	
  	$oEquipmentModel =& $this->getModel();
  	
  	//find the facility by name
  	$oFacility = $oEquipmentModel->findOrganizationByName($strFacilityName);
  	
  	//get the major equipment if any
  	$oFacilityMajorEquipmentArray = $oEquipmentModel->findAllMajorByOrganization($oFacility);
  	if(!empty($oFacilityMajorEquipmentArray)){
  	  //ok, we have major equipment.  find the children by parent_id.
  	  foreach($oFacilityMajorEquipmentArray as $oMajorEquipment){
  	  	$oMinorEquipmentArray = $oEquipmentModel->findAllByParent($oMajorEquipment->getId());  //parent_id is the major_id
  	  	
  	  	/*
  	  	 * for display purpsoses override the note with the facility name.
  	  	 * the output will be [equipment] -> [major] (facility)
  	  	 */
  	  	foreach($oMinorEquipmentArray as $oMinorEquipment){
  	  	  $oMinorEquipment->setNote($oFacility->getShortName()." - ".$oMajorEquipment->getName());
  	  	  array_push($oThisEquipmentArray, $oMinorEquipment);
  	  	}
  	  }
  	}else{
  	  /*
  	   * for display purpsoses override the note with the facility name.
  	   * the output will be [equipment] -> (facility)
  	   */
  	  $oMinorEquipmentArray = $oEquipmentModel->findAllByOrganization($oFacility->getId());	
  	  foreach($oMinorEquipmentArray as $oMinorEquipment){
  	    $oMinorEquipment->setNote($oFacility->getShortName());
  	  	array_push($oThisEquipmentArray, $oMinorEquipment);
  	  }
  	}
  	
  	//if session array is empty, create a new array
        $oEquipmentArray = array();
        if(isset($_SESSION["SUGGESTED_FACILITY_EQUIPMENT"])){
  	  $oEquipmentArray = unserialize($_SESSION["SUGGESTED_FACILITY_EQUIPMENT"]);
        }
  	
  	//append the new equipment to the content of the session
  	foreach($oThisEquipmentArray as $oEquipment){
  	  array_push($oEquipmentArray, $oEquipment);
  	}
  	
  	$_SESSION["SUGGESTED_FACILITY_EQUIPMENT"] = serialize($oEquipmentArray);
        $this->assignRef( 'iSelectedEquipmentArray', $iEquipmentIdArray );
  	
    
	parent::display($tpl);
  }
  
}
?>
