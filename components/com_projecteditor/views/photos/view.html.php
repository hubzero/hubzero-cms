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

class ProjectEditorViewPhotos extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelPhotos */
    $oModel =& $this->getModel(); 

    $oHubUser = $oModel->getCurrentUser();
    $this->assignRef("oUser", $oHubUser);

    $iProjectId = JRequest::getVar('projid');
    $this->assignRef( "iProjectId", $iProjectId );

    /* @var $oProject Project */
    $oProject = $oModel->getProjectById($iProjectId);

    /* @var $oExperiment Experiment */
    $oExperiment = null;
    $iExperimentId = JRequest::getInt('experimentId',0);
    if($iExperimentId){
      $oExperiment = $oModel->getExperimentById($iExperimentId);
      $_SESSION[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);
    }else{
      if(isset($_SESSION[ExperimentPeer::TABLE_NAME])){
        $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
      }
    }

    if(!$oExperiment){
      echo ComponentHtml::showError(ProjectEditor::EXPERIMENT_ERROR_MESSAGE);
      return;
    }else{
      $oAuthorizer = Authorizer::getInstance();
      if(!$oAuthorizer->canEdit($oExperiment)){
        echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_EXPERIMENT_EDIT_ERROR);
        return;
      }
    }

    $this->assignRef( "iExperimentId", $oExperiment->getId() );
    
    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'data');

    $strSubTabArray = $oModel->getExperimentsSubTabArray();
    $strSubTabViewArray = $oModel->getExperimentsSubTabViewArray();
    $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTabViewArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $strPath = $oExperiment->getPathname() ."/Documentation/Photos";
    $this->assignRef( 'strPath', $strPath );

    if(!is_dir($strPath)){
      $oFileCommand = FileCommandAPI::create($strPath);
      $oFileCommand->mkdir(TRUE);
    }

    //get the photos
    $iPhotoType = JRequest::getVar('photoType', DataFilePeer::PHOTO_TYPE_GENERAL);
    $iDisplay = JRequest::getVar('limit', 25);
    $iPageIndex = JRequest::getVar('index', 0);

    $iLowerLimit = $oModel->computeLowerLimit($iPageIndex, $iDisplay);
    $iUpperLimit = $oModel->computeUpperLimit($iPageIndex, $iDisplay);

    //get the current photos
    //$oDocumentArray = $oModel->findDataFilePhotosByDirectory($strPath, array(), $iProjectId, $iLowerLimit, $iUpperLimit, $iExperimentId);
    $oDocumentArray = $oModel->getProjectEditorPhotos($iPhotoType, $iProjectId, $iExperimentId, $iLowerLimit, $iUpperLimit);
    $oDocumentArray = DataFilePeer::resizePhotos($oDocumentArray);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDocumentArray);

    $iUploadType = Files::IMAGE;
    $this->assignRef( 'uploadType', $iUploadType );

    //get the total number of files
    //$iResultsCount = $oModel->findDataFilePhotosByDirectoryCount($strPath, array(), $iProjectId, $iExperimentId);
    $iResultsCount = $oModel->getProjectEditorPhotosCount($iPhotoType, $iProjectId, $iExperimentId);
    $this->assignRef("documentCount", $iResultsCount);

    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
    $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmProject", "project-list"));

    $iEntityViews = $oModel->getEntityPageViews(3, $oExperiment->getId());
    $iEntityDownloads = $oModel->getEntityDownloads(3, $oExperiment->getId());

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);
    $this->assignRef("iPhotoType", $iPhotoType);
    $this->assignRef("iDisplay", $iDisplay);
    $this->assignRef("iPageIndex", $iPageIndex);

    JFactory::getApplication()->getPathway()->addItem($oExperiment->getProject()->getName(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId());
    JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiments");
    JFactory::getApplication()->getPathway()->addItem($oExperiment->getTitle(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId());
    JFactory::getApplication()->getPathway()->addItem("Photos","javascript:void(0)");

    if($oExperiment){
      $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }

    $strReturnUrl = $oModel->getRawReturnURL();
    $this->assignRef( "strReturnUrl", $strReturnUrl );

    parent::display($tpl);
  }
  
}
?>
