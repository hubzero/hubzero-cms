<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/oracle/util/DbPagination.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/PersonEntityRolePeer.php';
require_once 'lib/data/PersonEntityRole.php';

/*
require_once('FirePHPCore/FirePHP.class.php');
ob_start();

$firephp = FirePHP::getInstance(true);
$firephp->log('com_projecteditor/members.php');
*/

class ProjectEditorViewMembers extends JView{
	
  function display($tpl = null){
    $_SESSION["USER_ROLES"] = null;
    unset ($_SESSION["USER_ROLES"]);

    $iErrors = JRequest::getInt('errors',0);
    if(!$iErrors){
      $_SESSION["MEMBER_ERRORS"] = null;
      unset ($_SESSION["MEMBER_ERRORS"]);
    }

    $iProjectId = JRequest::getInt('projid',0);
    
    /*
     * if we don't have a project id from the request, go to session.
     */
    if(!$iProjectId){
      //if not in session, return error
      if(!isset($_SESSION[ProjectEditor::ACTIVE_PROJECT])){
        echo ComponentHtml::showError(ProjectEditor::PROJECT_ERROR_MESSAGE);
        return;
      }

      //if session value is 0, return error
      $iProjectId = $_SESSION[ProjectEditor::ACTIVE_PROJECT];
      if($iProjectId===0){
        echo ComponentHtml::showError(ProjectEditor::PROJECT_ERROR_MESSAGE);
        return;
      }
    }else{
      //we got a valid request, store in session
      $_SESSION[ProjectEditor::ACTIVE_PROJECT] = $iProjectId;
    }

    $this->assignRef( "iProjectId", $iProjectId );

    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[ProjectPeer::TABLE_NAME] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );

    $oAuthorizer = Authorizer::getInstance();
    if(!$oAuthorizer->canEdit($oProject)){
      echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_PROJECT_EDIT_ERROR);
      return;
    }

    /* @var $oMembersModel ProjectEditorModelMembers */
    $oMembersModel =& $this->getModel();

    //get the tabs to display on the page
    $strTabArray = $oMembersModel->getTabArray();
    $strTabViewArray = $oMembersModel->getTabViewArray();
    $strTabHtml = $oMembersModel->getTabs( "warehouse/projecteditor/project/$iProjectId", "", $strTabArray, $strTabViewArray, "members" );
    $this->assignRef( "strTabs", $strTabHtml );

    $oJUser = $oMembersModel->getCurrentUser();
    $this->assignRef( "strUsername", $oJUser->username );
	
    $iIndex = JRequest::getVar('index', 0);
    $iDisplay = JRequest::getVar('limit', 25);

    //find the upper and lower bounds for pagination
    $iLowerLimit = $oMembersModel->computeLowerLimit($iIndex, $iDisplay);
    $iUpperLimit = $oMembersModel->computeUpperLimit($iIndex, $iDisplay);

