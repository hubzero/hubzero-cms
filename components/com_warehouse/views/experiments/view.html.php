<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewExperiments extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $this->assignRef( "projid", $iProjectId );

    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);

    /* @var $oExperimentsModel WarehouseModelExperiments */
    $oExperimentsModel =& $this->getModel();

    //get the tabs to display on the page
    $strTabArray = $oExperimentsModel->getTabArray();
    $strTabHtml = $oExperimentsModel->getTabs( "warehouse", $iProjectId, $strTabArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );

    //removed tree from display as of NEEScore meeting on 4/8/10
    //$this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );

    $oExperimentArray = $oExperimentsModel->findByProject($iProjectId);
    $_REQUEST[Experiments::EXPERIMENT_LIST] = serialize($oExperimentArray);
    $_REQUEST[Experiments::COUNT] = sizeof($oExperimentArray);


    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
//	$iDisplay = JRequest::getVar('limit', 25);
//	$iIndex = JRequest::getVar('index', 0);
//	$iResultsCount = JRequest::getVar('count');
//	
//	$oDbPagination = new DbPagination($iIndex, sizeof($oExperimentArray), $iDisplay);
//  $oDbPagination->computePageCount();
//  $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmResults", "project-list"));

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
    JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/experiments/$iProjectId");
    parent::display($tpl);
  }
}

?>