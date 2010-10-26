<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/Person.php';
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/PersonEntityRolePeer.php';
require_once 'lib/data/PersonEntityRole.php';

class ProjectEditorViewDeleteMember extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iProjectId = JRequest::getInt('projectId', 0);
    $iPersonId = JRequest::getInt("personId", 0);

    /* @var $oModel ProjectEditorModelDeleteMember */ 
    $oModel =& $this->getModel();

    /* @var $oPerson Person */
    $oPerson = $oModel->getPersonById($iPersonId);
    if(!$oPerson){

    }

    /* @var $oProject Project */
    $oProject = $oModel->getProjectById($iProjectId);
    if(!$oProject){

    }

    $oPersonEntityRoleArray = PersonEntityRolePeer::findByPersonEntityEntityType($oPerson->getId(), $oProject->getId(), 1);

    $_REQUEST[PersonPeer::TABLE_NAME] = serialize($oPerson);
    $_REQUEST[ProjectPeer::TABLE_NAME] = serialize($oProject);
    $_REQUEST[PersonEntityRolePeer::TABLE_NAME] = serialize($oPersonEntityRoleArray);
    
    parent::display();
  }
  
}

?>