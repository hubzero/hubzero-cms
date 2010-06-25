<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once( 'api/org/nees/oracle/Project.php' );
require_once( 'api/org/nees/oracle/Experiment.php' );
require_once( 'api/org/nees/oracle/Curate.php' );
require_once( 'api/org/nees/oracle/DataFile.php' );
require_once( 'api/org/nees/html/CurateHtml.php' );
require_once( 'api/org/nees/oracle/util/DbStatement.php' );
require_once( 'api/org/nees/util/StringHelper.php' );
require_once( 'api/org/nees/util/FileHelper.php' );

class CurateModelExperiment extends JModel{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
  }
  
  /**
   * Return a list of experiments by project id
   * @param $p_iProjectId - the current project
   */
  public function getExperiments($p_iProjectId){
  	return Curate::getExperiments($p_iProjectId);
  }
  
  /**
   * Return the requested experiment
   * @param $p_iExperimentId - the current experiment
   */
  public function getCuratedExperimentById($p_iExperimentId){
  	return Curate::getExperimentById($p_iExperimentId);
  }
  
  /**
   * Return the requested project
   * @param $p_iProjectId - the current project
   */
  public function getProjectById($p_iProjectId){
  	return Project::getProjectById($p_iProjectId);
  }
  
  /**
   * Return the requested experiment
   * @param $p_iExperimentId - the current experiment
   */
  public function getExperimentById($p_iExperimentId){
  	return Experiment::getExperimentById($p_iExperimentId);
  }
  
  /**
    * Find all of the documents related to a specified project.
    * There are not any foreign keys that link the project to 
    * its rescpective data file.  The query is performed using 
    * a like clause to find all documents under the directory:
    * /nees/home/<project_name>.groups
    * @param $p_sProjectName - name of the given project
    * @param $p_sExperimentName - name of the given experiment
    * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
    * @return collection of rows (array)
    */
  public function getExperimentDocumentsAll($p_sProjectName, $p_sExperimentName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated){
  	return Curate::getExperimentDocumentsAll($p_sProjectName, $p_sExperimentName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated);
  }
  
  /**
   * Returns a list of curation object types.
   *
   */
  public function getCurationObjectTypes(){
  	return Curate::getCurationObjectTypes();
  }
  
  /**
   * Returns the html for an input field that can implement 
   * an ajax call.
   *
   */
  public function getAjaxHandler($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_strName){
  	return CurateHtml::getAjaxHandler($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_strName);
  }
  
  /**
   * Returns the html for a textarea that can implement 
   * an ajax call.
   *
   */
  public function getAjaxTextAreaHandler($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_strName){
  	return CurateHtml::getAjaxTextAreaHandler($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_strName);
  }
  
  /**
   * 
   *
   */
  public function getHiddenInput($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName){
  	return CurateHtml::getHiddenInput($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName);
  }
  
  /**
   * 
   *
   */
  public function insertExperiment($p_oExperimentArray){
  	$oUser =& JFactory::getUser();
  	
//  	$firephp = FirePHP::getInstance(true);
//  	$firephp->log('CurateModelExperiment::insertExperiment');
  	
  	$p_iExperimentId = JRequest::getVar('expId'); 
  	$iVersion = JRequest::getVar('txtProjectVersion', 0); 
//  	$firephp->log('CurateModelExperiment::insertExperiment-version', $iVersion);
  	$strObjectType = JRequest::getVar('txtProjectObjectType', 'Experiment');
//  	$firephp->log('CurateModelExperiment::insertExperiment-type', $strObjectType);
  	$strName = JRequest::getVar('txtProjectName'); 
//  	$firephp->log('CurateModelExperiment::insertExperiment-name', $strName);
  	if(strlen($strName)==0)throw new ValidationException("Name should be in the format NEES-YYYY-####");
    $strTitle = JRequest::getVar('txtProjectTitle');
//    $firephp->log('CurateModelExperiment::insertExperiment-title', $strTitle);
    if(strlen($strTitle)==0)throw new ValidationException("Title should not be blank");
    $strTitleShort = JRequest::getVar('txtProjectShortTitle');
//    $firephp->log('CurateModelExperiment::insertExperiment-short title', $strTitleShort);
//    $firephp->log('CurateModelExperiment::insertExperiment-short title', strlen($strTitleShort));
    if(strlen($strTitleShort)==0)throw new ValidationException("Short Title should not be blank");
    //if(!$strTitleShort || $strTitleShort==="")throw new ValidationException("Short Title should not be blank");
  	$strDescription = JRequest::getVar('txtProjectDescription');
//  	$firephp->log('CurateModelExperiment::insertExperiment-descript', $strDescription);
    if(strlen($strDescription)==0)throw new ValidationException("Description should not be blank");
    $oObjectCreationDate = JRequest::getVar('txtProjectCurated');
//    $firephp->log('CurateModelExperiment::insertExperiment-curated', $oObjectCreationDate);
    if(strlen($oObjectCreationDate)==0)throw new ValidationException("Curation Date should not be blank");
    $oInitialCurationDate = JRequest::getVar('txtProjectCurated');
//    $firephp->log('CurateModelExperiment::insertExperiment-curation date', $oInitialCurationDate);
    $strCuratonState = JRequest::getVar('txtProjectCurationState');
    if(strlen($strCuratonState)==0)throw new ValidationException("Curation State should not be blank");
//    $firephp->log('CurateModelExperiment::insertExperiment-curation state', $strCuratonState);
    $strObjectVisibility = JRequest::getVar('txtProjectVisibility'); 
    if(strlen($strObjectVisibility)==0)throw new ValidationException("Viewability should not be blank");
//    $firephp->log('CurateModelExperiment::insertExperiment-visibility', $strObjectVisibility);
    $strObjectStatus = JRequest::getVar('txtProjectStatus');
    if(strlen($strObjectStatus)==0)throw new ValidationException("Project Status should not be blank");
//    $firephp->log('CurateModelExperiment::insertExperiment-object status', $strObjectStatus);
    $strConformanceLevel = "Complete Metadata";
    $strLink = JRequest::getVar('txtProjectLink');
    if(strlen($strLink)==0)throw new ValidationException("Link should not be blank");
//    $firephp->log('CurateModelExperiment::insertExperiment-link', $strLink);
    $strCreatedBy = $oUser->username; 
    $oCreatedDate = JRequest::getVar('txtProjectStartDate');
//    $firephp->log('CurateModelExperiment::insertExperiment-create date', $oCreatedDate);
    $strModifiedBy = $oUser->username; 
    $oModifiedDate = JRequest::getVar('txtProjectCurated');
//    $firephp->log('CurateModelExperiment::insertExperiment-modified', $oModifiedDate);
    
    return Curate::insertObject($iVersion, $strObjectType, $strName, $strTitle, $strTitleShort, 
    							 $strDescription,$oObjectCreationDate, $oInitialCurationDate,
    							 $strCuratonState, $strObjectVisibility, $strObjectStatus, $strConformanceLevel,
    							 $strLink, $strCreatedBy, $oCreatedDate, $strModifiedBy, $oModifiedDate);
    return false;							 
  }
  
  /**
   * Inserts a record into curatedncidcross_ref
   */
  public function insertCuratedNcIdCrossRef($p_iNeesCentralId, $p_iCuratedObjectId, $p_strTableSource){
  	$oUser =& JFactory::getUser();
  	return Curate::insertCuratedNcIdCrossRef($p_iNeesCentralId, $p_iCuratedObjectId, $p_strTableSource, $oUser->username);
  }
  
  /**
   * Returns a curated object using its title and link.  
   * @param $p_strTitle 
   * @param $p_strLink
   */
  public function getCuratedObjectByTitleAndLink($p_strTitle, $p_strLink){
  	return Curate::getCuratedObjectByTitleAndLink($p_strTitle, $p_strLink);
  }
  
}//end class

?>