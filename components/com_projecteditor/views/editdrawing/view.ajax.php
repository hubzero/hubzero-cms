<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'api/org/nees/lib/interface/Data.php';

class ProjectEditorViewEditDrawing extends JView{

  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iDataFileId = JRequest::getInt("dataFileId", 0);
    if(!$iDataFileId){
      echo "File not selected.";
      return;
    }

    $iProjectId = JRequest::getInt("projectId", 0);
    if(!$iProjectId){
      echo "Project not provided.";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not provided";
      return;
    }

    $this->assignRef("dataFileId", $iDataFileId);
    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);

    $iIndex = JRequest::getInt("index", 0);
    $iDisplay = JRequest::getInt("display", 25);
    $strReturnUrl = JRequest::getString("return", "");

    $strReturnUrl = $this->getReturnUrl($strReturnUrl, $iIndex, $iDisplay);

    /* @var $oModel ProjectEditorModelEditDrawing */
    $oModel =& $this->getModel();

    $oProject = $oModel->getProjectById($iProjectId);
    $oExperiment = $oModel->getExperimentById($iExperimentId);

    /* @var $oDataFile DataFile */
    $oDataFile = $oModel->getDataFileById($iDataFileId);
    if(!$oDataFile){
      echo "File not found.";
      return;
    }
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
    $_REQUEST[EntityTypePeer::TABLE_NAME] = serialize($oModel->getDataFileUsageTypes("Drawing"));

    //$strDrawingsDir = "/nees/home/".$oProject->getName().".groups/".$oExperiment->getName()."/Documentation/Drawings";
    $strDrawingsDir = $oDataFile->getPath();
    $this->assignRef("strDrawingsDir", $strDrawingsDir);

    $this->assignRef( "strReturnUrl", $strReturnUrl );

    parent::display();
  }

  private function getReturnUrl($strReturnUrl, $iIndex, $iDisplay){
    if(StringHelper::hasText($strReturnUrl)){
      if(StringHelper::contains($strReturnUrl, "\?")){
        if(!StringHelper::contains($strReturnUrl, "index=") && !StringHelper::contains($strReturnUrl, "limit=")){
          $strReturnUrl .= "&index=$iIndex&limit=$iDisplay";
        }else{
          if(!StringHelper::contains($strReturnUrl, "index=")){
            $strReturnUrl .= "&index=$iIndex";
          }
          if(!StringHelper::contains($strReturnUrl, "limit=")){
            $strReturnUrl .= "&limit=$iDisplay";
          }
        }
      }else{
        $strReturnUrl .= "?index=$iIndex&limit=$iDisplay";
      }
    }
    return $strReturnUrl;
  }

}

?>