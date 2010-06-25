<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class WarehouseViewMembers extends JView{
	
  function display($tpl = null){
  	$iProjectId = JRequest::getVar('projid');
	$oProject = ProjectPeer::retrieveByPK($iProjectId);
	$_REQUEST[Search::SELECTED] = serialize($oProject);
	$this->assignRef( "projid", $iProjectId );
	
  	//get the tabs to display on the page
    $oExperimentsModel =& $this->getModel();
    $strTabArray = $oExperimentsModel->getTabArray();
	$strTabHtml = $oExperimentsModel->getTabs( "warehouse", $iProjectId, $strTabArray, "team members" );
	$this->assignRef( "strTabs", $strTabHtml );
	
	//removed tree from display as of NEEScore meeting on 4/8/10
	//$this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );
	
	
	$iLimitStart = JRequest::getVar('limitstart', 0);
    $iDisplay = JRequest::getVar('limit', 25);
    
    //find the upper and lower bounds for pagination
    $oMembersModel =& $this->getModel();
  	$iLowerLimit = $oMembersModel->computeLowerLimit($iLimitStart);
    $iUpperLimit = $oMembersModel->computeUpperLimit($iLowerLimit, $iDisplay);
    
    $oMembersArray = $this->getMembersForEntityWithPagination($oMembersModel, $iProjectId, $iLimitStart, $iDisplay);
	$_REQUEST[PersonPeer::TABLE_NAME] = $oMembersArray;
	
	$iCount = $oMembersModel->findMembersForEntityCount($iProjectId);
	$this->assignRef( "iMemberCount", $iCount);
	
    $bSearch = false;
	if(isset($_SESSION[Search::KEYWORDS]))$bSearch = true;
	if(isset($_SESSION[Search::SEARCH_TYPE]))$bSearch = true;
	if(isset($_SESSION[Search::FUNDING_TYPE]))$bSearch = true;
	if(isset($_SESSION[Search::MEMBER]))$bSearch = true;
	if(isset($_SESSION[Search::START_DATE]))$bSearch = true;
	if(isset($_SESSION[Search::END_DATE]))$bSearch = true;
	
	//set the breadcrumbs
	JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
	if($bSearch){
	  JFactory::getApplication()->getPathway()->addItem("Results","/warehouse/find?keywords=".$_SESSION[Search::KEYWORDS]
																. "&type=".$_SESSION[Search::SEARCH_TYPE]
																. "&funding=".$_SESSION[Search::FUNDING_TYPE]
																. "&member=".$_SESSION[Search::MEMBER]
																. "&startdate=".$_SESSION[Search::START_DATE]
																. "&startdate=".$_SESSION[Search::END_DATE]);
	}
	JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/project/$iProjectId");
	JFactory::getApplication()->getPathway()->addItem("Team Members","#");
    parent::display($tpl);
  }
  
  private function getMembers($p_oMembersModel, $p_iProjectId){
    $oMembersResutSet = $p_oMembersModel->getMembersByProjectId($p_iProjectId);
    $oMembersArray = array();
    while($oMembersResutSet->next()){
      $oPersonArray = array();	
      $oPersonArray['FIRST_NAME'] = $oMembersResutSet->getString('FIRST_NAME');
      $oPersonArray['LAST_NAME'] = $oMembersResutSet->getString('LAST_NAME');
      $oPersonArray['ROLE'] = $oMembersResutSet->getString('ROLENAME');
      $oPersonArray['EMAIL'] = $oMembersResutSet->getString('E_MAIL');
      $oPersonArray['USER_NAME'] = $oMembersResutSet->getString('USER_NAME');
      $oPersonArray['ID'] = $oMembersResutSet->getInt('ID');
      array_push($oMembersArray, $oPersonArray);
    }
    return $oMembersArray;
  }
  
  private function getMembersForEntityWithPagination($p_oMembersModel, $p_iProjectId, $p_iLowerLimit, $p_iUpperLimit){
  	$oMembersResutSet = $p_oMembersModel->findMembersForEntityWithPagination($p_iProjectId, 1, $p_iLowerLimit, $p_iUpperLimit);
    $oMembersArray = array();
    while($oMembersResutSet->next()){
      $oPersonArray = array();	
      $oPersonArray['FIRST_NAME'] = $oMembersResutSet->getString('FIRST_NAME');
      $oPersonArray['LAST_NAME'] = $oMembersResutSet->getString('LAST_NAME');
      $oPersonArray['ROLE'] = $oMembersResutSet->getString('ROLENAME');
      $oPersonArray['EMAIL'] = $oMembersResutSet->getString('E_MAIL');
      $oPersonArray['USER_NAME'] = $oMembersResutSet->getString('USER_NAME');
      $oPersonArray['ID'] = $oMembersResutSet->getInt('ID');
      array_push($oMembersArray, $oPersonArray);
    }
    return $oMembersArray;
  }
  
}

?>