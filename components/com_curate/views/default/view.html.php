<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once('api/org/nees/oracle/util/DbPagination.php');

class CurateViewDefault extends JView{
	
  function display($tpl = null){
    
    parent::display($tpl);
  }
}
?>
