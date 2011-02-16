<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/lib/interface/Data.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/Project.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewData extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelData */
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

    $strDataDir = "/nees/home/".$oProject->getName().".groups/".$oExperiment->getName();
    $_REQUEST[Files::CURRENT_DIRECTORY] = $strDataDir;
    $_REQUEST[Files::TOP_DIRECTORY] = $strDataDir;

    $strPath = JRequest::getVar("path", "");
    if(!empty($strPath)){
      $strPath = get_systemPath($strPath);
      $_REQUEST[Files::CURRENT_DIRECTORY] = $strPath;
    }

    $strReturnUrl = $oModel->getRawReturnURL();
    $this->assignRef( "strReturnUrl", $strReturnUrl );
    $_REQUEST[ProjectEditor::RETURN_URL] = $strReturnUrl;

    $_REQUEST[Files::REQUEST_TYPE] = Files::DATA;

    //Create the directory if it doesn't exist yet.
    $_REQUEST[Files::ABSOLUTE_DIRECTORY_PATH_LIST] = array($strDataDir);
    $_REQUEST[Files::WAREHOUSE] = true;
    $_REQUEST[Files::PROJECT_NAME] = $oProject->getName();
    $oModel->makeDirectory();
    
    $_REQUEST[Files::PROJECT_ID] = $oProject->getId();
    $_REQUEST[Files::EXPERIMENT_ID] = $oExperiment->getId();
    $_REQUEST[Files::PARENT_DIV] = "browser";
    $_REQUEST[Files::CHILD_DIV] = "divId";
    
    $this->assignRef( "mod_warehouseupload_drawings", ComponentHtml::getModule("mod_warehouseupload") );

    $iEntityViews = $oModel->getEntityPageViews(3, $oExperiment->getId());
    $iEntityDownloads = $oModel->getEntityDownloads(3, $oExperiment->getId());

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    JFactory::getApplication()->getPathway()->addItem($oExperiment->getProject()->getName(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId());
    JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiments");
    JFactory::getApplication()->getPathway()->addItem($oExperiment->getTitle(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId());
    JFactory::getApplication()->getPathway()->addItem("Data","javascript:void(0)");

    if($oExperiment){
      $_REQUEST[Experiments::SELECTED] = serialize($oExperiment);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }
    
    parent::display($tpl);
  }
  
}
?>
