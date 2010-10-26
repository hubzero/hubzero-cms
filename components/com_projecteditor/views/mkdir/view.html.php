<?php 

/**
 * @see components/com_projecteditor/models/uploadform.php
 * @see modules/mod_warehouseupload
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';

class ProjectEditorViewMkDir extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelMkDir */
    $oModel =& $this->getModel();

    //Incoming 
    $strPath = JRequest::getVar("path", "");
    $iProjectId = JRequest::getInt("projid", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);
    $strReferer = $_SERVER['HTTP_REFERER'];

    $this->assignRef("strPath", $strPath);
    $this->assignRef("iProjectId", $iProjectId);
    $this->assignRef("iExperimentId", $iExperimentId);
    $this->assignRef("strReferer", $strReferer);
    
    parent::display($tpl);
  }
  
}

?>