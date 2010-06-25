<?php
/**
 * @version		$Id: controller.php 13338 2009-10-27 02:15:55Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport('joomla.application.component.controller');

include('api/org/nees/html/LinkParamsHtml.php');
include('api/org/nees/util/StringHelper.php');


/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class CurateController extends JController{	
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
	$this->registerTask( 'showform' , 'getForm' );
	$this->registerTask( 'saveform' , 'save' );
	$this->registerTask( 'update' , 'update' );
	//$this->registerTask( 'project' , 'submitProject' );
	$this->registerTask( 'project' , 'insertProject' );
	$this->registerTask( 'experiment' , 'insertExperiment' );
	//$this->registerTask( 'experiment' , 'submitExperiment' );
	$this->registerTask( 'download' , 'download' );
	$this->registerTask( 'showexperiment' , 'showExperiment' );
	$this->registerTask( 'documents' , 'submitDocuments' );
  }
  
  /**
   * Method to display the view
   * 
   * @access    public
   */
  function display(){
    $strViewName	= JRequest::getVar('view', 'projects');
    
    //$oUser =& JFactory::getUser();
    
    //echo YGroupHelper::is_member('docman_test') ? 't' : 'f'; # current user is amember (or manager) of the group 'docman_test'?
    
    //check if user is logged into system.
    $oAuthenticationArray = AuthenticationHelper::isLoggedIn();
    if($oAuthenticationArray["VALID"]){
      //if user is logged in, check to see if they are a member of curation group.
      $oAuthenticationArray = AuthenticationHelper::isMember("curation");
    }
    
    //show content if logged in and a member of curation group.  otherwise, display exception.
    if($oAuthenticationArray["VALID"]){
      JRequest::setVar('view', $strViewName );
      parent::display();
    }else{
      echo $oAuthenticationArray["ERROR"];
    }
   
  }
  
  function getForm(){
  	JRequest::setVar( 'view', 'form' );
    parent::display();
  }
  
  function save(){
  	JRequest::setVar( 'view', 'finished' );
  	JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }
  
  function update(){
  	$oLinkParamsHtml = unserialize($_SESSION['LINK_PARAMS']);
  	$strFieldName = $oLinkParamsHtml->get( 'name' );
  	
  	/*
  	 * The input element on the form has name=$strFieldName.  Grab the value using 
  	 * $strFieldName.  Then, overwrite its content in $oLinkParamsHtml.  
  	 * Lastly, save the update to the session object.
  	 */
  	$strValue = JRequest::getVar( $strFieldName );
  	$oLinkParamsHtml->append("value", $strValue);
    $oLinkParamsHtml->store("value", $strValue);
    $_SESSION['LINK_PARAMS'] = serialize($oLinkParamsHtml);
    
  	$iCuratedObjectId = $oLinkParamsHtml->get( 'curated' );
  	$strColumn = $oLinkParamsHtml->get( 'column' );
  	
  	$oModel =& $this->getModel('Project');
  	try{
  	  $oModel -> update($strColumn, $strValue, $iCuratedObjectId);
  	}catch(Exception $oException){
  	  echo "<b>".$oException->getMessage()."</b>";  
  	  return;
  	}
  	
  	JRequest::setVar( 'view', 'finished' );
  	JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }
  
  /**
   * Process the user's request when they hit the submit button 
   * on the form.  If the project has already be curated, update 
   * the documents.  If the project has not been previously curated, 
   * insert the project.  Next, insert or update the associated documents.
   * 
   * @deprecated
   */
  function submitProject(){
  	$bReturn = true;
  	$strError = "";
  	
  	$strCuratedObjectId = JRequest::getVar( 'curatedProjectId' );
  	if(strlen($strCuratedObjectId) > 0){
  	  /*
  	   * has the project been curated?  if yes, 
  	   * update the documents.  the project info 
  	   * gets updated with the update() method.
  	   */
  	  try{	
  	    $this->submitDocuments();
  	  }catch(ValidationException $oException){
  	  	$bReturn = false;
  	  	$strError = $oException->getError();
  	  }
  	}else{
  	  /*
  	   * the current project hasn't been added to 
  	   * the curated_objects table.  also add a record 
  	   * to the curated/neescentral cross 
  	   * reference table for the project.  
  	   */	
  	  try{
  	    $bReturn = $this->insertProject();
  	  }catch(ValidationException $oException){
  	  	$bReturn = false;
  	  	$strError = $oException->getError();
  	  }
  	  
  	  if($bReturn){
  	  	/*
  	  	 * if both inserts succeed, update or insert 
  	     * all of the project documents. 
  	  	 */
  	  	try{
  	  	  $bReturn = $this->submitDocuments();
  	  	}catch(ValidationException $oException){
  	  	  $bReturn = false;
  	  	  $strError = $oException->getError();
  	    }
  	    
  	    /*
  	     * the documents failed to insert.  remove the previously inserted project.
  	     */
  	    if(!$bReturn){
  	      
  	    }
  	  }
  	}
  	
  	if($bReturn){
  	  $iProjectId = JRequest::getVar( 'projectId' );
  	  $strLink = JRoute::_('/curate/project/'.$iProjectId);
	  $this->setRedirect($strLink);
  	}else{
  	  echo $strError;
  	  return;	
  	}
  }
  
  /**
   * @deprecated
   *
   */
  function submitExperiment(){
  	$bReturn = true;
  	$strError = "";
  	
  	$strCuratedObjectId = JRequest::getVar( 'curatedExperimentId' );
  	$strCuratedObjectId = "";
  	if(strlen($strCuratedObjectId) > 0){
  	  /*
  	   * has the project been curated?  if yes, 
  	   * update the documents.  the project info 
  	   * gets updated with the update() method.
  	   */
  	  try{	
  	    $this->submitDocuments();
  	  }catch(ValidationException $oException){
  	  	$bReturn = false;
  	  	$strError = $oException->getError();
  	  }
  	}else{
  	  /*
  	   * the current project hasn't been added to 
  	   * the curated_objects table.  also add a record 
  	   * to the curated/neescentral cross 
  	   * reference table for the project.  
  	   */	
  	  try{
  	    $bReturn = $this->insertExperiment();
  	  }catch(ValidationException $oException){
  	  	$bReturn = false;
  	  	$strError = $oException->getError();
  	  }
  	  
  	  if($bReturn){
  	  	/*
  	  	 * if both inserts succeed, update or insert 
  	     * all of the project documents. 
  	  	 */
  	  	try{
  	  	  $bReturn = $this->submitDocuments();
  	  	}catch(ValidationException $oException){
  	  	  $bReturn = false;
  	  	  $strError = $oException->getError();
  	    }
  	    
  	    /*
  	     * the documents failed to insert.  remove the previously inserted project.
  	     */
  	    if(!$bReturn){
  	      
  	    }
  	  }else{
  	  	//$strError="Testing before insertExperiment";
  	  }
  	}
  	
  	if($bReturn){
  	  $iProjectId = JRequest::getVar( 'projectId' );
  	  $iExperimentId = JRequest::getVar( 'expId' );
  	  $strLink = JRoute::_('/curate/experiment/'.$iExperimentId.'/project/'.$iProjectId);
	  $this->setRedirect($strLink);
  	}else{
  	  echo $strError;
  	  return;	
  	}
  }
  
  /**
   * Process any documents associated with a given project.
   *
   */
  function submitDocuments(){
  	try{
  	  $oModel =& $this->getModel('Project');
  	
  	  //first we will attempt to update/insert the documents into curated_objects table.
  	  $oDocumentDbStatementArray = $oModel->createDocumentDbStatementArray();
  	  $bReturn = $oModel->executeBatch($oDocumentDbStatementArray);
  	
  	  /*
  	   * if the return is true, try to populate curatedncidcross_ref for any 
  	   * rows inserted into curated_objects.  update statements on curated_objects 
  	   * get ignored.
  	   */
  	  if($bReturn){
  	  	$oCrossRefDbStatementArray = $oModel->createCrossRefDbStatementArray();
  	    if(!empty($oCrossRefDbStatementArray)){
  	      try{
//  	        echo "create crossref called<br>";
  	      	$bReturn = $oModel->executeBatch($oCrossRefDbStatementArray);
//  	        echo "create crossref return-$bReturn<br>";
  	      }catch(Exception $oException){
  	      	$bReturn = false;
  	      	$strError = $oException->getError();
//  	      	echo "cross reference error<br>";
  	      }
  	    }
  	  }
  	
  	  /*
  	   * if the cross reference failed, clean out any inserted curated_objects (documents).
  	   */
  	  if(!$bReturn){
//  	  	echo "delete from curated_objects-$bReturn<br>";
  		$bReturn = $oModel->deleteDocumentDbStatementArray($oDocumentDbStatementArray);
//  		echo "delete from curated_objects return - $bReturn<br>";
  	  }
  	}catch(ValidationException $oException){
  	  $bReturn = false;
  	  $strError = $oException->getError();
  	}
  	
  	if(!$bReturn){
  	  echo $strError;
  	  return;
  	}else{
  	  $iProjectId = JRequest::getVar( 'projectId' );
  	  $iExperimentId = JRequest::getVar( 'expId', '' );
  	  
  	  if(!$iExperimentId){
  	  	$strLink = JRoute::_('/curate/project/'.$iProjectId);
  	    $this->setRedirect($strLink);
  	  }else{
  	  	$strLink = JRoute::_('/curate/experiment/'.$iExperimentId.'/project/'.$iProjectId);
  	  	$this->setRedirect($strLink);
  	  }
  	}
  	
  	return $bReturn;
  }
  
  /**
   * Inserts a project into the curated_objects table.  
   * This method also creates a record in curatedncidcross_ref table.
   */
  function insertProject(){
  	try{
  	  $oModel =& $this->getModel('Project');
  	
   	  //get the project from nees central
  	  $iProjectId = JRequest::getVar('projectId');
  	  $oProjectArray = $oModel->getProjectById($iProjectId);
  	
   	  //insert the curated object
  	  $bReturn = $oModel->insertProject($oProjectArray);
  	  if($bReturn){
  	    //use the title and link to find the curated object
  	    $strTitle = JRequest::getVar('txtProjectTitle');
  	    $strLink = JRequest::getVar('txtProjectLink');
  	    $oCuratedObjectArray = $oModel->getCuratedObjectByTitleAndLink($strTitle, $strLink);
  	
  	    //insert curated object, nees central ids into cross ref table
  	    $bReturn = $oModel->insertCuratedNcIdCrossRef($iProjectId, $oCuratedObjectArray['OBJECT_ID'], 'Project');  	
  	  }
  	}catch(ValidationException $oException){
  	  $bReturn = false;
  	  $strError = $oException->getError();
  	}
  	
    if($bReturn){
  	  $iProjectId = JRequest::getVar( 'projectId' );
  	  $strLink = JRoute::_('/curate/project/'.$iProjectId);
	  $this->setRedirect($strLink);
  	}else{
  	  echo $strError;
  	  return;	
  	}  
  	
  	return $bReturn;
  }//end insertProject
  
  /**
   * Inserts an experiment into the curated_objects table.  
   * This method also creates a record in curatedncidcross_ref table.
   */
  function insertExperiment(){
  	try{
  	  $oModel =& $this->getModel('Experiment');
  	
  	  //get the project from nees central
  	  $iProjectId = JRequest::getVar('projectId');
  	  $oProjectArray = $oModel->getProjectById($iProjectId);
  	
  	  $iExperimentId = JRequest::getVar('expId');
  	  $oExperimentArray = $oModel->getExperimentById($iExperimentId);
  	
  	  //insert the curated object
  	  $bReturn = $oModel->insertExperiment($oExperimentArray);
  	  if($bReturn){
  	    //use the title and link to find the curated object
  	    $strTitle = JRequest::getVar('txtProjectTitle');
  	    $strLink = JRequest::getVar('txtProjectLink');
  	    $oCuratedObjectArray = $oModel->getCuratedObjectByTitleAndLink($strTitle, $strLink);
  	
  	    //insert curated object, nees central ids into cross ref table
  	    $bReturn = $oModel->insertCuratedNcIdCrossRef($iExperimentId, $oCuratedObjectArray['OBJECT_ID'], 'Experiment');  	
  	  }
  	}catch(ValidationException $oException){
  	  $bReturn = false;
  	  $strError = $oException->getError();
  	}
  	
    if($bReturn){
  	  $iProjectId = JRequest::getVar( 'projectId' );
  	  $iExperimentId = JRequest::getVar( 'expId' );
  	  $strLink = JRoute::_('/curate/experiment/'.$iExperimentId.'/project/'.$iProjectId);
	  $this->setRedirect($strLink);
  	}else{
  	  echo $strError;
  	  return;	
  	}
  	
  	return $bReturn;
  }//end insertExperiment
  
  function download(){
  	$oModel =& $this->getModel('Project');
  	$oModel->downloadFiles();
  }
  
  function showExperiment(){
  	$iExperimentId = JRequest::getVar( 'expId' );
  	$iProjectId = JRequest::getVar( 'projectId' );
  	$strLink = JRoute::_('/curate/experiment/'.$iExperimentId.'/project/'.$iProjectId);
	$this->setRedirect($strLink);
  }
	
}

?>
