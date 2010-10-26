<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ProjectEditorViewAdd extends JView{
	
  function display($tpl = null){
  	$strInputField = JRequest::getVar('name');
    $this->assignRef( "strName", $strInputField );
	parent::display($tpl);
  }
  
}
?>
