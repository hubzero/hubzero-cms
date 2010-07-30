<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class WarehouseViewMaterials extends JView {

    function display($tpl = null) {
        //$iExperimentId = JRequest::getVar("expid");

        $iProjectId = JRequest::getVar("projectId");
        $this->assignRef("projectId", $iProjectId);

        $iExperimentId = JRequest::getVar("experimentId");
        $this->assignRef("experimentId", $iExperimentId);

        $iMaterialId = JRequest::getVar("materialId", 0);
        $this->assignRef("materialId", $iMaterialId);

        //get the materials
        $oMaterialsModel = & $this->getModel();

        $oMaterialArray = $oMaterialsModel->findMaterialsByExperiment($iExperimentId);
        $strMaterialHtml = $oMaterialsModel->findMaterialsByExperimentHTML($oMaterialArray, $iProjectId, $iExperimentId, $iMaterialId);
        $this->assignRef("materialInfo", $strMaterialHtml);

        parent::display($tpl);
    }

//end display
}
?>