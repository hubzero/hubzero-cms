<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewMembers extends JView{
	
  function display($tpl = null){
  	$iProjectId = JRequest::getVar("projid");
	
	//get the trial
    $oMembersModel =& $this->getModel();
    $oMembersArray = $oMembersModel->getMembersByProjectId($iProjectId);
	$_REQUEST[PersonPeer::TABLE_NAME] = serialize($oMembersArray);
	
    parent::display($tpl);
  }//end display
  
}

?>