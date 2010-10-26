<?php 

/**
 * @see components/com_projecteditor/models/uploadform.php
 * @see modules/mod_warehouseupload
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';

class ProjectEditorViewUpload extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelUpload */
    $oModel =& $this->getModel();

    
    
    parent::display($tpl);
  }
  
}

?>