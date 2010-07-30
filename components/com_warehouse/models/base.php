<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once 'api/org/nees/html/TabHtml.php';
require_once 'lib/data/PersonPeer.php';

class WarehouseModelBase extends JModel {

    private $m_oTabArray;
    private $m_oSearchTabArray;
    private $m_oSearchResultsTabArray;

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct() {
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
    public function getTabArray() {
        return $this->m_oTabArray;
    }

    /**
     *
     * @return Returns an array of tabs for the search screen
     */
    public function getSearchTabArray() {
        return $this->m_oSearchTabArray;
    }

    /**
     *
     */
    public function getTreeBrowserTabArray() {
        return $this->m_oTreeTabArray;
    }

    /**
     *
     * @return Returns an array of tabs for the search results
     */
    public function getSearchResultsTabArray() {
        return $this->m_oSearchResultsTabArray;
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive) {
        return TabHtml::getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive);
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getSubTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive) {
        return TabHtml::getSubTabs($p_strOption, $p_iId, $p_strTabArray, $p_strActive);
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized) {
        return TabHtml::getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive, $minimized);
    }

    public function computeLowerLimit($p_iPageIndex, $p_iDisplay) {
        if ($p_iPageIndex == 0) {
            return 1;
        }
        return ($p_iDisplay * $p_iPageIndex) + 1;
    }

    public function computeUpperLimit($p_iPageIndex, $p_iDisplay) {
        if ($p_iPageIndex == 0) {
            return $p_iDisplay;
        }
        return $p_iDisplay * ($p_iPageIndex + 1);
    }

    public function getCurrentUser() {
      $oUser =& JFactory::getUser();
      return $oUser;
    }

    public function getOracleUserByUsername($p_strUsername){
      if(empty($p_strUsername)){
        return null;
      }

      if(empty($p_strUsername)){
        return null;
      }

      return PersonPeer::findByUserName($p_strUsername);
    }

    public function getTrialById($p_iTrialId) {
        return TrialPeer::retrieveByPK($p_iTrialId);
    }

    public function getRepetitionById($p_iRepetitionId) {
        return RepetitionPeer::retrieveByPK($p_iRepetitionId);
    }

    public function findDataFileByType($p_strFileType, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0) {
        return DataFilePeer::findDataFileByType($p_strFileType, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
    }

    public function findDataFileByDrawing($p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0) {
        return DataFilePeer::findDataFileByDrawing($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
    }

    public function findDataFileByEntityType($p_strEntityTypeNTableName, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0) {
        return DataFilePeer::findDataFileByEntityType($p_strEntityTypeNTableName, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
    }

    public function findDataFileByMimeTypeCount($p_iProjectId, $p_iExperimentId, $p_iTrialId=0, $p_iRepetitionId=0) {
        return DataFilePeer::findDataFileByMimeTypeCount($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
    }

    public function getMysqlUserByUsername($p_strUsername) {
        if (empty($p_strUsername))
            return false;

        $oUser = & JFactory::getUser($p_strUsername);
        return $oUser;
    }

    public function downloadTarBall() {
        $oDataFileIdArray = array();

        $oSelectedDataFileArray = $_POST['cbxDataFile'];

        while (list ($iIndex, $iSelectedDataFileId) = @each($oSelectedDataFileArray)) {
            echo "array " . $iSelectedDataFileId . " <br>";
            array_push($oDataFileIdArray, $iSelectedDataFileId);
        }//end while $oSelectedDataFileArray

        if (empty($oDataFileIdArray)) {
            throw new Exception("Data file(s) not selected");
        }

        //create a temporary directory for archiving the files
        $strCurrentTimestamp = time();
        $strFilesToCopy = "";

        //copy the selected files to the temp directory
        $oDataFileArray = DataFilePeer::retrieveByPKs($oDataFileIdArray);
        foreach ($oDataFileArray as $oDataFile) {
            $strFilesToCopy .= $oDataFile->getPath() . "/" . $oDataFile->getName() . " ";
        }

        /*
         * create the archive under /tmp.
         * if user is logged in, use their username.
         * otherwise, use "guest".
         */
        $oUser = & JFactory::getUser();
        $strUsername = $oUser->username;
        if (strlen($strUsername) === 0) {
            $strUsername = "guest";
        }
        $strArchiveFile = "/tmp/$strUsername-neeshub-$strCurrentTimestamp-download.tar.gz";
        $strCommandToExecute = "tar -pzcvf $strArchiveFile $strFilesToCopy";
        exec($strCommandToExecute, $output);

        //download the file
        return FileHelper::downloadTarBall($strArchiveFile);
    }

    public function getSearchTabs($activeTabIndex) {

        $tabArrayLinks = array("featured",
            "search");

        $tabArrayText = array("Featured Projects",
            "Search");

        $strHtml = '<div id="sub-menu">';
        $strHtml .= '<ul>';
        $i = 0;

        foreach ($tabArrayText as $tabEntryText) {
            if ($tabEntryText != '') {
                $strHtml .= '<li id="sm-' . $i . '"';
                $strHtml .= ( $i == $activeTabIndex) ? ' class="active"' : '';
                $strHtml .= '><a class="tab" rel="' . $tabEntryText . '" href="' . JRoute::_('index.php?option=com_warehouse&view="' . strtolower($tabArrayLinks[$i])) . '"><span>' . $tabEntryText . '</span></a></li>';
                $i++;
            }
        }

        $strHtml .= '</ul>';
        $strHtml .= '<div class="clear"></div>';
        $strHtml .= '</div><!-- / #sub-menu -->';

        return $strHtml;
    }

    public function getDisplayDescription($p_oDescriptionClob) {
        $p_oDescriptionClob = nl2br($p_oDescriptionClob);
        $strReturnDescription = "";
        if (strlen($p_oDescriptionClob) > 300) {
            $strShortDescription = StringHelper::neat_trim($p_oDescriptionClob, 250);
            $strReturnDescription = <<< ENDHTML
              <div id="ProjectShortDescription">
                $strShortDescription (<a href="javascript:void(0);" onClick="document.getElementById('ProjectLongDescription').style.display='';document.getElementById('ProjectShortDescription').style.display='none';">more</a>)
              </div>
              <div id="ProjectLongDescription" style="display:none">
                $p_oDescriptionClob (<a href="javascript:void(0);" onClick="document.getElementById('ProjectLongDescription').style.display='none';document.getElementById('ProjectShortDescription').style.display='';">hide</a>)
              </div>
ENDHTML;
        } else {
            $strReturnDescription = $p_oDescriptionClob;
        }
        return $strReturnDescription;
    }

    public function resizePhotos($p_oDataFileArray) {
        return DataFilePeer::resizePhotos($p_oDataFileArray);
    }

    /**
     *
     * @param DataFile $p_oDataFile
     * @param bool $p_bIsThumbnail
     * @param bool $p_bIsDisplay
     * @return DataFile
     */
    public function scaleImage($p_oDataFile, $p_bIsThumbnail=false, $p_bIsDisplay=false) {
      //invoke the upload plugin
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);
      $bResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);

      /*
       * update photo for view...
       * show thumbnail or display where appropriate
       */
      $bImageScaled = $bResultsArray[0];
      if($bImageScaled){
        //echo $bImageScaled."<br>";
        $strPath = $p_oDataFile->getPath()."/".Files::GENERATED_PICS;
        if($p_bIsThumbnail){
          $strName = "thumb_".$p_oDataFile->getId()."_".$p_oDataFile->getName();
        }

        if($p_bIsDisplay){
          $strName = "display_".$p_oDataFile->getId()."_".$p_oDataFile->getName();
        }

        $p_oDataFile->setName($strName);
        $p_oDataFile->setPath($strPath);
      }

      return $p_oDataFile;
    }


    /**
     *
     * @param DataFile $p_oDataFile
     * @param bool $p_bIsThumbnail
     * @param bool $p_bIsDisplay
     * @return DataFile
     */
    public function scaleImageByWidth($p_oDataFile, $p_bIsThumbnail=false, $p_bIsDisplay=false) {
      //invoke the upload plugin
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);
      $bResultsArray = $oDispatcher->trigger('onScaleImageDataFileByWidth',$strParamArray);

      /*
       * update photo for view...
       * show thumbnail or display where appropriate
       */
      $bImageScaled = $bResultsArray[0];
      if($bImageScaled){
        //echo $bImageScaled."<br>";
        $strPath = $p_oDataFile->getPath()."/".Files::GENERATED_PICS;
        if($p_bIsThumbnail){
          $strName = "thumb_".$p_oDataFile->getId()."_".$p_oDataFile->getName();
        }

        if($p_bIsDisplay){
          $strName = "display_".$p_oDataFile->getId()."_".$p_oDataFile->getName();
        }

        $p_oDataFile->setName($strName);
        $p_oDataFile->setPath($strPath);
      }

      return $p_oDataFile;
    }

}
?>