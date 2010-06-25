<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewProject extends JView{
	
  function display($tpl = null){
    $model =& $this->getModel();
	
    $this->assignRef( "variable", "value" );
    
    parent::display($tpl);
  }
}

?>