<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once 'api/org/nees/html/TabHtml.php';

class WarehouseModelBase extends JModel{
	
  private $m_oTabArray;
  private $m_oSearchTabArray;
  private $m_oSearchResultsTabArray;
  
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
	$this->m_oTabArray = array("Project", "Experiments", "Data", "Team Members", "More");
	$this->m_oSearchTabArray = array("Featured", "Search");
	$this->m_oSearchResultsTabArray = array("Results");
	$this->m_oTreeTabArray = array("Projects");
  }
  
  /**
   * 
   * @return Returns an array of tabs for the selected warehouse
   */
  public function getTabArray(){
  	return $this->m_oTabArray;
  } 
  
  /**
   * 
   * @return Returns an array of tabs for the search screen
   */
  public function getSearchTabArray(){
    return $this->m_oSearchTabArray;
  }
  
  /**
   * 
   */
  public function getTreeBrowserTabArray(){
  	return $this->m_oTreeTabArray;
  }
  
  /**
   * 
   * @return Returns an array of tabs for the search results
   */
  public function getSearchResultsTabArray(){
    return $this->m_oSearchResultsTabArray;
  }
  
  /**
   * 
   * @return strTabs in html format
   */
  public function getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive){
  	return TabHtml::getTabs( $p_strOption, $p_iId, $p_strTabArray, $p_strActive );
  }
  
  /**
   * 
   * @return strTabs in html format
   */
  public function getSubTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive){
  	return TabHtml::getSubTabs( $p_strOption, $p_iId, $p_strTabArray, $p_strActive );
  }
  
  /**
   * 
   * @return strTabs in html format
   */
  public function getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized){
  	return TabHtml::getTreeTab( $p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized );
  }
  
  public function computeLowerLimit($p_iPageIndex, $p_iDisplay){
  	if($p_iPageIndex==0){
  	  return 1;	
  	}
  	return ($p_iDisplay * $p_iPageIndex) + 1;
  }
  
  public function computeUpperLimit($p_iPageIndex, $p_iDisplay){
  	if($p_iPageIndex==0){
  	  return $p_iDisplay;	
  	}
  	return $p_iDisplay * ($p_iPageIndex + 1);
  }
  
  public function getTrialById($p_iTrialId){
  	return TrialPeer::retrieveByPK($p_iTrialId);
  }
  
  public function getRepetitionById($p_iRepetitionId){
  	return RepetitionPeer::retrieveByPK($p_iRepetitionId);
  }
  
  public function findDataFileByType($p_strFileType, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
  	return DataFilePeer::findDataFileByType($p_strFileType, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileByDrawing($p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
  	return DataFilePeer::findDataFileByDrawing($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileByEntityType($p_strEntityTypeNTableName, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
  	return DataFilePeer::findDataFileByEntityType($p_strEntityTypeNTableName, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findDataFileByMimeTypeCount($p_iProjectId, $p_iExperimentId, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByMimeTypeCount($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);	
  }
	
  public function getMysqlUserByUsername0($p_strUsername){
  	if (empty($p_strUsername))
	  return false;

	$db = &JFactory::getDBO();
		
	$query = "SELECT id FROM #__users WHERE username=" . $db->Quote($p_strUsername);
		
	$db->setQuery($query);

	$result = $db->loadResultArray();
		
	if (empty($result))
	  return false;
			
	return $result;	
  }
  
  public function getMysqlUserByUsername($p_strUsername){
  	if (empty($p_strUsername))
	  return false;

	$oUser =& JFactory::getUser($p_strUsername);
	return $oUser;	
  }
  
//  public function downloadFiles(){
//  	$oDataFileIdArray = array();
//  	
//  	$oSelectedDataFileArray = $_POST['cbxDataFile'];
//  	
//  	while (list ($iIndex,$iSelectedDataFileId) = @each ($oSelectedDataFileArray)) {
//	  array_push($oDataFileIdArray, $iSelectedDataFileId);
//	}//end while $oSelectedDataFileArray
//	
//	if(empty($oDataFileIdArray)){
//	  throw new Exception("Data file(s) not selected");
//	}
//	
//	//create a temporary directory for archiving the files
//	$strCurrentTimestamp = time();
//	$strDownloadDirectory = "/tmp/neeshub-download-".$strCurrentTimestamp;
//	exec("mkdir ".$strDownloadDirectory, $output);
//	
//	//copy the selected files to the temp directory 
//	$oDataFileArray = DataFilePeer::retrieveByPKs($oDataFileIdArray);
//	foreach($oDataFileArray as $oDataFile){
//	  $strFileToCopy = $oDataFile->getPath()."/".$oDataFile->getName();
//	  $strCommand = "cp $strFileToCopy $strDownloadDirectory";
//	  if($oDataFile->isDirectory()){
//	    $strCommand = "cp -R $strFileToCopy $strDownloadDirectory";
//	  }
//	  exec($strCommand, $output);
//	}
//	
//	//create the archive under /tmp
//	$oUser =& JFactory::getUser();
//	$strUsername = $oUser->username;
//	if(strlen($strUsername)===0){
//	  $strUsername = "guest";	
//	}
//	$strArchiveFile = "/tmp/$strUsername-neeshub-download-$strCurrentTimestamp.tar.gz";
//	$strCommandToExecute = "tar -czPf $strArchiveFile $strDownloadDirectory";
//	exec($strCommandToExecute, $output);
//	
//	//download the file
//	FileHelper::download($strArchiveFile);
//  }

  public function downloadTarBall(){
  	$oDataFileIdArray = array();
  	
  	$oSelectedDataFileArray = $_POST['cbxDataFile'];
  	
  	while (list ($iIndex,$iSelectedDataFileId) = @each ($oSelectedDataFileArray)) {
	  echo "array ".$iSelectedDataFileId." <br>";
	  array_push($oDataFileIdArray, $iSelectedDataFileId);
	}//end while $oSelectedDataFileArray
	
	if(empty($oDataFileIdArray)){
	  throw new Exception("Data file(s) not selected");
	}
	
	//create a temporary directory for archiving the files
	$strCurrentTimestamp = time();
	$strFilesToCopy = "";
	
	//copy the selected files to the temp directory 
	$oDataFileArray = DataFilePeer::retrieveByPKs($oDataFileIdArray);
	foreach($oDataFileArray as $oDataFile){
	  $strFilesToCopy .= $oDataFile->getPath()."/".$oDataFile->getName()." ";
	}
	
	/*
	 * create the archive under /tmp.
	 * if user is logged in, use their username.
	 * otherwise, use "guest".
	 */
	$oUser =& JFactory::getUser();
	$strUsername = $oUser->username;
	if(strlen($strUsername)===0){
	  $strUsername = "guest";	
	}
	$strArchiveFile = "/tmp/$strUsername-neeshub-$strCurrentTimestamp-download.tar.gz";
    $strCommandToExecute = "tar -pzcvf $strArchiveFile $strFilesToCopy";
    exec($strCommandToExecute, $output);
	
	//download the file
	return FileHelper::downloadTarBall($strArchiveFile);
  }
  
  
  	public function getSearchTabs($activeTabIndex)
	{
		
		$tabArrayLinks = array("featured",
			"search");
	
		$tabArrayText = array("Featured Projects",
			"Search");
		
		$strHtml  = '<div id="sub-menu">';
		$strHtml .= '<ul>';
		$i = 0;
		
		foreach ($tabArrayText as $tabEntryText){
			if ($tabEntryText != '') {
				$strHtml .= '<li id="sm-'.$i.'"';
				$strHtml .= ($i==$activeTabIndex) ? ' class="active"' : '';
				$strHtml .= '><a class="tab" rel="' . $tabEntryText . '" href="' . JRoute::_('index.php?option=com_warehouse&view="' . strtolower($tabArrayLinks[$i])) . '"><span>' . $tabEntryText . '</span></a></li>';
				$i++;
			}
		}
		
		$strHtml .= '</ul>';
		$strHtml .= '<div class="clear"></div>';
		$strHtml .= '</div><!-- / #sub-menu -->';

		return $strHtml;
    }		
  
  
  
  
  
//  public function computeLowerLimit($p_iPageIndex, $p_iDisplay){
//  	if($p_iPageIndex==0){
//  	  return 1;	
//  	}
//  	return ($p_iPageIndex * $p_iDisplay) + 1;
//  }
//  
//  public function computeUpperLimit($p_iPageIndex, $p_iDisplay){
//  	return $p_iDisplay * ($p_iPageIndex+1);
//  }
}

?>