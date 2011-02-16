<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewAdvancedSearch extends JView{
	
  function display($tpl = null){

    /* @var $oModel WarehouseModelAdvancedSearch */
    $oModel =& $this->getModel();

    $oFacilityArray = $oModel->getNeesFacilities();
    $_REQUEST[FacilityPeer::TABLE_NAME] = serialize($oFacilityArray);
    
    parent::display($tpl);
  }
  
  
}

?>