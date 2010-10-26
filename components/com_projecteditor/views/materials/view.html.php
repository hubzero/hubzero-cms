<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/Material.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewMaterials extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelMaterials */
    $oModel =& $this->getModel();

    $iProjectId = JRequest::getVar('projid');
    $this->assignRef( "iProjectId", $iProjectId );

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


    $iMaterialId = JRequest::getInt('materialId', 0);

    //get the user
    $this->assignRef( "oUser", $oModel->getCurrentUser() );
    
    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabViewArray = $oModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'materials');

    $strSubTabArray = $oModel->getExperimentsSubTabArray();
    $strSubTabHtml = $oModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId/experiment", $iExperimentId, $strSubTabArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    //get the current materials
    $strMaterialHTML = "";
    $oMaterialArray = $oModel->findMaterialsByExperiment($oExperiment);
    if(!empty($oMaterialArray)){
      $strMaterialHTML = $oModel->findMaterialsByExperimentHTML($oMaterialArray, $iProjectId, $oExperiment->getId());
    }
    $this->assignRef( "materialInfo", $strMaterialHTML );

    $iEntityViews = $oModel->getEntityPageViews(3, $oExperiment->getId());
    $iEntityDownloads = $oModel->getEntityDownloads(3, $oExperiment->getId());

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);
    
    JFactory::getApplication()->getPathway()->addItem($oExperiment->getProject()->getName(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId());
    JFactory::getApplication()->getPathway()->addItem("Experiments","/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiments");
    JFactory::getApplication()->getPathway()->addItem($oExperiment->getTitle(),"/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId());
    JFactory::getApplication()->getPathway()->addItem("Materials","javascript:void(0)");

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
