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
	
    /* @var $oMembersModel WarehouseModelMembers */
    $oMembersModel =& $this->getModel();

    //get the tabs to display on the page
    $strTabArray = $oMembersModel->getTabArray();
    $strTabHtml = $oMembersModel->getTabs( "warehouse", $iProjectId, $strTabArray, "team members" );
    $this->assignRef( "strTabs", $strTabHtml );

    //removed tree from display as of NEEScore meeting on 4/8/10
    //$this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );

    $iDisplay = JRequest::getVar('limit', 25);
    $iIndex = JRequest::getVar('index', 0);

    
    //find the upper and lower bounds for pagination
    $iLowerLimit = $oMembersModel->computeLowerLimit($iIndex, $iDisplay);
    $iUpperLimit = $oMembersModel->computeUpperLimit($iIndex, $iDisplay);
    $oMembersArray = $this->getMembersForEntityWithPagination($oMembersModel, $iProjectId, $iLowerLimit, $iUpperLimit);
    $_REQUEST[PersonPeer::TABLE_NAME] = $oMembersArray;
	
    $iCount = $oMembersModel->findMembersForEntityCount($iProjectId);
    $this->assignRef( "iMemberCount", $iCount);

    $oDbPagination = new DbPagination($iIndex, $iCount, $iDisplay);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmResults", "project-list"));
	
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

  /**
   *
   * @param WarehouseModelMembers $p_oMembersModel
   * @param int $p_iProjectId
   * @param int $p_iLowerLimit
   * @param int $p_iUpperLimit
   * @return array
   */
  private function getMembersForEntityWithPagination($p_oMembersModel, $p_iProjectId, $p_iLowerLimit, $p_iUpperLimit){
    $oMembersResutSet = $p_oMembersModel->findMembersForEntityWithPagination($p_iProjectId, 1, $p_iLowerLimit, $p_iUpperLimit);
    $oMembersArray = array();
    while($oMembersResutSet->next()){
      $oPersonArray = array();	
      $oPersonArray['FIRST_NAME'] = ucfirst($oMembersResutSet->getString('FIRST_NAME'));
      $oPersonArray['LAST_NAME'] = ucfirst($oMembersResutSet->getString('LAST_NAME'));
      $oPersonArray['ROLE'] = $oMembersResutSet->getString('ROLENAME');
      $oPersonArray['EMAIL'] = $oMembersResutSet->getString('E_MAIL');
      $oPersonArray['USER_NAME'] = $oMembersResutSet->getString('USER_NAME');
      $oPersonArray['ID'] = $oMembersResutSet->getInt('ID');
      //$oPersonArray['PERMISSIONS'] = $oMembersResutSet->getString('PERMISSIONS');
      $oPersonArray['PERMISSIONS'] = "";
      $oPersonArray['LINK'] = false;
      $oPersonArray['PICTURE'] = "/components/com_members/images/profile_thumb.gif";
      $oPersonArray['HUB_ID'] = 0;

      //check to see if we can show the link for this user
      $oHubUser = $p_oMembersModel->getMysqlUserByUsername($oPersonArray['USER_NAME']);
      if($oHubUser){
        $profile->load( $oHubUser->id );

        $oPersonArray['HUB_ID'] = $oHubUser->id;
        $oPersonArray['PICTURE'] = "/site/members/0".$oHubUser->id."/".$profile->get('picture');
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