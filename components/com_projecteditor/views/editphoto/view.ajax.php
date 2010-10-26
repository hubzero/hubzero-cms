<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';

class ProjectEditorViewEditPhoto extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $iDataFileId = JRequest::getInt("dataFileId", 0);
    $iProjectId = JRequest::getInt("projectId", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);

    if(!$iDataFileId){
      echo "Please select a data file.";
      return;
    }
    
    $this->assignRef("dataFileId", $iDataFileId);
    
    
    /* @var $oModel ProjectEditorModelEditPhoto */
    $oModel =& $this->getModel();

    /* @var $oDataFile DataFile */
    $oDataFile = $oModel->getDataFileById($iDataFileId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);

    $strPath = $oDataFile->getFriendlyPath();
    $this->assignRef("path", $strPath);
    
    $strUsageTypeArray = array("Film Strip", "General Photo");
    $oEntityTypeArray = $oModel->findUsageTypeList($strUsageTypeArray);
    $_REQUEST[EntityTypePeer::TABLE_NAME] = serialize($oEntityTypeArray);

    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);

    parent::display();
  }
  
}

?>