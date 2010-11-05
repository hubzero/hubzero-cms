<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewDataFiles extends JView{

  function display($tpl = null){
    $oDataFileArray = array();
    $strPathArray = null;
    $oRepetition = null;

    $strReferer = JRequest::getVar("referer");
    $this->assignRef("referer", $strReferer);

    $oDataModel =& $this->getModel();

    $iRepetitionId = JRequest::getVar("id");
    $strPath = JRequest::getVar("path","");

    if(strlen($strPath)==0){
      //look up the repetition object and get its name
      $oRepetition = $oDataModel->getRepetitionById($iRepetitionId);
      $strPath = $oRepetition->getPathname();
    }
    $this->assignRef( "strCurrentPath", $strPath );

    $strPathArray = explode("/", $strPath);
    $strBackArray = array_diff($strPathArray, array(array_pop($strPathArray)));
    $strBackPath = implode("/", $strBackArray);
    $this->assignRef( "strBackPath", $strBackPath );

    /*
     * If we are not at NEES-yyyy-#### level, use the path.
     * Otherwise, only show Documentation, Public, and Analysis.
     */
    $oDataFileArray = $oDataModel->findByDirectory($strPath);
//    $oDataFileArray = array();
//    if(!StringHelper::endsWith($strPath, ".groups")){
//      $oDataFileArray = $oDataModel->findByDirectory($strPath);
//    }else{
//      $strIncludedFolderArray = array("Documentation", "Public", "Analysis");
//      $oDataFileArray = DataFilePeer::getDocumentSummary($strPath, $strIncludedFolderArray);
//    }
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFileArray);

    /* @var $oDataFile DataFile */
    $oDataFile = $oDataFileArray[0];

    /* @var $oDataFileLink DataFileLink */
    $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());
    $iProjectId = $oDataFileLink->getProject()->getId();
    $iExperimentId = $oDataFileLink->getExperiment()->getId();

    $this->assignRef( "iProjectId", $iProjectId );
    $this->assignRef( "iExperimentId", $iExperimentId );

    parent::display($tpl);
  }//end display


}

?>