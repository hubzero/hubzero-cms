<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewMaterials extends JView{
	
  function display($tpl = null){
  	//$iExperimentId = JRequest::getVar("expid");
	
  	$iProjectId = JRequest::getVar("projectId");
  	$this->assignRef("projectId", $iProjectId);
  	
  	$iExperimentId = JRequest::getVar("experimentId");
  	$this->assignRef("experimentId", $iExperimentId);
  	
	//get the materials
    $oMaterialsModel =& $this->getModel();
    
    parent::display($tpl);
  }//end display
  
}

?>