<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/ProjectGrantPeer.php';

class ProjectEditorViewAddSponsor extends JView{
	
  function display($tpl = null){
    /* @var $oProjectModel ProjectEditorModelAddSponsor */
    $oProjectModel =& $this->getModel();

    $oProjectGrantArray = array();
    if(isset($_SESSION[ProjectGrantPeer::TABLE_NAME])){
      $oProjectGrantArray = unserialize($_SESSION[ProjectGrantPeer::TABLE_NAME]);
    }

    $strSponsorPicked = $oProjectModel->getProjectGrantHTML("sponsor", $oProjectGrantArray);
    $this->assignRef("pickedSponsors", $strSponsorPicked);

    parent::display($tpl);
  }
  
}
?>
