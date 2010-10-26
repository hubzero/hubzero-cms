<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';

class ProjectEditorViewSetup extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelSetup */
    $oModel =& $this->getModel();

    $iProjectId = JRequest::getVar('projid');
    $this->assignRef( "iProjectId", $iProjectId );

    $oExperiment = null;
    $iExperimentId = JRequest::getInt('experimentId',0);
    if($iExperimentId){
      $oExperiment = $oModel->getExperimentById($iExperimentId);
      $_SESSION[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);
    }else{
      $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
    }

    //get the user
    $this->assignRef( "oUser", $oModel->getCurrentUser() );
    
    //get the tabs to display on the page
    $strTabArray = $oModel->getTabArray();
    $strTabHtml = $oModel->getTabs( "warehouse", $iProjectId, $strTabArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'materials');

    $strSubTabArray = array("About", "Setup", "Data", "Other", "Review");
    $strSubTabHtml = $oModel->getSubTabs( "warehouse/projecteditor/experiment", $iProjectId, $strSubTabArray, $strSubTab );
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    $strSpecimenType = (isset($_REQUEST["specimenType"])) ? $_REQUEST["specimenType"] : "";
    $this->assignRef( "strSpecimenType", $strSpecimenType );

    $oMaterialTypeArray = $oModel->findMaterialTypes();
    $_REQUEST[MaterialTypePeer::TABLE_NAME] = serialize($oMaterialTypeArray);

    $strMaterial = (isset($_REQUEST["material"])) ? $_REQUEST["material"] : "Material name";
    $this->assignRef("strMaterial", $strMaterial);

    $strMaterialType = (isset($_REQUEST["materialType"])) ? $_REQUEST["materialType"] : "Material type";
    $this->assignRef("strMaterialType", $strMaterialType);

    $strMaterialDesc = (isset($_REQUEST["materialDesc"])) ? $_REQUEST["materialDesc"] : "Material description";
    $this->assignRef("strMaterialDesc", $strMaterialDesc );

    //get the current materials
    $strMaterialHTML = "Added Materials";
    $oMaterialArray = $oModel->findMaterialsByExperiment($oExperiment);
    //echo sizeof($oMaterialArray)."<br>";
    if(!empty($oMaterialArray)){
      $strMaterialHTML = $oModel->findMaterialsByExperimentHTML($oMaterialArray, $iProjectId, $oExperiment->getId());
    }
    $this->assignRef( "materialInfo", $strMaterialHTML );

//    $_REQUEST[Files::REQUEST_TYPE] = Files::DRAWING;
//    $_REQUEST[Files::SEARCH_CONDITION] = "Drawing";
//    $this->assignRef( "mod_warehouseupload_drawings", ComponentHtml::getModule("mod_warehouseupload") );

//    $_REQUEST[Files::REQUEST_TYPE] = Files::DRAWING;
//    $_REQUEST[Files::SEARCH_CONDITION] = "Drawing";
//    $this->assignRef( "mod_warehouseupload_images", ComponentHtml::getModule("mod_warehouseupload") );

    $_REQUEST[Files::CURRENT_DIRECTORY] = "/nees/home/NEES-2005-0022.groups/Experiment-2/Documentation/Drawings_Sensors";
    $_REQUEST[Files::REQUEST_TYPE] = Files::DRAWING;
    $_REQUEST["DIV_ID"] = "divId";
    $this->assignRef( "mod_warehouseupload_drawings", ComponentHtml::getModule("mod_warehouseupload") );

    JFactory::getApplication()->getPathway()->addItem("Create Experiment","/projecteditor/experiment/".$iProjectId);

    parent::display($tpl);
  }
  
}
?>
