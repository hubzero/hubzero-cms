<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/Organization.php';
require_once 'lib/data/OrganizationPeer.php';
require_once 'lib/data/Facility.php';

class ProjectEditorViewAddOrganization extends JView{
	
  function display($tpl = null){
    parent::display($tpl);
  }
  
}
?>