//    $firephp = FirePHP::getInstance(true);
//    $firephp->log("lower limit=", $iLowerLimit);
//    $firephp->log("upper limit=", $iUpperLimit);
//    $firephp->log("display=$firephp", $iDisplay);

    //perform query
    $oMembersArray = $this->getMembersForEntityWithPagination($oMembersModel, $iProjectId, $iLowerLimit, $iUpperLimit, $oProject->getExperiments());
    $_REQUEST[PersonPeer::TABLE_NAME] = $oMembersArray;

    //get count
    $iCount = $oMembersModel->findMembersForEntityCount($iProjectId);
    $this->assignRef( "iMemberCount", $iCount);

    $oDbPagination = new DbPagination($iIndex, $iCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmMemberAdd", "project-list"));
    
    $bSearch = false;
    if(isset($_SESSION[Search::KEYWORDS]))$bSearch = true;
    if(isset($_SESSION[Search::SEARCH_TYPE]))$bSearch = true;
    if(isset($_SESSION[Search::FUNDING_TYPE]))$bSearch = true;
    if(isset($_SESSION[Search::MEMBER]))$bSearch = true;
    if(isset($_SESSION[Search::START_DATE]))$bSearch = true;
    if(isset($_SESSION[Search::END_DATE]))$bSearch = true;

    //set the breadcrumbs
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/projecteditor/project/".$oProject->getId());
    JFactory::getApplication()->getPathway()->addItem("Team Members","#");
    parent::display($tpl);
  }

  /**
   *
   * @param ProjectEditorModelMembers $p_oMembersModel
   * @param int $p_iProjectId
   * @param int $p_iLowerLimit
   * @param int $p_iUpperLimit
   * @return array
   */
  private function getMembersForEntityWithPagination($p_oMembersModel, $p_iProjectId, $p_iLowerLimit, $p_iUpperLimit, $p_oExperimentArray){
    $profile = new XProfile();

    $oMembersArray = array();

    $oTeamMembersArray = $p_oMembersModel->findMembersForEntityWithPagination($p_iProjectId, 1, $p_iLowerLimit, $p_iUpperLimit);

    $config =& JComponentHelper::getParams( 'com_members' );
    $thumb = $config->get('webpath');
    if (substr($thumb, 0, 1) != DS) {
      $thumb = DS.$thumb;
    }
    if (substr($thumb, -1, 1) == DS) {
      $thumb = substr($thumb, 0, (strlen($thumb) - 1));
    }

    // Default thumbnail
    $dfthumb = $config->get('defaultpic');
    if (substr($dfthumb, 0, 1) != DS) {
      $dfthumb = DS.$dfthumb;
    }

    $dfthumb = $p_oMembersModel->createThumb($dfthumb);

    $iExperimentIdArray = array();
    foreach($p_oExperimentArray as $oExperiment){
      /* @var $oExperiment Experiment */
      $iExperimentDeleted = $oExperiment->getDeleted();
      if(!$iExperimentDeleted){
        array_push($iExperimentIdArray, $oExperiment->getId());
      }
    }

    /* @var $oPerson Person */
    foreach($oTeamMembersArray as $oPerson){
      $oPersonArray = array();

      $oPersonArray['FIRST_NAME'] = ucfirst($oPerson->getFirstName());
      $oPersonArray['LAST_NAME'] = ucfirst($oPerson->getLastName());
      $oPersonArray['EMAIL'] = $oPerson->getEMail();
      $oPersonArray['USER_NAME'] = $oPerson->getUserName();
      $oPersonArray['ID'] = $oPerson->getId();
      $oPersonArray['PERMISSIONS'] = "";
      $oPersonArray['LINK'] = false;
      $oPersonArray['PICTURE'] = $dfthumb;
      $oPersonArray['HUB_ID'] = 0;

      $oEntityMembershipArray = AuthorizationPeer::findPersonMembership($oPerson->getId(), $iExperimentIdArray, 3);
      $oPersonArray['EXPERIMENTS'] = sizeof($oEntityMembershipArray) . " of " .sizeof($iExperimentIdArray);

      //lookup roles and permissions
      $oMemberRoleAndPermissionsArray = $p_oMembersModel->getMemberRoleAndPermissionsCollection($oPersonArray['ID'], $p_iProjectId, 1);
      $oMemberRoleArray = $oMemberRoleAndPermissionsArray[0];
      $oPersonArray['ROLE'] = serialize($oMemberRoleArray);

      $strPermissionsArray = $oMemberRoleAndPermissionsArray[1];
      $oPersonArray['PERMISSIONS'] = implode(", ", $strPermissionsArray);

      $oHubUser = $p_oMembersModel->getMysqlUserByUsername($oPersonArray['USER_NAME']);
      if($oHubUser){
        //load the user
        $profile->load( $oHubUser->id );

        $oPersonArray['HUB_ID'] = $oHubUser->id;
        $uthumb = '';
        if ($profile->get('picture')) {
          $uthumb = $thumb.DS.$p_oMembersModel->formatId($profile->get('uidNumber')).DS.$profile->get('picture');
          $uthumb = $p_oMembersModel->createThumb($uthumb);
        }

        if ($uthumb && is_file(JPATH_ROOT.$uthumb)) {
          $oPersonArray['PICTURE'] = $uthumb;
        } 

        //check to see if we can show the link for this user
        if($profile->get('public') == 1){
          $oPersonArray['LINK'] = true;
        }
      }

      array_push($oMembersArray, $oPersonArray);
    }
    return $oMembersArray;
  }
  
}

?>