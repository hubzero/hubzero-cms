<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ProjectEditorViewMultipleFiles extends JView{
	
  function display($tpl = null){
    $iNumFiles = JRequest::getVar('files_num');
    $this->assignRef( "iNumFiles", $iNumFiles );
    parent::display($tpl);
  }
  
}
?>
