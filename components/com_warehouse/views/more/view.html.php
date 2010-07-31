<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/security/Authorizer.php';

class WarehouseViewMore extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::retrieveByPK($iProjectId);
    $_REQUEST[Search::SELECTED] = serialize($oProject);
    $this->assignRef( "projid", $iProjectId );
	
  	//get the tabs to display on the page
    /* @var $oMoreModel WarehouseModelMore */
    $oMoreModel =& $this->getModel();
    $strTabArray = $oMoreModel->getTabArray();
    $strTabViewArray = $oMoreModel->getTabViewArray();
    $strTabHtml = $oMoreModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "more" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the photos
    $iDisplay = JRequest::getVar('limit', 24);
    $iPageIndex = JRequest::getVar('index', 0);

    $iLowerLimit = $oMoreModel->computeLowerLimit($iPageIndex, $iDisplay);
    $iUpperLimit = $oMoreModel->computeUpperLimit($iPageIndex, $iDisplay);    
	
    $oPhotoDataFileArray = $oMoreModel->findProjectPhotoDataFiles($iProjectId, $iLowerLimit, $iUpperLimit);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oPhotoDataFileArray);
    //print_r($oPhotoDataFileArray);
    
    $iResultsCount = $oMoreModel->findProjectPhotoDataFilesCount($iProjectId);
    $this->assignRef("photoCount", $iResultsCount);
    
    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
    $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmPhotos", "project-list"));

    /* @var $oHubUser JUser */
    $oHubUser = $oMoreModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );
    
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
	JFactory::getApplication()->getPathway()->addItem("More","#");
    parent::display($tpl);
  }
  
}

?>