<?php

/**
 * @see components/com_projecteditor/models/uploadform.php
 * @see modules/mod_warehouseupload
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';

class ProjectEditorViewUploadForm extends JView{

  function display($tpl = null){
    $strErrorArray = array();
    $oEntityTypeArray = null;
    $strEntityTypesHTML = "";

    /* @var $oModel ProjectEditorModelUploadForm */
    $oModel =& $this->getModel();

    //Incoming
    $iFileUploadType = JRequest::getInt("uploadType", 0);
    $strPath = JRequest::getVar("path", "");
    $iDivId = JRequest::getVar('div');
    $iProjectId = JRequest::getInt("projid", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);

    switch ($iFileUploadType){
      case Files::DRAWING:
        $oEntityTypeArray = $oModel->getDataFileUsageTypes("Drawing");
        $strEntityTypesHTML = $oModel->getDataFileUsageTypesHTML($oEntityTypeArray);
        break;
      case Files::DATA:
        $oEntityTypeArray = $oModel->findOpeningTools();
        $strEntityTypesHTML = $oModel->findOpeningToolsHTML($oEntityTypeArray);
        break;
      case Files::IMAGE:
        //$strUsageTypeArray = array("Film Strip", "General Photo");
        $strUsageTypeArray = array("Film Strip");  //More tab is gone!
        $oEntityTypeArray = $oModel->findUsageTypeList($strUsageTypeArray);
        $strEntityTypesHTML = $oModel->getDataFileUsageTypesHTML($oEntityTypeArray);
        break;
      case Files::VIDEO:
        $oEntityTypeArray = $oModel->getDataFileUsageTypes("Video");
        $strEntityTypesHTML = $oModel->getDataFileUsageTypesHTML($oEntityTypeArray, false);
        break;
      default :
        break;
    }


    $strUploadForm = $oModel->getUploadFormHTML($strPath, $iFileUploadType, $iDivId, $strEntityTypesHTML, $iProjectId, $iExperimentId);
    $this->assignRef("uploadForm", $strUploadForm);

    parent::display($tpl);
  }

}

?>