<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class WarehouseViewPublications extends JView {

  function display($tpl = null){
    $iProjectId = JRequest::getVar("projectId");
    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);

    $oProjectModel =& $this->getModel();

    /* @var $oModel WarehouseModelPublications */
    $oProject = $oProjectModel->getProjectById($iProjectId);

    /* @var $oHubUser JUser */
    $oHubUser = $oProjectModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );

    $oPublicationArray = $oProjectModel->findProjectPublications($oHubUser->id, $oProject->getName(), 3);
    $this->assignRef( "pubArray", $oPublicationArray);

    //get the tabs to display on the page
    $strTabArray = $oProjectModel->getTabArray();
    $strTabViewArray = $oProjectModel->getTabViewArray();
    $strTabHtml = $oProjectModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "project" );
    $this->assignRef( "strTabs", $strTabHtml );

    //removed tree from display as of NEEScore meeting on 4/8/10
    $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    //$this->assignRef( "mod_warehousedocs", ComponentHtml::getModule("mod_warehousedocs") );
    $this->assignRef( "mod_warehousetags", ComponentHtml::getModule("mod_warehousetags") );

    // update and get the page views
    $iEntityViews = $oProjectModel->getPageViews(1, $oProject->getId());
    $this->assignRef("iEntityActivityLogViews", $iEntityViews);

    // update and get the page views
    $iEntityDownloads = $oProjectModel->getEntityDownloads(1, $oProject->getId());
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

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
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"javascript:void(0)");

    parent::display($tpl);
  }//end display

//end display
}
?>