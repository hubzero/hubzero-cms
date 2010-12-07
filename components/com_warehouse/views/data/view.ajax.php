<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewData extends JView{

  function display($tpl = null){
    $strPathArray = null;
    $oRepetition = null;

    $strReferer = JRequest::getVar("referer");
    $this->assignRef("referer", $strReferer);

    $strForm = JRequest::getVar("form", "frmData");
    $this->assignRef("strFormId", $strForm);

    $strTarget = JRequest::getVar("target", "dataList");
    $this->assignRef("strTarget", $strTarget);

    $oDataModel =& $this->getModel();

    $iRepetitionId = JRequest::getVar("id");
    $strPath = JRequest::getVar("path","");
    if(strlen($strPath)==0){

      //look up the repetition object and get its name
      $oRepetition = $oDataModel->getRepetitionById($iRepetitionId);
//      $strName = trim($oRepetition->getName());
//
//      //get a list of files by rep_id and examine the first element
//      $oRepetitionArray = $oDataModel->findDataByRepetition($iRepetitionId);
//      $oRepetition = $oRepetitionArray[0];
//
//      //get the position of the path up to the name
//      $iIndex = strpos(trim($oRepetition->getPath()), $strName);
//
//      //only consider the path up to the name
//      $strPath = substr($oRepetition->getPath(), 0, $iIndex + strlen($strName));
      $strPath = $oRepetition->getPathname();
    }

    $iProjectId = 0;
    $iExperimentId = 0;

    $oDataFileArray = $oDataModel->findByDirectory($strPath);
    //echo count($oDataFileArray)."<br>";
    //echo "path: ".$strPath."<br>";
    if(empty ($oDataFileArray)){
      //get parent directory if the current has no files
      $strDirectoryArray = explode("/", $strPath);
      array_pop($strDirectoryArray);
      $strParentPath = implode("/", $strDirectoryArray);
      $oDataFileArray = $oDataModel->findByDirectory($strParentPath);
    }

    /* @var $oDataFile DataFile */
    $oDataFile = $oDataFileArray[0];

    /* @var $oDataFileLink DataFileLink */
    $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());
    $iProjectId = $oDataFileLink->getProject()->getId();
    if($oDataFileLink->getExperiment()){
      $iExperimentId = $oDataFileLink->getExperiment()->getId();
    }

    /*
    if(!empty ($oDataFileArray)){
      $oDataFile = $oDataFileArray[0];

      $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());
      $iProjectId = $oDataFileLink->getProject()->getId();
      if($oDataFileLink->getExperiment()){
        $iExperimentId = $oDataFileLink->getExperiment()->getId();
      }
    }else{
      //get parent directory
      $strDirectoryArray = explode("/", $strPath);
      array_pop($strDirectoryArray);
      $strParentPath = implode("/", $strDirectoryArray);
      $oDataFileArray = $oDataModel->findByDirectory($strParentPath);
      if(!empty ($oDataFileArray)){
        $oDataFile = $oDataFileArray[0];

        $oDataFileLink = DataFileLinkPeer::retrieveByPK($oDataFile->getId());
        $iProjectId = $oDataFileLink->getProject()->getId();
        if($oDataFileLink->getExperiment()){
          $iExperimentId = $oDataFileLink->getExperiment()->getId();
        }
      }
    }
    */

    //variable for link
    $this->assignRef( "strCurrentPath", $strPath );

    //variables for form
    $this->assignRef( "iProjectId", $iProjectId );
    $this->assignRef( "iExperimentId", $iExperimentId );

    //variables needed for module...
    $strType = "Ajax";
    JRequest::setVar('projid', $iProjectId);
    JRequest::setVar('path', $strPath);
    JRequest::setVar('type', $strType);

    $strAddressBarUrl = "/warehouse/experiment/$iExperimentId/projecct/$iProjectId";
    $strReturnURL = $oDataModel->getReturnURL($strAddressBarUrl);
    $this->assignRef( "warehouseURL", $strReturnURL );
    JRequest::setVar('warehouseURL', $strReturnURL);

    $this->assignRef( "mod_warehousefiles", ComponentHtml::getModule("mod_warehousefiles") );

    parent::display($tpl);
  }//end display


}

?>