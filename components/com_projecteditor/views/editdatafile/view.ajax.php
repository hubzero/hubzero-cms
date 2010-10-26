<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';

class ProjectEditorViewEditDataFile extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();

    //Incoming
    $strPath = JRequest::getString("path", "");
    $iDataFileId = JRequest::getInt("dataFileId", 0);
    $iProjectId = JRequest::getInt("projectId", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);

    //Validation
    if(!$strPath){
      echo "Please invalid file path.";
      return;
    }

    if(!$iDataFileId){
      echo "Please select a data file.";
      return;
    }
    
    $this->assignRef("dataFileId", $iDataFileId);
    $this->assignRef("path", $strPath);
    
    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel();

    /* @var $oDataFile DataFile */
    $oDataFile = $oModel->getDataFileById($iDataFileId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
    
    $strUsageTypeArray = array("Drawing", "Drawing-Setup", "Drawing-Sensor", "Drawing-Specimen", "Experiment Image", "Film Strip", "General Photo", "Project Image");
    $oEntityTypeArray = $oModel->findUsageTypeList($strUsageTypeArray);
    $_REQUEST[EntityTypePeer::TABLE_NAME] = serialize($oEntityTypeArray);

    $strToolArray = $oModel->findOpeningTools();
    $this->assignRef( "strToolArray", $strToolArray);

    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);

    parent::display();
  }
  
}

?>