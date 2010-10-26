<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/RolePeer.php';
require_once 'lib/data/Role.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Authorization.php';
require_once 'lib/data/AuthorizationPeer.php';

class ProjectEditorViewEditRole extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();
    
    /* @var $oModel ProjectEditorModelEditRole */
    $oModel =& $this->getModel();

    //Incoming
    $iRoleId = JRequest::getInt("roleId", 0);
    $this->assignRef("roleId", $iRoleId);

    $iPersonId = JRequest::getInt("personId", 0);
    $this->assignRef("personId", $iPersonId);

    $iProjectId = JRequest::getInt("projectId", 0);
    $this->assignRef("projectId", $iProjectId);

    $strAction = JRequest::getVar("action", "add");

    $iSelectedRemoveIndex = JRequest::getInt("index", 0);

    if($iRoleId > 0){
      $oResultsArray = array();
      if($strAction=="add"){
        $oResultsArray = $this->addRole($oModel, $iPersonId, $iProjectId, $iRoleId);
      }elseif($strAction=="remove"){
        $oResultsArray = $this->removeRole($iSelectedRemoveIndex);
      }
      
      $_SESSION["USER_ROLES"] = serialize($oResultsArray);
      $_REQUEST[RolePeer::TABLE_NAME] = serialize($oModel->getRolesByEntityType(1));

      parent::display();
    }
  }
  
  public function addRole($oModel, $iPersonId, $iProjectId, $iRoleId){
    $oResultsArray = array();

    /*
     * a) look up the user's current roles from db
     *    if we don't have a list in the session.
     *
     *    if the user has roles in the db, a user
     *    may manage them in the interface.
     */
    $oDbRoleArray = array();
    if(!isset($_SESSION["USER_ROLES"])){
      $oDbRoleArray = $oModel->getRolesByPersonEntity($iPersonId, $iProjectId, 1);
    }

    /*
     * b) look up the roles from the session.
     *
     *    merge the db and session arrays to get
     *    the most authorizations possible.
     */
    $oSessionRoleArray = array();
    if(isset($_SESSION["USER_ROLES"])){
      $oSessionRoleArray = unserialize($_SESSION["USER_ROLES"]);
      $oResultsArray = array_merge($oDbRoleArray, $oSessionRoleArray);
    }

    /*
     * c) add the new (selected) role from the UI.
     *
     *    merge the roles with the results of (b).
     */

    /* @var $oRole Role */
    $oRole = $oModel->findRoleById($iRoleId);
    if(!array_search($oRole, $oResultsArray)){
      array_push($oResultsArray, $oRole);
    }

    return $oResultsArray;
  }

  public function removeRole($p_iCurrentRoleIndex){
    $oSessionRoleArray = unserialize($_SESSION["USER_ROLES"]);
    unset($oSessionRoleArray[$p_iCurrentRoleIndex]);
    return array_values($oSessionRoleArray);
  }
  
}

?>