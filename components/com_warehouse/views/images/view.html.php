<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class WarehouseViewImages extends JView{
	
  function display($tpl = null){
  	$iProjectId = JRequest::getVar('projid');
	$oProject = ProjectPeer::retrieveByPK($iProjectId);
	$_REQUEST[Search::SELECTED] = serialize($oProject);
	$this->assignRef( "projid", $iProjectId );
	
  	//get the tabs to display on the page
    $oExperimentsModel =& $this->getModel();
    $strTabArray = $oExperimentsModel->getTabArray();
	$strTabHtml = $oExperimentsModel->getTabs( "warehouse", $iProjectId, $strTabArray, "images" );
	$this->assignRef( "strTabs", $strTabHtml );
	
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
	JFactory::getApplication()->getPathway()->addItem("Images","#");
    parent::display($tpl);
  }
  
}

?>