<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once 'libraries/joomla/application/module/helper.php';

class WarehouseViewExperiment extends JView{
	
  function display($tpl = null){
  	$iExperimentId = JRequest::getVar("id");
	$oExperiment = ExperimentPeer::retrieveByPK($iExperimentId);
	$_REQUEST[Experiments::SELECTED] = serialize($oExperiment);
	
	$iProjectId = JRequest::getVar("projid");
	$oProject = ProjectPeer::retrieveByPK($iProjectId);
	
	//get the tabs to display on the page
    $oExperimentModel =& $this->getModel();
    $strTabArray = $oExperimentModel->getTabArray();
	$strTabHtml = $oExperimentModel->getTabs( "warehouse", $iProjectId, $strTabArray, "experiments" );
	$this->assignRef( "strTabs", $strTabHtml );
	
	$oTrialArray = $oExperimentModel->findTrialsByExperiment($oExperiment->getId(), "trialid");
	
	$_REQUEST[TrialPeer::TABLE_NAME] = serialize($oTrialArray);
	$_REQUEST[OrganizationPeer::TABLE_NAME] = serialize($oExperimentModel->findFacilityByExperiment($iExperimentId));
	
	$oDrawingArray = $oExperimentModel->findDataFileByType("Drawing", $iProjectId, $iExperimentId);
	$_REQUEST["Drawings"] = serialize($oDrawingArray);
	
	/*
	 * Check to see if the current experiment has any repetitions.
	 * If yes, display repetitions in the table.  If no, hide repetitions. 
	 */
	$oDataFileLinkArray = $oExperimentModel->findRepetitionDataFileLinksByExperiment($oExperiment->getId());
	$this->assignRef( "repetitionDataFileSize", sizeof($oDataFileLinkArray) );
	
	$this->assignRef( "mod_warehousedocs", ComponentHtml::getModule("mod_warehousedocs") );
	$this->assignRef( "mod_warehousetags", ComponentHtml::getModule("mod_warehousetags") );
	
	//set the breadcrumbs
	JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
	JFactory::getApplication()->getPathway()->addItem("Results","/warehouse/find?keywords=".$_SESSION[Search::KEYWORDS]
																. "&type=".$_SESSION[Search::SEARCH_TYPE]
																. "&funding=".$_SESSION[Search::FUNDING_TYPE]
																. "&member=".$_SESSION[Search::MEMBER]
																. "&startdate=".$_SESSION[Search::START_DATE]
																. "&startdate=".$_SESSION[Search::END_DATE]);
	JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"javascript:void(0)");															
	JFactory::getApplication()->getPathway()->addItem($oExperiment->getName(),"javascript:void(0)");
    
    parent::display($tpl);
  }//end display
  
  /**
   * Gets the list of organizations for the project
   * @return array of organization names
   */
  private function getOrganizations($p_oExperiment){
  	return OrganizationPeer::findByExperiment($p_oExperiment->getId());
  }
  
}

?>