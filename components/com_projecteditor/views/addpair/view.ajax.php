<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ProjectEditorViewAddPair extends JView{
	
  function display($tpl = null){
    $strName=$_REQUEST['field1'];
    $this->assignRef( "strName", $strName );
    parent::display($tpl);
  }
  
}
?>
