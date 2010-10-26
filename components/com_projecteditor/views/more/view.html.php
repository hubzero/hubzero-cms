<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/oracle/util/DbPagination.php';
require_once 'lib/security/Authorizer.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/PersonEntityRolePeer.php';
require_once 'lib/data/PersonEntityRole.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewMore extends JView{
	
  function display($tpl = null){
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

    /* @var $oModel ProjectEditorModelMore */
    $oModel =& $this->getModel();

    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strTabHtml = $oModel->getTabs( "warehouse/projecteditor/project/$iProjectId", "", $strTabArray, $strTabViewArray, "more" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the photos
    $iDisplay = JRequest::getVar('limit', 24);
    $iPageIndex = JRequest::getVar('index', 0);

    $iLowerLimit = $oModel->computeLowerLimit($iPageIndex, $iDisplay);
    $iUpperLimit = $oModel->computeUpperLimit($iPageIndex, $iDisplay);

    $oPhotoDataFileArray = $oModel->findProjectPhotoDataFiles($iProjectId, $iLowerLimit, $iUpperLimit);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oPhotoDataFileArray);
    //print_r($oPhotoDataFileArray);

    $iResultsCount = $oModel->findProjectPhotoDataFilesCount($iProjectId);
    $this->assignRef("photoCount", $iResultsCount);

    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
    $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter24($_SERVER['REQUEST_URI'], "frmPhotos", "project-list"));

    /* @var $oHubUser JUser */
    $oHubUser = $oModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );


    $bSearch = false;
    if(isset($_SESSION[Search::KEYWORDS]))$bSearch = true;
    if(isset($_SESSION[Search::SEARCH_TYPE]))$bSearch = true;
    if(isset($_SESSION[Search::FUNDING_TYPE]))$bSearch = true;
    if(isset($_SESSION[Search::MEMBER]))$bSearch = true;
    if(isset($_SESSION[Search::START_DATE]))$bSearch = true;
    if(isset($_SESSION[Search::END_DATE]))$bSearch = true;

    //set the breadcrumbs
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"/warehouse/projecteditor/project/$iProjectId");
    JFactory::getApplication()->getPathway()->addItem("More","javascript:void(0)");
    parent::display($tpl);
  }
  
}

?>