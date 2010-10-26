<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/Location.php';
require_once 'lib/data/Role.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Authorization.php';
require_once 'lib/data/AuthorizationPeer.php';

class ProjectEditorViewEditLocation extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iLocationId = JRequest::getInt("locationId", 0);
    $this->assignRef("locationId", $iLocationId);

    /* @var $oModel ProjectEditorModelEditLocation */
    $oModel =& $this->getModel();

    $_REQUEST[LocationPeer::TABLE_NAME] = serialize($oModel->findLocationById($iLocationId));

    parent::display();
  }
  
}

?>