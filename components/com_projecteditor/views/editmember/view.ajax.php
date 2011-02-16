<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/RolePeer.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'api/org/nees/exceptions/ValidationException.php';

class ProjectEditorViewEditMember extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iProjectId = JRequest::getVar('projectId');
    $strUser = JRequest::getVar("user", "");
    $iPersonId = JRequest::getInt("personId", 0);

    //if new user, personId should be 0
    $_SESSION["PERMISSIONS_TARGET_ID"] = "permissions-".$iPersonId;
    $strTask = "new";
    if(!StringHelper::hasText($strUser)){
      if( $iPersonId > 0 ){  
        $strTask = "edit";
      }else{
        array_push($strErrorArray, "Can't be in both new person and edit mode.");      
      }
    }else{
      //check to see if user already exists...
    }

    if(empty($strErrorArray)){
      $oProject = ProjectPeer::find($iProjectId);
      $_REQUEST[ProjectPeer::TABLE_NAME] = serialize($oProject);

      /* @var $oModel ProjectEditorModelEditMember */
      $oModel =& $this->getModel();

      //get available roles for type entity type project
      $oRoleArray = $oModel->getRolesByEntityType(1);
      $_REQUEST[RolePeer::TABLE_NAME] = serialize($oRoleArray);

      //current values...
      $strRoleArray = array();
      $strPermissionsArray = array();

      $oMemberRoleArray = array();

      //get the oracle user
      /* @var $oPerson Person */
      $oPerson = null;
      if($strTask=="new"){
//        $strUsername = $oModel->extractUsername($strUser);
//        $oPerson = $oModel->getOracleUserByUsername($strUsername);

        //check if using auto-fill, otherwise use last, first name
        if(preg_match(ProjectEditor::PERSON_NAME_PATTERN, $strUser)){
          try{
            $oPerson = $oModel->getSuggestedUsername($strUser);
          }catch(ValidationException $oException){
            array_push($strErrorArray, $oException->getEntityMessage("Team Members"));
          }
        }else{
          try{
            $oPerson = $oModel->getPersonByLastCommaFirstName($strUser);
            if(!$oPerson){
              array_push($strErrorArray, "$strUser not found.");
            }
          }catch(ValidationException $oException){
            array_push($strErrorArray, $oException->getEntityMessage("Team Members"));
          }
        }
      }else{
        //get the person by id
        $oPerson = $oModel->getPersonById($iPersonId);
        $oMemberRoleAndPermissionsArray = $oModel->getMemberRoleAndPermissionsCollection($iPersonId, $iProjectId, 1);
        if(!empty($oMemberRoleAndPermissionsArray)){
          $oMemberRoleArray = $oMemberRoleAndPermissionsArray[0];
          $strPermissionsArray = $oMemberRoleAndPermissionsArray[1];
        }
      }

      //keep track of the desired roles
      $_SESSION["USER_ROLES"] = serialize($oMemberRoleArray);

      if(!$oPerson){
        array_push($strErrorArray, "Team Members - Invalid User");
      }

      if(empty($strErrorArray)){
        $oHubUser = $oModel->getMysqlUserByUsername($oPerson->getUserName());
        $_REQUEST["ID"] = $oHubUser->id;
        $_REQUEST["LINK"] = ($oModel->getHubPublicProfile($oHubUser)) ? true : false;
        $_REQUEST[PersonPeer::TABLE_NAME] = serialize($oPerson);
        $_REQUEST["TARGET_ID"] = "";
        //$_REQUEST["ROLE_ARRAY"] = serialize($oRoleArray);
        $_REQUEST["PERMISSIONS_ARRAY"] = $strPermissionsArray;

        $this->assignRef( "bCanCreate", array_search("create", $strPermissionsArray));
        $this->assignRef( "bCanEdit", array_search("edit", $strPermissionsArray));
        $this->assignRef( "bCanDelete", array_search("delete", $strPermissionsArray));
        $this->assignRef( "bCanGrant", array_search("grant", $strPermissionsArray));
        parent::display($tpl);
      }else{
        echo ProjectEditor::MEMBER_NOT_FOUND;
      }
    }
  }
  
}

?>