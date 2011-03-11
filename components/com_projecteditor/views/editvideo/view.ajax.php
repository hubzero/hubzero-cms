<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';

class ProjectEditorViewEditVideo extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iDataFileId = JRequest::getInt("dataFileId", 0);
    $iProjectId = JRequest::getInt("projectId", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);
    $iIndex = JRequest::getInt("index", 0);
    $iDisplay = JRequest::getInt("display", 25);
    $strReturnUrl = JRequest::getString("return", "");

    $strReturnUrl = $this->getReturnUrl($strReturnUrl, $iIndex, $iDisplay);

    if(!$iDataFileId){
      echo "Please select a data file.";
      return;
    }
    
    $this->assignRef("dataFileId", $iDataFileId);
    
    
    /* @var $oModel ProjectEditorModelEditVideo */
    $oModel =& $this->getModel();

    /* @var $oDataFile DataFile */
    $oDataFile = $oModel->getDataFileById($iDataFileId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);

    $strPath = $oDataFile->getFriendlyPath();
    $this->assignRef("path", $strPath);
    
    $strUsageTypeArray = array("Video-Frames", "Video-Movies");
    $oEntityTypeArray = $oModel->findUsageTypeList($strUsageTypeArray);
    $_REQUEST[EntityTypePeer::TABLE_NAME] = serialize($oEntityTypeArray);

    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);

    if(!StringHelper::hasText($strReturnUrl)){
    $strReturnUrl = "/warehouse/projecteditor/project/$iProjectId/projectvideos";
    if($iExperimentId){
      $strReturnUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/videos"; 
    }
    }
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