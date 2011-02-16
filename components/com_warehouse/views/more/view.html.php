<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/security/Authorizer.php';

class WarehouseViewMore extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
    $oProject = ProjectPeer::find($iProjectId);
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
    $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmPhotos", "project-list"));

    /* @var $oHubUser JUser */
    $oHubUser = $oMoreModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );
    
    //set the breadcrumbs
    JFactory::getApplication()->getPathway()->addItem("Project Editor","/warehouse/projecteditor");
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/projecteditor/project/$iProjectId");
    JFactory::getApplication()->getPathway()->addItem("More","#");
    parent::display($tpl);
  }
  
}

?>