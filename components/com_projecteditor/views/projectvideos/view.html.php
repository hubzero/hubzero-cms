<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/Experiment.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'api/org/nees/oracle/util/DbPagination.php';
require_once 'api/org/nees/static/Files.php';

class ProjectEditorViewProjectVideos extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelDrawings */
    $oModel =& $this->getModel();

    $oHubUser = $oModel->getCurrentUser();
    $this->assignRef("oUser", $oHubUser);

    $iProjectId = JRequest::getVar('projid');
    $this->assignRef( "iProjectId", $iProjectId );

    /* @var $oProject Project */
    $oProject = $oModel->getProjectById($iProjectId);
    if(!$oProject){
      echo ComponentHtml::showError(ProjectEditor::PROJECT_ERROR_MESSAGE);
      return;
    }else{
      $oAuthorizer = Authorizer::getInstance();
      if(!$oAuthorizer->canEdit($oProject)){
        echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_PROJECT_EDIT_ERROR);
        return;
      }
    }
    $_REQUEST[ProjectPeer::TABLE_NAME] = serialize($oProject);
    
    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'videos');

    $strSubTabArray = $oModel->getProjectSubTabArray();
    $strSubTabViewArray = $oModel->getProjectSubTabViewArray();

    $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId", "", $strSubTabArray, $strSubTabViewArray, $strSubTab );
    if(!$iProjectId){
      $strSubTabHtml = $oModel->getOnClickSubTabs( ProjectEditor::CREATE_PROJECT_SUBTAB_ALERT, $strSubTabArray, $strSubTab );
    }
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $strPath = $oProject->getPathname() ."/Documentation/Videos";
    $this->assignRef( 'strPath', $strPath );

    if(!is_dir($strPath)){
      $oVideosFileCommand = FileCommandAPI::create($strPath);
      $oVideosFileCommand->mkdir(TRUE);
    }

    if(!is_dir($strPath."/Movies")){
      $oVideosMovieFileCommand = FileCommandAPI::create($strPath."/Movies");
      $oVideosMovieFileCommand->mkdir(TRUE);
    }
    
    if(!is_dir($strPath."/Frames")){
      $oVideosFramesFileCommand = FileCommandAPI::create($strPath."/Frames");
      $oVideosFramesFileCommand->mkdir(TRUE);
    }

    //get the videos
    $iDisplay = JRequest::getVar('limit', 25);
    $iPageIndex = JRequest::getVar('index', 0);

    $iLowerLimit = $oModel->computeLowerLimit($iPageIndex, $iDisplay);
    $iUpperLimit = $oModel->computeUpperLimit($iPageIndex, $iDisplay);

    //$oVideoArray = $oModel->findDataFileByUsage("Video", array(), $iLowerLimit, $iUpperLimit, $iProjectId);
    $oVideoArray = $oModel->findProjectEditorVideos("'video/%'", "'wmv'", "Videos/Frames", $iProjectId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oVideoArray);
    
    $iUploadType = Files::VIDEO;
    $this->assignRef( 'uploadType', $iUploadType );

    //get the total number of files
    //$iResultsCount = $oModel->findDataFileByUsageCount("Video", array(), $iProjectId);
    $iResultsCount = $oModel->findProjectEditorVideosCount("'video/%'", "'wmv'", "Videos/Frames", $iProjectId);
    $this->assignRef("documentCount", $iResultsCount);

    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
    $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmProject", "project-list"));

    $iEntityViews = $oModel->getEntityPageViews(1, $oProject->getId());
    $iEntityDownloads = $oModel->getEntityDownloads(1, $oProject->getId());

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    if($oProject){
      $_REQUEST[Search::SELECTED] = serialize($oProject);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }

    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"javascript:void(0)");
    JFactory::getApplication()->getPathway()->addItem("Videos","javascript:void(0)");

    $strReturnUrl = $oModel->getRawReturnURL();
    $this->assignRef( "strReturnUrl", $strReturnUrl );

    parent::display($tpl);
  }
  
}
?>
