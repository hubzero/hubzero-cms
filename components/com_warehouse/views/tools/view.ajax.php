<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class WarehouseViewTools extends JView {

    function display($tpl = null) {
        $iRepId = JRequest::getVar("id");

        //get the trial
        /* @var $oToolModel WarehouseModelTools */
        $oToolModel = & $this->getModel();
        $oProjectExperimentArray = $oToolModel->findProjectAndExperimentByRepetition($iRepId);
        $this->assignRef("projectId", $oProjectExperimentArray["PROJ_ID"]);
        $this->assignRef("experimentId", $oProjectExperimentArray["EXP_ID"]);

        $oRepetition = $oToolModel->getRepetitionById($iRepId);

        //single out the tool files using proj/exp/trial/rep identifiers
        $oToolFileArray = $oToolModel->findDataFileByTool("inDEED", $oProjectExperimentArray["PROJ_ID"], $oProjectExperimentArray["EXP_ID"], $oRepetition->getTrialId(), $oRepetition->getId());
        
        $_REQUEST["TOOL_DATA_FILES"] = serialize($oToolFileArray);


        if (sizeof($oToolFileArray) === 0) {
            echo "0 interactive data files found.";
        }

        parent::display($tpl);
    }

//end display
}
?>