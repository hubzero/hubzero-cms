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
          //var_dump($oRepetition);
          
  	  $strName = trim($oRepetition->getName());
  	  
  	  //get a list of files by rep_id and examine the first element
  	  $oRepetitionArray = $oDataModel->findDataByRepetition($iRepetitionId);
  	  $oRepetition = $oRepetitionArray[0];
  	  
  	  //get the position of the path up to the name
  	  $iIndex = strpos(trim($oRepetition->getPath()), $strName);
  	  
  	  //only consider the path up to the name
  	  $strPath = substr($oRepetition->getPath(), 0, $iIndex + strlen($strName));
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
  	$oDataFileArray = array();
  	if(!StringHelper::endsWith($strPath, ".groups")){
      $oDataFileArray = $oDataModel->findByDirectory($strPath);
  	}else{
  	  $strIncludedFolderArray = array("Documentation", "Public", "Analysis");
 	  $oDataFileArray = DataFilePeer::getDocumentSummary($strPath, $strIncludedFolderArray);
  	}
  	
  	$_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFileArray);
	
    parent::display($tpl);
  }//end display
  
  
}

?>