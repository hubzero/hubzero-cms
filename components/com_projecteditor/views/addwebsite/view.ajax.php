<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/ProjectHomepagePeer.php';

class ProjectEditorViewAddWebsite extends JView{
	
  function display($tpl = null){
    /* @var $oProjectModel ProjectEditorModelAddWebsite */
    $oProjectModel =& $this->getModel();

    $oProjectHomepageArray = array();
    if(isset($_SESSION[ProjectHomepagePeer::TABLE_NAME])){
      $oProjectHomepageArray = unserialize($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
    }

    $strWebsitePicked = $oProjectModel->getProjectLinksHtml("website", $oProjectHomepageArray);
    $this->assignRef("pickedWebsites", $strWebsitePicked);

    parent::display($tpl);
  }
  
}
?>
