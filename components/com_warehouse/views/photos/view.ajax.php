<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class WarehouseViewPhotos extends JView{
	
  function display($tpl = null){
  	
  	$iProjectId = JRequest::getVar('projectId'); 
  	$this->assignRef('projectId', $iProjectId);
  	
  	$iExperimentId = JRequest::getVar('experimentId');
  	$this->assignRef('experimentId', $iExperimentId);
  	
  	//get the tabs to display on the page
    $oPhotosModel =& $this->getModel();
    $oPhotoDataFileArray = $oPhotosModel->findDataFileByMimeType($iProjectId, $iExperimentId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oPhotoDataFileArray);
    
    parent::display($tpl);
  }
  
}

?>