<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewTrial extends JView{
	
  function display($tpl = null){
    $iTrialId = JRequest::getVar("id");
	
    //get the trial
    $oTrialModel =& $this->getModel();
    $oTrial = $oTrialModel->getTrialById($iTrialId);
	
    $strTrialDescription = StringHelper::hasText($oTrial->getDescription()) ? nl2br($oTrial->getDescription()) : 'Description not available';
    $this->assignRef( Experiments::TRIAL_DESC, $strTrialDescription );
	
    parent::display($tpl);
  }//end display
  
}

?>