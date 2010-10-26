<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
//require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewFileBrowser extends JView{
	
  function display($tpl = null){
    $oDataModel =& $this->getModel();

    // Incoming
    $strPath = JRequest::getVar("path","");
    $strTopPath = JRequest::getVar("toppath","");
    $strDivId = JRequest::getVar('div');
    $strParentDivId = JRequest::getVar('parent');
    $iRequestType = JRequest::getVar('uploadType');
    $iProjectId = JRequest::getInt("projid", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);

    $_REQUEST[Files::CURRENT_DIRECTORY] = $strPath;
    $_REQUEST[Files::TOP_DIRECTORY] = $strTopPath;
    $_REQUEST[Files::CHILD_DIV] = $strDivId;
    $_REQUEST[Files::PARENT_DIV] = $strParentDivId;
    $_REQUEST[Files::PROJECT_ID] = $iProjectId;
    $_REQUEST[Files::EXPERIMENT_ID] = $iExperimentId;
    $_REQUEST[Files::REQUEST_TYPE] = $iRequestType;

    
    $this->assignRef( "mod_warehouseupload", ComponentHtml::getModule("mod_warehouseupload") );
	
    parent::display($tpl);
  }//end display
  
  
}

?>