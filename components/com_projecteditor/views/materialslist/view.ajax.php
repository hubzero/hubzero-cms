<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class ProjectEditorViewMaterialsList extends JView {

    function display($tpl = null) {
        //get the materials
        $oMaterialsModel = & $this->getModel();

        $iProjectId = JRequest::getVar("projectId");
        $this->assignRef("projectId", $iProjectId);

        $iMaterialId = JRequest::getVar("materialId", 0);
        $this->assignRef("materialId", $iMaterialId);

        $oExperiment = null;
        $iExperimentId = JRequest::getInt('experimentId',0);
        if($iExperimentId){
          $oExperiment = $oMaterialsModel->getExperimentById($iExperimentId);
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

        $oMaterialArray = $oMaterialsModel->findMaterialsByExperiment($oExperiment);
        $strMaterialHtml = $oMaterialsModel->findMaterialsByExperimentHTML($oMaterialArray, $iProjectId, $iExperimentId, $iMaterialId);
        $this->assignRef("materialInfo", $strMaterialHtml);

        parent::display($tpl);
    }

//end display
}
?>