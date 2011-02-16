<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/DataFileLinkPeer.php';
require_once 'lib/data/DataFileLink.php';
require_once 'lib/data/ProjectHomepage.php';
require_once 'lib/data/ProjectHomepagePeer.php';

class ProjectEditorViewEditDocument extends JView{
	
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
    
    
    /* @var $oModel ProjectEditorModelEditDocument */
    $oModel =& $this->getModel();

    /* @var $oDataFile DataFile */
    $oDataFile = $oModel->getDataFileById($iDataFileId);
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);

    /* @var $oProjectHomepage ProjectHomepage */
    $oProjectHomepage = ProjectHomepagePeer::findByProjectIdAndDataFileId($iProjectId, $iDataFileId);
    if($oProjectHomepage){
      $_REQUEST[ProjectHomepagePeer::TABLE_NAME] = serialize($oProjectHomepage);
    }

    $strPath = $oDataFile->getPath();
    $this->assignRef("path", get_friendlyPath($strPath));
    $this->assignRef("projectId", $iProjectId);
    $this->assignRef("experimentId", $iExperimentId);
    $this->assignRef("requestType", $iRequestType);

    parent::display();
  }
  
}

?>