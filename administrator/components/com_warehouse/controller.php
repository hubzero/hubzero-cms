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
	
	$this->registerTask( 'find' , 'getProjectsByForm' );
	$this->registerTask( 'tag' , 'getProjectsByTag' );
	$this->registerTask( 'popular' , 'getProjectsByPopularity' );
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
  public function getProjectsByForm(){
  	//set the search parameters
  	$strKeyword = JRequest::getVar("keywords", "default");
  	$strParamArray = array(0,0);
  	
  	//invoke the search form plugin
  	JPluginHelper::importPlugin( 'project' );
	$oDispatcher =& JDispatcher::getInstance();
	$strResultsArray = $oDispatcher->trigger('onProjectSearchForm',$strParamArray);
	
	//check to see if we have results
	if(!empty($strResultsArray)){
	  /*
	   * if we do get the first array.  plugins, return arrays.  
	   * thus, what we are looking for is wrapped inside results array. 
	   * 
	   */
	  $oResultsArray = $strResultsArray[0];

	  /*
	   * store the results in the session.
	   */
	  $_SESSION[Search::RESULTS]=$oResultsArray;
	}
	
  	JRequest::setVar("view", "results" );
  	parent::display();
  }
  
  public function getProjectsByTag(){
  	JRequest::setVar("view", "results" );
    parent::display();
  }
  
  public function getProjectsByPopularity(){
  	JRequest::setVar("view", "results" );
    parent::display();
  }
}

?>