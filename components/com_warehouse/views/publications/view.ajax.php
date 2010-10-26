<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewPublications extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar("projectId");
    $this->assignRef("projectId", $iProjectId);

    $oModel =& $this->getModel();

    /* @var $oModel WarehouseModelPublications */
    $oProject = $oModel->getProjectById($iProjectId);

    $oUser =& JFactory::getUser();
    $oPublicationArray = $oModel->findProjectPublications($oUser->id, $oProject->getName(), 3);
    $this->assignRef( "pubArray", $oPublicationArray);

    parent::display($tpl);
  }//end display
  
}

?>