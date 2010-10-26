<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once 'lib/data/EquipmentPeer.php';
require_once 'lib/data/Material.php';
require_once 'lib/data/MaterialType.php';
require_once 'lib/data/MaterialTypeProperty.php';
require_once 'lib/data/Experiment.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewPreviewExp extends JView{
	
  function display($tpl = null){
    $iProjectId = JRequest::getVar('projid');
  	
    $oPreviewModel =& $this->getModel();
    
    //get the tabs to display on the page
    $strTabArray = $oPreviewModel->getTabArray();
    $strTabHtml = $oPreviewModel->getTabs( "warehouse", $iProjectId, $strTabArray, "experiments" );
    $this->assignRef( "strTabs", $strTabHtml );
    
    $this->assignRef( "oUser", $oPreviewModel->getCurrentUser() );

    $strFilmstrip = $oPreviewModel->getFilmstripHTML(array());
    $this->assignRef( "strFilmstrip", $strFilmstrip );
	
    $oProject = $oPreviewModel->getProjectById($iProjectId);
	
    $oExperiment = unserialize($_SESSION[ExperimentPeer::TABLE_NAME]);
    $this->assignRef("strTitle", $oExperiment->getTitle());
    $this->assignRef("strDesc", $oPreviewModel->getDisplayDescription($oExperiment->getDescription()));

    $strDates = strftime("%B %d, %Y", strtotime($oExperiment->getStartDate()));
    $strEndDate = ($oExperiment->getEndDate()) ? $oExperiment->getEndDate() : "";
    if($strEndDate != ""){
      $strDates .= " - ".strftime("%B %d, %Y", strtotime($strEndDate));
    }else{
      $strDates .= " - Present";
    }
    $this->assignRef("strDates", $strDates);

    $oExperimentFacilityArray = $oExperiment->getExperimentFacilitys();
    $strExperimentFacilities = $oPreviewModel->getExperimentFacilitiesHTML($oExperimentFacilityArray);
    $this->assignRef("strFacilities", $strExperimentFacilities);

    $strTags = JRequest::getVar("tags", "");
    $this->assignRef( "strTags", $strTags );

    $strTagArray = array();
    if(strlen($strTags) > 0){
      $strTagArray = explode(",", $strTags);
    }
    $this->assignRef( "strTagArray", $strTagArray );

    $strSpecimenName = StringHelper::EMPTY_STRING;
    $oSpecimen = unserialize($_SESSION[SpecimenPeer::TABLE_NAME]);
    if($oSpecimen){
      $strSpecimenName = $oSpecimen->getName();
    }
    $this->assignRef( "strSpecimen", $strSpecimenName );

    $_REQUEST[EquipmentPeer::TABLE_NAME] = $_SESSION['EQUIPMENT_ARRAY'];
    $this->assignRef( "mod_warehouseequipment", ComponentHtml::getModule("mod_warehouseequipment") );

    $strAccess = $oPreviewModel->getHubAccessSettings($oExperiment->getView());
    $this->assignRef( "strAccess", $strAccess );

    JFactory::getApplication()->getPathway()->addItem("Create Experiment","/projecteditor/experiment/".$iProjectId);
    JFactory::getApplication()->getPathway()->addItem("Confirm Experiment","javascript:void(0)");
    parent::display($tpl);
  }
  
  


}
?>
