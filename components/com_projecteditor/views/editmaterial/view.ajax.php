<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/Material.php';

class ProjectEditorViewEditMaterial extends JView{
	
  function display($tpl = null){
    unset ($_SESSION["ERRORS"]);

    /* @var $oModel ProjectEditorModelMaterials */
    $oModel =& $this->getModel();

    $iProjectId = JRequest::getVar('projectId');
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
      echo ProjectEditor::EXPERIMENT_ERROR_MESSAGE;
      return;
    }

    $iMaterialId = JRequest::getInt('materialId', 0);

    //get the user
    $this->assignRef( "oUser", $oModel->getCurrentUser() );

    $oMaterialTypeArray = $oModel->findMaterialTypes();
    $_REQUEST[MaterialTypePeer::TABLE_NAME] = serialize($oMaterialTypeArray);

    $strMaterial = StringHelper::EMPTY_STRING;
    $strMaterialType = StringHelper::EMPTY_STRING;
    $strMaterialDesc = StringHelper::EMPTY_STRING;
    $strFilesHTML = StringHelper::EMPTY_STRING;
    $strMaterialProperties = "<span style='color:#999999'>Select material type above</span>";
    if($iMaterialId > 0){
      /* @var $oMaterial Material */
      $oMaterial = $oModel->getMaterialById($iMaterialId);
      $strMaterial = $oMaterial->getName();
      $strMaterialType = $oMaterial->getMaterialType()->getName();
      $iMaterialTypeId = $oMaterial->getMaterialType()->getId();
      $strMaterialDesc = $oMaterial->getDescription();
      $strMaterialProperties = $oModel->findMaterialPropertiesFormByMaterialHTML($oMaterial);

      //get the files table
      $strFilesHTML = $oModel->findMaterialFilesByExperimentHTML($oMaterial);
    }else{
      $strMaterial = (isset($_REQUEST["material"])) ? $_REQUEST["material"] : "Material name";
      $strMaterialType = (isset($_REQUEST["materialType"])) ? $_REQUEST["materialType"] : "Material type";
      $strMaterialDesc = (isset($_REQUEST["materialDesc"])) ? $_REQUEST["materialDesc"] : "Material description";
    }
    $this->assignRef( "iMaterialId", $iMaterialId );
    $this->assignRef("strMaterial", $strMaterial);
    $this->assignRef("strMaterialType", $strMaterialType);
    $this->assignRef("strMaterialDesc", $strMaterialDesc );
    $this->assignRef("strMaterialProperties", $strMaterialProperties );
    $this->assignRef("strMaterialFiles", $strFilesHTML );

    parent::display($tpl);
  }
  
}
?>
