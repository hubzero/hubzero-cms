<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewDrawings extends JView{
	
  function display($tpl = null){
  	$iProjectId = JRequest::getVar("projectId");
  	$this->assignRef("projectId", $iProjectId);
  	
  	$iExperimentId = JRequest::getVar("experimentId");
  	$this->assignRef("experimentId", $iExperimentId);
  	
  	$oDrawingsModel =& $this->getModel();
  	$oDrawingArray = $oDrawingsModel->findDataFileByEntityType("Drawing", $iProjectId, $iExperimentId);
	$_REQUEST["Drawings"] = serialize($oDrawingArray);
  	
  	parent::display($tpl);
  }//end display
  
}

?>