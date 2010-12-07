<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once 'api/org/nees/html/TabHtml.php';
require_once 'api/org/nees/static/Experiments.php';
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/EntityActivityLogPeer.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/security/Authorizer.php';

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

//        $this->m_oTabArray = array("Project", "Experiments", "Data", "Team Members", "More");
//        $this->m_oTabViewArray = array("project", "experiments", "data", "members", "more");

        $this->m_oTabArray = array("Project", "Experiments", "Team Members", "File Browser");
        $this->m_oTabViewArray = array("project", "experiments", "members", "filebrowser");

        $this->m_oSearchTabArray = array("Search", "Enhanced Projects");
        $this->m_oSearchTabViewArray = array("search","featured");

        $this->m_oSearchResultsTabArray = array("Results");
        $this->m_oSearchResultsTabViewArray = array("results");

        $this->m_oTreeTabArray = array("Projects");
        $this->m_oTreeTabViewArray = array("projects");
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
     * @return Returns an array of tab views for the selected warehouse
     */
    public function getTabViewArray() {
        return $this->m_oTabViewArray;
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
     * @return Returns an array of tabs for the search screen
     */
    public function getSearchTabViewArray() {
        return $this->m_oSearchTabViewArray;
    }

    /**
     *
     */
    public function getTreeBrowserTabArray() {
        return $this->m_oTreeTabArray;
    }

    /**
     *
     */
    public function getTreeBrowserTabViewArray() {
        return $this->m_oTreeTabViewArray;
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
     * @return Returns an array of tabs for the search results
     */
    public function getSearchResultsTabViewArray() {
        return $this->m_oSearchResultsTabViewArray;
    }

    /**
     *
     * @return strTabs in html format
     */
    public function getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strTabViewArray, $p_strActive) {
        return TabHtml::getTabs($p_strOption, $p_iId, $p_strTabArray, $p_strTabViewArray, $p_strActive);
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

        $oSelectedDataFileArray = $_REQUEST['cbxDataFile'];

        while (list ($iIndex, $iSelectedDataFileId) = @each($oSelectedDataFileArray)) {
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

        //delete old download files
        FileHelper::downloadCleanup("/tmp");

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

    /**
     * Updates the current page view count and returns the new count.
     * @param int $p_iEntityTypeId
     * @param int $p_iEntityId
     * @return int
     */
    public function getPageViews($p_iEntityTypeId, $p_iEntityId){
      $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID] = $p_iEntityTypeId;
      $_REQUEST[EntityActivityLogPeer::ENTITY_ID] = $p_iEntityId;

      JPluginHelper::importPlugin( 'project', 'entityactivitylog' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      //set the page view count
      $oDispatcher->trigger('onUpdateViews',$strParamArray);

      //get the page view count
      $oResultsArray = $oDispatcher->trigger('onViews',$strParamArray);

      //return the updated count
      return $oResultsArray[0];
    }

    /**
     * Updates the current page view count and returns the new count.
     * @param int $p_iEntityTypeId
     * @param int $p_iEntityId
     * @return int
     */
    public function getEntityPageViews($p_iEntityTypeId, $p_iEntityId){
      $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID] = $p_iEntityTypeId;
      $_REQUEST[EntityActivityLogPeer::ENTITY_ID] = $p_iEntityId;

      JPluginHelper::importPlugin( 'project', 'entityactivitylog' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      //get the page view count
      $oResultsArray = $oDispatcher->trigger('onViews',$strParamArray);

      //return the updated count
      return $oResultsArray[0];
    }

    /**
     * Updates the current entity download count.
     * @param int $p_iEntityTypeId
     * @param int $p_iEntityId
     * @return int
     */
    public function updateEntityDownloads($p_iEntityTypeId, $p_iEntityId){
      $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID] = $p_iEntityTypeId;
      $_REQUEST[EntityActivityLogPeer::ENTITY_ID] = $p_iEntityId;

      JPluginHelper::importPlugin( 'project', 'entityactivitylog' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      //set the page view count
      $oDispatcher->trigger('onUpdateDownloads',$strParamArray);
    }

    /**
     * Gets the current entity download count.
     * @param int $p_iEntityTypeId
     * @param int $p_iEntityId
     * @return int
     */
    public function getEntityDownloads($p_iEntityTypeId, $p_iEntityId){
      $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID] = $p_iEntityTypeId;
      $_REQUEST[EntityActivityLogPeer::ENTITY_ID] = $p_iEntityId;

      JPluginHelper::importPlugin( 'project', 'entityactivitylog' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      //set the page view count
      $oResultsArray = $oDispatcher->trigger('onDownloads',$strParamArray);

      //return the value
      return $oResultsArray[0];
    }

    /**
   * Returns an array of arrays.
   * array[0] = roles
   * array[1] = permissions
   *
   * @param int $p_iPersonId
   * @param int $p_iEntityId
   * @param int $p_iEntityTypeId
   * @return array
   */
  public function getMemberRoleAndPermissionsCollection($p_iPersonId, $p_iEntityId, $p_iEntityTypeId){
    $strPermissionsArray = array();
    $oRoleArray = array();

    $oPersonEntityRoleArray = PersonEntityRolePeer::findByPersonEntityEntityType($p_iPersonId, $p_iEntityId, $p_iEntityTypeId);
    /* @var $oPersonEntityRole as PersonEntityRole */
    foreach($oPersonEntityRoleArray as $oPersonEntityRole){
      $oRole = $oPersonEntityRole->getRole();
      array_push($oRoleArray, $oRole);

      //get the permission string for this role
      $strPermissions = $oPersonEntityRole->getRole()->getDefaultPermissions()->toString();

      //explode string into array and push each element into temp array
      $strExplodedPermissionsArray = explode(",", $strPermissions);
      foreach($strExplodedPermissionsArray as $strPermission){
        if(!array_search($strPermission, $strPermissionsArray)){
          array_push($strPermissionsArray, $strPermission);
        }
      }

    }//end foreach $oPersonEntityRoleArray

    return array($oRoleArray, $strPermissionsArray);
  }

  public function getSpecimenByProjectId($p_iProjectId){
    return SpecimenPeer::retrieveByPK($p_iProjectId);
  }

  /**
   *
   * @param Authorizer $p_oAuthorizer
   * @param Project $p_oProject
   * @return array
   */
  public function getViewableExperimentsByProject($p_oAuthorizer, $p_oProject){
    $oViewableEntityArray = array(Experiments::SHOW => array(), Experiments::HIDE => array());

    $oExperimentArray = $p_oProject->getExperiments();


    foreach($oExperimentArray as $oExperiment){
      /* @var $oExperiment Experiment */
      $bDeleted = $oExperiment->getDeleted();
      if($p_oAuthorizer->canView($oExperiment) && !$bDeleted){
        array_push($oViewableEntityArray[Experiments::SHOW], $oExperiment);
      }else{
        array_push($oViewableEntityArray[Experiments::HIDE], $oExperiment->getId());
      }
    }

    return $oViewableEntityArray;
  }

  /**
   *
   * @param int $p_iProjectId
   * @return Project
   */
  public function getProjectById($p_iProjectId){
    return ProjectPeer::retrieveByPK($p_iProjectId);
  }

  /**
   *
   * @param int $p_iExperimentId
   * @return Experiment
   */
  public function getExperimentById($p_iExperimentId){
    return ExperimentPeer::retrieveByPK($p_iExperimentId);
  }

  /**
   *
   * @param int $p_iDataFileId
   * @return DataFile
   */
  public function getDataFileById($p_iDataFileId){
    return DataFilePeer::retrieveByPK($p_iDataFileId);
  }

  public static function getReturnURL($p_strCurrentUrl=""){
    // Get current page path and querystring
    $uri  =& JURI::getInstance();
    $redirectUrl = $uri->toString(array('path', 'query'));
    if(StringHelper::hasText($p_strCurrentUrl)){
      $redirectUrl = $p_strCurrentUrl;
    }

    //echo "url=$redirectUrl";

    // Code the redirect URL
    $redirectUrl = base64_encode($redirectUrl);
    $redirectUrl = 'return=' . $redirectUrl;

    return $redirectUrl;
  }

}
?>