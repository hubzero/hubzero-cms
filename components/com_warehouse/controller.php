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
	$this->registerTask( 'get' , 'getFile' );
	$this->registerTask( 'download' , 'download' );
  }	
	
  /**
   * Method to display the view
   * 
   * @access    public
   */
  function display(){
  	$strViewName	= JRequest::getVar('view', 'featured');
	JRequest::setVar('view', $strViewName );
    parent::display();
  }
  
  /**
   * Processes the search form on the main page.
   *
   */
  public function findProjects(){
  	//set the search parameters
  	$strKeyword = JRequest::getVar("keywords", "");
  	$_SESSION[Search::KEYWORDS] = $strKeyword;
  	
  	$strType = JRequest::getVar('type');
  	$_SESSION[Search::SEARCH_TYPE] = $strType;
  	
  	$strFunding = JRequest::getVar('funding');
  	$_SESSION[Search::FUNDING_TYPE] = $strFunding;
  	
  	$strMember = JRequest::getVar('member');
  	$_SESSION[Search::MEMBER] = $strMember;
  	
  	$strStartDate = JRequest::getVar('startdate');
  	$_SESSION[Search::START_DATE] = $strStartDate;
  	
  	$strEndDate = JRequest::getVar('enddate');
  	$_SESSION[Search::END_DATE] = $strEndDate;
  	
  	$iResultsCount = 0;
  	
  	$strParamArray = array(0,0);
  	
  	//invoke the search form plugin
  	JPluginHelper::importPlugin( 'project' );
	$oDispatcher =& JDispatcher::getInstance();
	$iResultsCountArray = $oDispatcher->trigger('onProjectSearchFormCount',$strParamArray);
	$strResultsArray = $oDispatcher->trigger('onProjectSearchForm',$strParamArray);
	
	//check to see if we have results
	if(!empty($iResultsCountArray)){
	  /*
	   * if we do get the first array.  the plugin returns arrays.  
	   * thus, what we are looking for is wrapped inside results array. 
	   * 
	   */
	  $oResultsArray = $strResultsArray[0];
	  $iResultsCount = $iResultsCountArray[0];
	  
	  /*
	   * store the results in the session.
	   * get in the view with unserialize($_REQUEST[Search::RESULTS])
	   */
	  $_SESSION[Search::RESULTS] = serialize($oResultsArray);
	  $_REQUEST[Search::COUNT] = $iResultsCount;
	  $_REQUEST[Search::KEYWORDS] = $strKeyword;	  
	}
	
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
  	$oModel =& $this->getModel('Base');
  	$ext = $oModel->downloadTarBall();
  }
  
}

?>