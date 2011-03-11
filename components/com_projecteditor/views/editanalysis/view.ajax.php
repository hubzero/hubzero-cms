<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';

class ProjectEditorViewEditAnalysis extends JView{

  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iDataFileId = JRequest::getInt("dataFileId", 0);
    $iProjectId = JRequest::getInt("projectId", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);
    $iRequestType = JRequest::getInt("requestType", 0);

    if(!$iDataFileId){
      echo "Please select a data file.";
      return;
    }
    $this->assignRef("dataFileId", $iDataFileId);

    $iIndex = JRequest::getInt("index", 0);
    $iDisplay = JRequest::getInt("display", 25);
    $strReturnUrl = JRequest::getString("return", "");
    $strReturnUrl = $this->getReturnUrl($strReturnUrl, $iIndex, $iDisplay);


    /* @var $oModel ProjectEditorModelEditDocument */
    $oModel =& $this->getModel();

    /* @var $oDataFile DataFile */
    $oDataFile = $oModel->getDataFileById($iDataFileId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);

    $strPath = $oDataFile->getPath();
    $this->assignRef("path", get_friendlyPath($strPath));
    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);
    $this->assignRef("requestType", $iRequestType);
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