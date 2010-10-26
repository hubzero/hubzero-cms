<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/Project.php';

class ProjectEditorViewDrawings extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelDrawings */
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
      echo ProjectEditor::EXPERIMENT_ERROR_MESSAGE;
      return;
    }

    $this->assignRef( "iExperimentId", $oExperiment->getId() );
    
    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strTabHtml = $oModel->getTabs( "warehouse/projecteditor", $iProjectId, $strTabArray, $strTabViewArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'data');

    $strSubTabArray = $oModel->getExperimentsSubTabArray();
    $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $strDrawingsDir = "/nees/home/".$oProject->getName().".groups/".$oExperiment->getName()."/Documentation/Drawings";
    $_REQUEST[Files::CURRENT_DIRECTORY] = $strDrawingsDir;
    $_REQUEST[Files::TOP_DIRECTORY] = $strDrawingsDir;

    $strPath = JRequest::getVar("path", "");
    if(!empty($strPath)){
      $strDrawingsArray = explode("/", $strDrawingsDir);
      $strPathArray = explode("/", $strPath);
      if(sizeof($strPathArray) > sizeof($strDrawingsArray)){
        $_REQUEST[Files::CURRENT_DIRECTORY] = $strPath;
        $_REQUEST[Files::TOP_DIRECTORY] = $strDrawingsDir;
      }
    }

    $_REQUEST[Files::REQUEST_TYPE] = Files::DRAWING;

    //Create the directory if it doesn't exist yet.
    $_REQUEST[Files::ABSOLUTE_DIRECTORY_PATH_LIST] = array($strDrawingsDir);
    $_REQUEST[Files::WAREHOUSE] = true;
    $_REQUEST[Files::PROJECT_NAME] = $oProject->getName();
    $oModel->makeDirectory();
    
    $_REQUEST[Files::PROJECT_ID] = $oProject->getId();
    $_REQUEST[Files::EXPERIMENT_ID] = $oExperiment->getId();
    $_REQUEST[Files::PARENT_DIV] = "browser";
    $_REQUEST[Files::CHILD_DIV] = "divId";
    $this->assignRef( "mod_warehouseupload_drawings", ComponentHtml::getModule("mod_warehouseupload") );

    JFactory::getApplication()->getPathway()->addItem("Create Experiment","/projecteditor/experiment/".$iProjectId);

    parent::display($tpl);
  }
  
}
?>
