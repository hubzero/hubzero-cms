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

class ProjectEditorViewAnalysis extends JView{

  function display($tpl = null){
    /* @var $oModel ProjectEditorModelAnalysis */
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
      $_REQUEST[ProjectPeer::TABLE_NAME] = serialize($oProject);
    }

    /* @var $oExperiment Experiment */
    $oExperiment = null;
    $iExperimentId = JRequest::getInt('experimentId',0);
    $this->assignRef( "iExperimentId", $iExperimentId );

    if($iExperimentId){
      $oExperiment = $oModel->getExperimentById($iExperimentId);
      $_REQUEST[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);
    }

    $iEntityViews = 0;
    $iEntityDownloads = 0;

    $oAuthorizer = Authorizer::getInstance();
    if(!$oExperiment){
      if(!$oAuthorizer->canEdit($oProject)){
        echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_PROJECT_EDIT_ERROR);
        return;
      }

      //get the tabs to display on the page
      $strTabArray = $oModel->getTabArray();
      $strTabViewArray = $oModel->getTabViewArray();
      $strTabViewArray[0] = "project";
      $strOption = "warehouse/projecteditor/project/$iProjectId";
      $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "project" );
      $this->assignRef( "strTabs", $strTabHtml );

      //get the sub tabs to display on the page
      $strSubTab = JRequest::getVar('subtab', 'analysis');
      $strSubTabArray = $oModel->getProjectSubTabArray();
      $strSubTabViewArray = $oModel->getProjectSubTabViewArray();

      $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId", "", $strSubTabArray, $strSubTabViewArray, $strSubTab );
      $this->assignRef( "strSubTabs", $strSubTabHtml );

      $strPath = $oProject->getPathname() ."/Analysis";

      $iEntityViews = $oModel->getEntityPageViews(1, $oProject->getId());
      $iEntityDownloads = $oModel->getEntityDownloads(1, $oProject->getId());
    }else{
      if(!$oAuthorizer->canEdit($oExperiment)){
        echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_EXPERIMENT_EDIT_ERROR);
        return;
      }

      //get the tabs to display on the page
      $strTabArray = $oModel->getTabArray();
      $strTabViewArray = $oModel->getTabViewArray();
      $strOption = "warehouse/projecteditor/project/$iProjectId";
      $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "experiments" );
      $this->assignRef( "strTabs", $strTabHtml );

      //get the sub tabs to display on the page
      $strSubTab = JRequest::getVar('subtab', 'analysis');

      $strSubTabArray = $oModel->getExperimentsSubTabArray();
      $strSubTabViewArray = $oModel->getExperimentsSubTabViewArray();
      $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTabViewArray, $strSubTab );
      $this->assignRef( "strSubTabs", $strSubTabHtml );

      $strPath = $oExperiment->getPathname() ."/Analysis";

      $iEntityViews = $oModel->getEntityPageViews(3, $oExperiment->getId());
      $iEntityDownloads = $oModel->getEntityDownloads(3, $oExperiment->getId());
    }

    //get the files
    $iDisplay = JRequest::getVar('limit', 25);
    $iPageIndex = JRequest::getVar('index', 0);

    $iLowerLimit = $oModel->computeLowerLimit($iPageIndex, $iDisplay);
    $iUpperLimit = $oModel->computeUpperLimit($iPageIndex, $iDisplay);

    //get the current documents
    $oDocumentArray = $oModel->findDataFilesByDirectory($strPath, array(), $iProjectId, $iLowerLimit, $iUpperLimit, $iExperimentId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDocumentArray);
    $this->assignRef( 'strPath', $strPath );

    $iUploadType = Files::ANALYSIS;
    $this->assignRef( 'uploadType', $iUploadType );

    //get the total number of files
    $iResultsCount = $oModel->findDataFilesByDirectoryCount($strPath, array(), $iProjectId, $iExperimentId);
    $this->assignRef("documentCount", $iResultsCount);

    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
    $oDbPagination = new DbPagination($iPageIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmProject", "project-list"));

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    $strProjectName = $oProject->getName();

    JFactory::getApplication()->getPathway()->addItem($strProjectName,"/warehouse/projecteditor/project/$iProjectId");
    if($iExperimentId){
      $strExperimentTitle = $oExperiment->getTitle();

      JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/projecteditor/project/".$iProjectId."/experiments");
      JFactory::getApplication()->getPathway()->addItem($strExperimentTitle,"/warehouse/projecteditor/project/".$iProjectId."/experiment/".$iExperimentId);
    }
    JFactory::getApplication()->getPathway()->addItem("Analysis","javascript:void(0)");

    if($oExperiment){
      $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else if($oProject){
      $_REQUEST[Search::SELECTED] = serialize($oProject);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }

    $this->assignRef("iDisplay", $iDisplay);
    $this->assignRef("iPageIndex", $iPageIndex);

    $strReturnUrl = $oModel->getRawReturnURL();
    $this->assignRef( "strReturnUrl", $strReturnUrl );

    parent::display($tpl);
  }

}
?>
