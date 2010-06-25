<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class DataViewGet extends JView{
	
  function display($tpl = null){
    #$model =& $this->getModel();
	#$greeting = $model->getDefaultGreeting();
    #$this->assignRef( 'greeting', $greeting );
 	
    #$greetingArray = $model->getGreeting();
    #$this->assignRef( 'greetingArray', $greetingArray );
    
    parent::display($tpl);
  }
}
?>
