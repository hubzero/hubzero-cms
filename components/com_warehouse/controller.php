<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class WarehouseController extends JController{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();

    $this->registerTask( 'find' , 'findProjects' );
    $this->registerTask( 'filter' , 'filterSearchResults' );
    $this->registerTask( 'get' , 'getFile' );
    $this->registerTask( 'download' , 'download' );
    $this->registerTask( 'downloadsize', 'getDownloadSize' );
    $this->registerTask( 'trialdropdown' , 'getTrialDropDown' );
    $this->registerTask( 'repetitiondropdown' , 'getRepetitionDropDown' );
    $this->registerTask( 'searchfiles', 'searchFiles' );
  }	
	
  /**
   * Method to display the view
   * 
   * @access    public
   */
  function display(){
    $strViewName	= JRequest::getVar('view', 'search');
    JRequest::setVar('view', $strViewName );
    parent::display();
  }
  
  /**
   * Processes the search form on the main page.
   *
   */
  public function findProjects(){
    $dStartTime = $this->getComputeTime();

    $_REQUEST[Search::FILTER] = false;

    //set the search parameters
    $strKeyword = JRequest::getVar("keywords", StringHelper::EMPTY_STRING);
    $_SESSION[Search::KEYWORDS] = $strKeyword;

    $strFunding = JRequest::getVar('funding');
    $_SESSION[Search::FUNDING_TYPE] = $strFunding;

    $iSite = JRequest::getInt('neesSite', 0);
    $_SESSION[Search::NEES_SITE] = $iSite;
    
    $strMember = JRequest::getVar('member');
    if($strMember=="Last Name, First Name"){
      $strMember = StringHelper::EMPTY_STRING;
    }
    $_SESSION[Search::MEMBER] = $strMember;
    $_SESSION[Search::IS_INVESTIGATOR] = 0;

    $strMaterialType = JRequest::getVar('materialType', StringHelper::EMPTY_STRING);
    if($strMaterialType=="(Separate by commas)"){
      $strMaterialType = StringHelper::EMPTY_STRING;
    }
    $_SESSION[Search::MATERIAL_TYPES] = $strMaterialType;

    $strAwards = JRequest::getVar('award', StringHelper::EMPTY_STRING);
    if($strAwards=="(Separate by commas)"){
      $strAwards = StringHelper::EMPTY_STRING;
    }
    $_SESSION[Search::AWARDS] = $strAwards;

    $strProjectIds = JRequest::getVar('projid', "");
    if($strProjectIds=="(Separate by commas)"){
      $strProjectIds = StringHelper::EMPTY_STRING;
    }
    $_SESSION[Search::PROJECT_IDS] = $strProjectIds;

    $iProjectTypeId = JRequest::getInt('projectType', 0);
    $_SESSION[Search::PROJECT_TYPE] = $iProjectTypeId;


    $iProjectYear = JRequest::getInt('projectYear', 0);
    $_SESSION[Search::PROJECT_YEAR] = $iProjectYear;

    $strOrderBy = JRequest::getVar('order', 'nickname');

    $iResultsCount = 0;

    $strParamArray = array(0,0);

    //invoke the search form plugin
    JPluginHelper::importPlugin( 'project' );
    $oDispatcher =& JDispatcher::getInstance();
    $oProjectArray = $oDispatcher->trigger('onProjectSearch',$strParamArray);
    $iResultsCountArray = array(0=>1);

    //check to see if we have results
    if(!empty($oProjectArray)){
      /*
       * if we do get the first array.  the plugin returns arrays.
       * thus, what we are looking for is wrapped inside results array.
       *
       */
      $oResultsArray = $oProjectArray[0];
      $iResultsCount = JRequest::getVar('total');

      /*
       * store the results in the session.
       * get in the view with unserialize($_REQUEST[Search::RESULTS])
       */
      $_SESSION[Search::RESULTS] = serialize($oResultsArray);
      $_REQUEST[Search::COUNT] = $iResultsCount;
      $_REQUEST[Search::KEYWORDS] = $strKeyword;
      $_REQUEST[Search::ORDER_BY] = $strOrderBy;

      //create thumbnails if need be...
      $strProjectIconArray = array();
      foreach($oResultsArray as $iProjectIndex=>$oProject){
        $strThumbnail =  $oProject->getProjectThumbnailHTML("icon");
        array_push($strProjectIconArray, $strThumbnail);
      }
      $_SESSION[Search::THUMBNAILS] = $strProjectIconArray;
    }

    $dEndTime = $this->getComputeTime();
    $dSeconds = $dEndTime - $dStartTime;
    $_REQUEST[Search::TIMER] = round($dSeconds, 2);

    JRequest::setVar("view", "results" );
    JRequest::setVar("count", $iResultsCount );
    parent::display();
  }

  public function filterSearchResults(){
    $dStartTime = $this->getComputeTime();

    $_REQUEST[Search::FILTER] = true;

    $strOrderBy = JRequest::getVar('order', 'nickname');
    $iResultsCount = 0;
    $strParamArray = array(0,0);

    //invoke the search form plugin
    JPluginHelper::importPlugin( 'project' );
    $oDispatcher =& JDispatcher::getInstance();
    $oProjectArray = $oDispatcher->trigger('onProjectSearch',$strParamArray);
    $iResultsCountArray = array(0=>1);

    //check to see if we have results
    if(!empty($oProjectArray)){
      /*
       * if we do get the first array.  the plugin returns arrays.
       * thus, what we are looking for is wrapped inside results array.
       *
       */
      $oResultsArray = $oProjectArray[0];
      $iResultsCount = JRequest::getVar('total');

      /*
       * store the results in the session.
       * get in the view with unserialize($_REQUEST[Search::RESULTS])
       */
      $_SESSION[Search::RESULTS] = serialize($oResultsArray);
      $_REQUEST[Search::COUNT] = $iResultsCount;
      $_REQUEST[Search::KEYWORDS] = (isset($_SESSION[Search::KEYWORDS])) ? $_SESSION[Search::KEYWORDS] : "";
      $_REQUEST[Search::ORDER_BY] = $strOrderBy;

      //create thumbnails if need be...
      $strProjectIconArray = array();
      foreach($oResultsArray as $iProjectIndex=>$oProject){
        $strThumbnail =  $oProject->getProjectThumbnailHTML("icon");
        array_push($strProjectIconArray, $strThumbnail);
      }
      $_SESSION[Search::THUMBNAILS] = $strProjectIconArray;
    }

    $dEndTime = $this->getComputeTime();
    $dSeconds = $dEndTime - $dStartTime;
    $_REQUEST[Search::TIMER] = round($dSeconds, 2);

    JRequest::setVar("view", "results" );
    JRequest::setVar("count", $iResultsCount );
    parent::display();
  }
  
  /**
   * Returns displays or downloads a file using 
   * the provided absolute path.
   *
   */
  public function getFile(){
    $strPathToFile = JRequest::getVar("path", "");

    /*
     * TODO: Remove this check before going to prototype 3.
     * We want to go straight to the file path!
     */
    if(!StringHelper::beginsWith($strPathToFile, "/nees/home")){
      $strPathToFile = "/www/neeshub/components/com_warehouse/".$strPathToFile;
    }

    FileHelper::download($strPathToFile);
  }
  
  /**
   * 
   */
  function download(){
    /* @var $oModel WarehouseModelBase */
    $oModel =& $this->getModel('Base');

    /*
     * as of 20100727, downloads are on experiment page.
     *
     * in the future, we may monitor downloads on project page.
     * when the time comes, experimentId should be 0.
     */
    $iProjectId = JRequest::getInt('projectId');
    $iExperimentId = JRequest::getInt('experimentId');

    if($iExperimentId > 0){
      $oModel->updateEntityDownloads(3, $iExperimentId);
    }else{
      if($iProjectId > 0){
        $oModel->updateEntityDownloads(1, $iProjectId);
      }
    }

    //$ext = $oModel->downloadTarBall();
    $ext = $oModel->downloadZip();
  }

  function getDownloadSize(){
    /* @var $oModel WarehouseModelBase */
    $oModel =& $this->getModel('Base');

    $iDataFileId = JRequest::getVar('id', 0);
    $iSum = JRequest::getVar('sum', 0);
    $strAction = JRequest::getVar('action');

    /* @var $oDataFile DataFile */
    $oDataFile = null;
      
    $iDataFileIdArray = explode(",", $iDataFileId);
    if(count($iDataFileIdArray)==1){
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $iSize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : $oDataFile->getFilesize();
      if($strAction=='add'){
        $iSum += $iSize;
      }else{
        $iSum -= $iSize;
      }
    }else{
      $oDataFileArray = DataFilePeer::retrieveByPKs($iDataFileIdArray);
      foreach($oDataFileArray as $oDataFile){
        $iSize = ($oDataFile->isDirectory()) ? DataFilePeer::getDirectorySize($oDataFile->getFullPath()) : $oDataFile->getFilesize();
        if($strAction=='add'){
          $iSum += $iSize;
        }else{
          $iSum -= $iSize;
        }
      }
    }

    $strReturn = $iSum .":". cleanSize($iSum);
    
    echo $strReturn;
  }
  
  function getTrialDropDown(){
  	$iProjectId = JRequest::getVar("projid");
  	$iExperimentId = JRequest::getVar("expid");
  	 
  	$oModel =& $this->getModel('Data');
  	$strTrialArray = $oModel->findDistinctTrials($iProjectId, $iExperimentId);
  	//print_r($strTrialArray);
  	echo $oModel->findDistinctTrialsHTML($strTrialArray, $iProjectId, $iExperimentId);
  	
  	//$s = $iProjectId."/".$iExperimentId;
  	//echo $s;
  	//echo "hello";
  }
  
  function getRepetitionDropDown(){
  	$iProjectId = JRequest::getVar("projid");
  	$iExperimentId = JRequest::getVar("expid");
  	$iTrialId = JRequest::getVar("trialid");
  	 
  	$oModel =& $this->getModel('Data');
  	$strRepetitionArray = $oModel->findDistinctRepetitions($iProjectId, $iExperimentId, $iTrialId);
  	echo $oModel->findDistinctRepetitionsHTML($strRepetitionArray);
  }

  function getComputeTime(){
    $mtime = microtime();
    $mtime = explode(' ', $mtime);
    $mtime = $mtime[1] + $mtime[0];
    return $mtime;
  }
}

?>