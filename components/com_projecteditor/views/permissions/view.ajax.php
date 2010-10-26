<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/RolePeer.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Authorization.php';
require_once 'lib/data/AuthorizationPeer.php';

class ProjectEditorViewPermissions extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelPermissions */
      $oModel =& $this->getModel();

    //Incoming
    $iProjectId = JRequest::getVar('projectId');
    $iPersonId = JRequest::getInt("personId", 0);
    $iRoleId = JRequest::getInt("roleId", 0);

    $strDbPermissionArray = array();
    $strSessionPermissionArray = array();
    $strSelectionPermissionArray = array();

    // a) look up authorizations from db for current user
    /* @var $oAuthorization Authorization */
    $oAuthorization = $oModel->findByPersonIdEntityidEntitytype($iPersonId, $iProjectId, 1);
    if($oAuthorization){
      $strDbPermissionArray = explode(",", $oAuthorization->getPermissions()->toString());
    }

    // b) if we have authorizations in the session, merge them.
    /* @var $oThisAuthorization Authorization */
    if(isset($_SESSION[AuthorizationPeer::TABLE_NAME])){
      $oAuthorizationArray = $_SESSION[AuthorizationPeer::TABLE_NAME];
      foreach($oAuthorizationArray as $oThisAuthorization){
        $strThisAuthorizationArray = explode(",", $oThisAuthorization->getPermissions());
        array_merge($strSessionPermissionArray, $strThisAuthorizationArray);
      }
    }

    // c) if we have an onchange event, get the selection by roleId
    /* @var $oRole Role */
    if($iRoleId){
      $oRole = $oModel->findRoleById($iRoleId);
      $strSelectionPermissionArray = $oRole->getDefaultPermissions()->getPermissionArray();
    }

    // d) merge arrays (a-c) to get all possible authorizations
    $strTemp = array_merge($strDbPermissionArray, $strSessionPermissionArray);
    $strResults = array_merge($strTemp, $strSelectionPermissionArray);
    $this->assignRef("strAuthorizationArray", $strResults);

    parent::display();
  }
  
  
  
}

?>