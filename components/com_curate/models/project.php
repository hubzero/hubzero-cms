<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once( 'api/org/nees/oracle/Project.php' );
require_once( 'api/org/nees/oracle/Curate.php' );
require_once( 'api/org/nees/oracle/DataFile.php' );
require_once( 'api/org/nees/html/CurateHtml.php' );
require_once( 'api/org/nees/oracle/util/DbStatement.php' );
require_once( 'api/org/nees/util/StringHelper.php' );
require_once( 'api/org/nees/util/FileHelper.php' );

class CurateModelProject extends JModel{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
  }
  
  /**
   * Find a project by id.
   * @returns an array of rows.
   *
   */
  function getCuratedProjectById($p_nProjectId){
  	return Curate::getProjectById($p_nProjectId);
  }
  
  /**
   * Find a project by id.
   * @returns an array of rows.
   *
   */
  function getProjectById($p_nProjectId){
  	return Project::getProjectById($p_nProjectId);
  }
  
  /**
    * Find all of the documents related to a specified project.
    * There are not any foreign keys that link the project to 
    * its rescpective data file.  The query is performed using 
    * a like clause to find all documents under the directory:
    * /nees/home/<project_name>.groups
    * @param $p_sProjectName - name of the given project
    * @param $p_nDeleted - 0 or 1 for not deleted or removed files.
    * @return collection of rows (array)
    */
  public function getProjectDocumentsAll($p_sProjectName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated){
  	return Curate::getProjectDocumentsAll($p_sProjectName, $p_nDeleted, $p_oCurationObjectTypeArray, $p_sCurated);
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
   * Updates an attribute of the curation_objects table.
   *
   */
  public function update($p_strColumn, $p_strValue, $p_iObjectId){
  	return Curate::updateCuratedObject($p_strColumn, $p_strValue, $p_iObjectId);
  }
  
  /**
   * Create a collection of document DbStatements that will be executed in a batch update.
   * If the document's object id is greater than 0, do an update on curated_objects.
   * If the document's object id is 0 or empty, do an insert on curated_objects.
   * 
   * By the way, the new display doesn't require all of the input fields of the old one.
   * Thus, we'll set them to empty strings or some default value until we are told differently.
   * TODO: Call Claude and Jerry about this ASAP!
   */
  public function createDocumentDbStatementArray(){
  	$oDbStatementArray = array();
  	
  	$strDate = "".date("m-d-Y",time());
  	
//  	$firephp = FirePHP::getInstance(true);
//  	$firephp->log('CurateModelProject::createDocumentDbStatementArray');
  	
  	/*
  	 * projectName is a hidden input value.  We will use the project's 
  	 * name to construct a link variable.  The curation object's link will 
  	 * not have the ".groups" in its path.
  	 */
  	$strProjectName = JRequest::getVar('projectName');
  	
  	/*
  	 * each checkbox ('cbxCurateSelect') has a value of 
  	 * <table row number>#<data_file_id>#<curated_object_id>.  the table row 
  	 * number is sequential 0,1,2...  if the document hasn't been curated, the 
  	 * object id will be blank.
  	 */
  	$oCurateSelectCheckboxArray = $_POST['cbxCurateSelect'];
	while (list ($iIndex,$strSelectValue) = @each ($oCurateSelectCheckboxArray)) {
	  $strQuery="";
	  $oRowObjectIdArray = explode("#", $strSelectValue);
	  $iRowNumber = $oRowObjectIdArray[0];
	  $iDataFileId = $oRowObjectIdArray[1];
	  $iObjectId = $oRowObjectIdArray[2];
//	  $firephp->log('CurateModelProject::createDocumentDbStatementArray-row', $iRowNumber);
//	  $firephp->log('CurateModelProject::createDocumentDbStatementArray-data_file_id', $iDataFileId);
//	  $firephp->log('CurateModelProject::createDocumentDbStatementArray-object_id', $iObjectId);
	  
	  //the coup de grace: checking if we should update or insert.
	  if($iObjectId > 0){
	  	$strQuery = "update curated_objects set title=:title, short_title=:short_title, description=:description, object_type=:object_type, object_status=:object_status, curation_state=:curation_state, name=:name, link=:link, object_visibility=:object_visibility, conformance_level=:conformance_level, version=:version, modified_by=:modified_by, modified_date=sysdate, object_creation_date=to_date(:object_creation_date,'MM-dd-yyyy') where object_id=:iObjectId";
	  }else{
	  	$strQuery = "insert into ".NeesConfig::ORACLE_SCHEMA.".curated_objects (object_id, version,object_type,name,title,short_title, ".
	      		  "description,object_creation_date,initial_curation_date,curation_state,object_visibility,object_status, ".
	      		  "conformance_level,link,created_by,created_date,modified_by, modified_date) ".
	      		  "values(curated_objects_seq.nextval, :version, :object_type, :name, :title, :short_title, :description, to_date(:object_creation_date,'MM-dd-yyyy'), ".
	      		  "sysdate, :curation_state, :object_visibility, :object_status, :conformance_level, :link, ".
	      		  ":created_by, to_date(:created_date,'MM-dd-yyyy') /*sysdate*/, :modified_by, to_date(:modified_date,'MM-dd-yyyy') /*sysdate*/)";
	  }//end if-else $iObjectId
//	  $firephp->log('CurateModelProject::createDocumentDbStatementArray-query', $strQuery);
	  
	  $strPath = JRequest::getVar('strDocumentPath'.$iRowNumber);
	  $strName = JRequest::getVar('strDocumentName'.$iRowNumber);
	  $strTitle = JRequest::getVar('strDocumentTitle'.$iRowNumber);
	  $strDescription = JRequest::getVar('strDocumentDescription'.$iRowNumber);
	  $strObjectType = JRequest::getVar('cboObjectType'.$iRowNumber);
	  if(strlen($strObjectType)===0)$strObjectType = NULL;
	  $strLink = "/File/".$strProjectName . $strPath . "/" . $strName;
	  $iComplete = JRequest::getVar('cbxCuratDone'.$iRowNumber);
	  
	  //TODO: Test version
	  $iVersion = JRequest::getVar('strDocumentCurateVersion'.$iRowNumber);
	  
  	  //bind the values
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  
  	  $oDbStatement->bind(":version", $iVersion);
  	  $oDbStatement->bind(":object_type", $strObjectType);
  	  $oDbStatement->bind(":name", $strName);
  	  $oDbStatement->bind(":title", $strTitle);
  	  $oDbStatement->bind(":short_title", $strTitle);
  	  $oDbStatement->bind(":description", $strDescription);
  	  $oDbStatement->bind(":link", $strLink);
  	  
  	  if($iComplete==1){
  	    $oDbStatement->bind(":object_status", "Complete");
  	    $oDbStatement->bind(":conformance_level", "Complete Metadata");
  	    $oDbStatement->bind(":curation_state", "Complete");
  	  }else{
  	  	$oDbStatement->bind(":object_status", "Incomplete");
  	    $oDbStatement->bind(":conformance_level", "Incomplete Metadata");
  	    $oDbStatement->bind(":curation_state", "Incomplete");
  	  }
  	  
  	  $oUser =& JFactory::getUser();
  	  if($iObjectId == 0)$oDbStatement->bind(":created_by", $oUser->username);
  	  $oDbStatement->bind(":modified_by", $oUser->username);
  	  
  	  //request the data_file array to get visibility and when document was created
  	  $oDataFileArray = DataFile::getById($iDataFileId);
  	  $oDbStatement->bind(":object_creation_date", $oDataFileArray['CREATED']);
  	  $oDbStatement->bind(":object_visibility", $oDataFileArray['VIEWABLE']);

  	  //only bind if update
  	  if($iObjectId > 0){
  	  	$oDbStatement->bind(":iObjectId", $iObjectId);
//  	  	$firephp->log('CurateModelProject::createDocumentDbStatementArray-do update', $iObjectId);
  	  }
  	  
  	  if($iObjectId == 0){
  	    $oDbStatement->bind(":created_date", $strDate);
  	    $oDbStatement->bind(":modified_date", $strDate);
  	  }
  	  array_push($oDbStatementArray, $oDbStatement);
	}//end while list 
	
	return $oDbStatementArray;
  }
  
  public function deleteDocumentDbStatementArray($p_oDbStatementArray){
  	$oDbStatementArray = array();
  	
  	$strQuery = "delete from ".NeesConfig::ORACLE_SCHEMA.".curated_objects 
  				 where version=:version and object_type=:object_type
  				   and name=:name and title=:title
  				   and short_title=:short_title 
  				   and object_creation_date=:object_creation_date 
  				   and initial_curation_date=:initial_curation_date
  				   and curation_state=:curation_state 
  				   and object_visibility=:object_visibility
  				   and object_status=:object_status
  				   and conformance_level=:conformance_level and link=:link
  				   and modified_by=:modified_by and modified_date=:modified_date 
  				   and created_by=:created_by and created_date=:created_date"; 
  	
  	foreach($p_oDbStatementArray as $oStatement){
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  
  	  $oDbStatement->bind(":version", $oStatement->getParameterValue(":version"));
  	  $oDbStatement->bind(":object_type", $oStatement->getParameterValue(":object_type"));
  	  $oDbStatement->bind(":name", $oStatement->getParameterValue(":name"));
  	  $oDbStatement->bind(":title", $oStatement->getParameterValue(":title"));
  	  $oDbStatement->bind(":short_title", $oStatement->getParameterValue(":short_title"));
//  	  $oDbStatement->bind(":description", $oStatement->getParameterValue(":description"));
  	  $oDbStatement->bind(":object_creation_date", $oStatement->getParameterValue(":object_creation_date"));
  	  $oDbStatement->bind(":initial_curation_date", $oStatement->getParameterValue(":initial_curation_date"));
  	  $oDbStatement->bind(":curation_state", $oStatement->getParameterValue(":curation_state"));
  	  $oDbStatement->bind(":object_visibility", $oStatement->getParameterValue(":object_visibility"));
  	  $oDbStatement->bind(":object_status", $oStatement->getParameterValue(":object_status"));
  	  $oDbStatement->bind(":conformance_level", $oStatement->getParameterValue(":conformance_level"));
  	  $oDbStatement->bind(":link", $oStatement->getParameterValue(":link"));
  	  $oDbStatement->bind(":modified_by", $oStatement->getParameterValue(":modified_by"));
  	  $oDbStatement->bind(":modified_date", $oStatement->getParameterValue(":modified_date"));
  	  
//  	  $iObjectId = $oStatement->getParameterValue(":iObjectId");
//  	  if($iObjectId == 0){
  	    $oDbStatement->bind(":created_date", $oStatement->getParameterValue(":created_date"));
  	    $oDbStatement->bind(":created_by", $oStatement->getParameterValue(":created_by"));
//  	  }
  	  
  	  echo "<br><br>";
  	  print_r($oStatement->getParameters());
	  echo "<br><br>";
  	  array_push($oDbStatementArray, $oDbStatement);
  	}//end foreach
  	
  	$oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
    $bReturn = DbHelper::executeBatch($oConnection, $oDbStatementArray);
    DbHelper::close($oConnection);
    return $bReturn;
  }
  
  /**
   * We need to create a collection of DbStatements for 
   * populating curatedncidcross_ref table.  
   */
  public function createCrossRefDbStatementArray(){
  	$oDbStatementArray = array();
  	
  	$oDate = date("m-d-Y",time());
  	
  	//Want to cause error for testing
  	//$oDate = date("d-m-y",time());
  	
//  	$firephp = FirePHP::getInstance(true);
//  	$firephp->log('CurateModelProject::createCrossRefDbStatementArray');
  	
  	/*
  	 * grab the project name as hidden form field.
  	 * the project name is used to construct the link stored 
  	 * in curated_objects.
  	 */
  	$strProjectName = JRequest::getVar('projectName');
  	
  	//get the current user.  they should be a member of the curation group.
  	$oUser =& JFactory::getUser();
  	
  	$oCurateSelectCheckboxArray = $_POST['cbxCurateSelect'];
	while (list ($iIndex,$strSelectValue) = @each ($oCurateSelectCheckboxArray)) {
	  $strQuery="";
	  $oRowObjectIdArray = explode("#", $strSelectValue);
	  $iRowNumber = $oRowObjectIdArray[0];
	  $iDataFileId = $oRowObjectIdArray[1];
	  $iObjectId = $oRowObjectIdArray[2];
	  
	  //if we inserted a document, get the object id 
	  if($iObjectId == 0){
	  	$strTitle = JRequest::getVar('strDocumentTitle'.$iRowNumber);
	  	$strPath = JRequest::getVar('strDocumentPath'.$iRowNumber);	  
	  	$strName = JRequest::getVar('strDocumentName'.$iRowNumber);
	  	$strLink = "/File/".$strProjectName . $strPath . "/" . $strName;
	  	
	  	//find the curated object given the title and link.
	    $oCuratedObjectArray = $this->getCuratedObjectByTitleAndLink($strTitle, $strLink);
//	    $firephp->log('CurateModelProject::createCrossRefDbStatementArray',$oCuratedObjectArray['OBJECT_ID']);
	    
	    /*
	     * Prepare the DbStatement object for a batch insert.
	     */
	    $strQuery = "insert into ".NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref ".
      			    "(id, neescentral_objectid, curated_entityid, neescentral_table_source, created_by, created_date) ".
      			    "values (curatedncidcross_ref_seq.nextval, :neescentral_objectid, :curated_entityid, :neescentral_table_source, :created_by, to_date(:created_date,'MM-dd-yyyy'))";
      
        $oDbStatement = new DbStatement();
  	    $oDbStatement->prepareStatement($strQuery);
  	    $oDbStatement->bind(":neescentral_objectid", $iDataFileId);
  	    $oDbStatement->bind(":curated_entityid", $oCuratedObjectArray['OBJECT_ID']);
  	    $oDbStatement->bind(":neescentral_table_source", "Data_File");
  	    $oDbStatement->bind(":created_by", $oUser->username);
  	    $oDbStatement->bind(":created_date", $oDate);
  	    
	    array_push($oDbStatementArray, $oDbStatement);
	  }//end if-else $iObjectId
	}//end while list 
	
	return $oDbStatementArray;
  }
  
  public function deleteCrossRefDbStatementArray($p_oDbStatementArray){
  	$oDbStatementArray = array();
  	
  	$strQuery = "delete from ".NeesConfig::ORACLE_SCHEMA.".curatedncidcross_ref 
  				 where neescentral_objectid=:neescentral_objectid 
  				   and curated_entityid=:curated_entityid
  				   and neescentral_table_source=:neescentral_table_source 
  				   and created_by=:created_by
  				   and created_date=:created_date";
  	
  	foreach($p_oDbStatementArray as $oStatement){
  	  $oDbStatement = new DbStatement();
  	  $oDbStatement->prepareStatement($strQuery);
  	  
  	  $oDbStatement->bind(":neescentral_objectid", $oStatement->getParameterValue(":neescentral_objectid"));
  	  $oDbStatement->bind(":curated_entityid", $oStatement->getParameterValue(":curated_entityid"));
  	  $oDbStatement->bind(":neescentral_table_source", $oStatement->getParameterValue(":neescentral_table_source"));
  	  $oDbStatement->bind(":created_by", $oStatement->getParameterValue(":created_by"));
  	  $oDbStatement->bind(":created_date", $oStatement->getParameterValue(":created_date"));
  	  
  	  array_push($oDbStatementArray, $oDbStatement);
  	}//end foreach
  	
  	$oConnection = DbHelper::getConnection(NeesConfig::ORACLE_USERNAME, NeesConfig::ORACLE_PASSWORD, NeesConfig::ORACLE_SERVER);
    $bReturn = DbHelper::executeBatch($oConnection, $p_oDocumentDbStatementArray);
    DbHelper::close($oConnection);
  }
  
  /**
   * Processes a batch of DbStatements.  This method is 
   * called when updating/inserting a project with its documents or 
   * just a project's documents only.
   *
   */
  public function executeBatch($p_oDocumentDbStatementArray){
  	return Curate::executeBatch($p_oDocumentDbStatementArray);
  }
  
  /**
   * Inserts a project into the curated_objects table.  If 
   * a value is missing, throw an exception.
   *
   */
  public function insertProject($p_oProjectArray){
  	$oUser =& JFactory::getUser();
  	
  	//$firephp = FirePHP::getInstance(true);
  	//$firephp->log('CurateModelProject::insertProject');
  	
  	$p_iProjectId = JRequest::getVar('projectId'); 
  	$iVersion = JRequest::getVar('txtProjectVersion', 0); 
  	//$firephp->log('CurateModelProject::insertProject', $iVersion);
  	$strObjectType = JRequest::getVar('txtProjectObjectType', 'Project');
  	//$firephp->log('CurateModelProject::insertProject', $strObjectType);
  	$strName = JRequest::getVar('txtProjectName'); 
  	//$firephp->log('CurateModelProject::insertProject', $strName);
  	if(strlen($strName)==0)throw new ValidationException("Name should be in the format NEES-YYYY-####");
    $strTitle = JRequest::getVar('txtProjectTitle');
    //$firephp->log('CurateModelProject::insertProject', $strTitle);
    if(strlen($strTitle)==0)throw new ValidationException("Title should not be blank");
    $strTitleShort = JRequest::getVar('txtProjectShortTitle');
    //$firephp->log('CurateModelProject::insertProject', $strTitleShort);
    if(strlen($strTitleShort)==0)throw new ValidationException("Short Title should not be blank");
  	$strDescription = JRequest::getVar('txtProjectDescription');
  	//$firephp->log('CurateModelProject::insertProject', $strDescription);
    if(strlen($strDescription)==0)throw new ValidationException("Description should not be blank");
    $oObjectCreationDate = JRequest::getVar('txtProjectCurated');
    //$firephp->log('CurateModelProject::insertProject', $oObjectCreationDate);
    if(strlen($oObjectCreationDate)==0)throw new ValidationException("Curation Date should not be blank");
    $oInitialCurationDate = JRequest::getVar('txtProjectCurated');
    //$firephp->log('CurateModelProject::insertProject', $oInitialCurationDate);
    $strCuratonState = JRequest::getVar('txtProjectCurationState');
    if(strlen($strCuratonState)==0)throw new ValidationException("Curation State should not be blank");
    //$firephp->log('CurateModelProject::insertProject', $strCuratonState);
    $strObjectVisibility = JRequest::getVar('txtProjectVisibility'); 
    if(strlen($strObjectVisibility)==0)throw new ValidationException("Viewability should not be blank");
    //$firephp->log('CurateModelProject::insertProject', $strObjectVisibility);
    $strObjectStatus = JRequest::getVar('txtProjectStatus');
    if(strlen($strObjectStatus)==0)throw new ValidationException("Project Status should not be blank");
    //$firephp->log('CurateModelProject::insertProject', $strObjectStatus);
    $strConformanceLevel = "Complete Metadata";
    $strLink = JRequest::getVar('txtProjectLink');
    if(strlen($strLink)==0)throw new ValidationException("Link should not be blank");
    //$firephp->log('CurateModelProject::insertProject', $strLink);
    $strCreatedBy = $oUser->username; 
    $oCreatedDate = JRequest::getVar('txtProjectStartDate');
    //$firephp->log('CurateModelProject::insertProject', $oCreatedDate);
    $strModifiedBy = $oUser->username; 
    $oModifiedDate = JRequest::getVar('txtProjectCurated');
    //$firephp->log('CurateModelProject::insertProject', $oModifiedDate);
    
    return Curate::insertObject($iVersion, $strObjectType, $strName, $strTitle, $strTitleShort, 
    							 $strDescription,$oObjectCreationDate, $oInitialCurationDate,
    							 $strCuratonState, $strObjectVisibility, $strObjectStatus, $strConformanceLevel,
    							 $strLink, $strCreatedBy, $oCreatedDate, $strModifiedBy, $oModifiedDate);
  }
  
  /**
   * Returns a curated object using its title and link.  
   * @param $p_strTitle 
   * @param $p_strLink
   */
  public function getCuratedObjectByTitleAndLink($p_strTitle, $p_strLink){
  	return Curate::getCuratedObjectByTitleAndLink($p_strTitle, $p_strLink);
  }
  
  /**
   * Inserts a record into curatedncidcross_ref
   */
  public function insertCuratedNcIdCrossRef($p_iNeesCentralId, $p_iCuratedObjectId, $p_strTableSource){
  	$oUser =& JFactory::getUser();
  	return Curate::insertCuratedNcIdCrossRef($p_iNeesCentralId, $p_iCuratedObjectId, $p_strTableSource, $oUser->username);
  }
  
  /**
   * Searches the document DbStatement collection to return an array 
   * of arrays (title, link, and object identifiers)  
   * @deprecated
   *
   */
  public function findCuratedDocumentObjectIds($p_oDocumentDbStatementArray, $p_strTitleKey, $p_strLinkKey){
  	$oReturnArray = array();
  	foreach($p_oDocumentDbStatementArray as $oDocumentDbStatement){
  	  $strQuery = $oDocumentDbStatement->getQuery();
  	  if(StringHelper::beginsWith($strQuery, "insert")){
  	  	$strTitle = $oDocumentDbStatement->getParameterValue($p_strTitleKey);
  	  	$strLink = $oDocumentDbStatement->getParameterValue($p_strLinkKey);
  	  	$oCuratedObjectArray = $this->getCuratedObjectByTitleAndLink($strTitle, $strLink);
  	  	
  	  	$oDataFileArray = array();
  	  	$oDataFileArray[$p_strTitleKey] = $strTitle;
  	  	$oDataFileArray[$p_strLinkKey] = $strLink;
  	  	$oDataFileArray['OBJECT_ID'] = $oCuratedObjectArray['OBJECT_ID'];
  	  	
  	  	array_push($oReturnArray, $oDataFileArray);
  	  }//end if	
  	}//end foreach
  	return $oReturArray;
  }//end findCuratedDocumentObjectIds
  
  /**
   * 
   *
   */
  public function downloadFiles(){
  	/*
  	 * projectName is a hidden input value.  We will use the project's 
  	 * name to construct a link variable.  The curation object's link will 
  	 * not have the ".groups" in its path.
  	 */
  	$strProjectName = JRequest::getVar('projectName');
  	
  	$oDataFileIdArray = array();
  	
  	/*
  	 * each checkbox ('cbxCurateSelect') has a value of 
  	 * <table row number>#<data_file_id>#<curated_object_id>.  the table row 
  	 * number is sequential 0,1,2...  if the document hasn't been curated, the 
  	 * object id will be blank.
  	 */
  	$oCurateSelectCheckboxArray = $_POST['cbxCurateSelect'];
	while (list ($iIndex,$strSelectValue) = @each ($oCurateSelectCheckboxArray)) {
	  $oRowObjectIdArray = explode("#", $strSelectValue);
	  $iDataFileId = $oRowObjectIdArray[1];
	  array_push($oDataFileIdArray, $iDataFileId);
	}//end while $oCurateSelectCheckboxArray
	
	if(empty($oDataFileIdArray)){
	  throw new Exception("Data files are not selected");
	}
	
	//create a temporary directory for archiving the files
	$strCurrentTimestamp = time();
	$strDownloadDirectory = "/tmp/curate-download-".$strCurrentTimestamp;
	exec("mkdir ".$strDownloadDirectory, $output);
	//exec("echo 'some simple text for testing' > ".$strDownloadDirectory."/a.log", $output);
	
	//copy the selected files to the temp directory 
	$oDataFileArray = DataFile::getDataFiles($oDataFileIdArray);
	foreach($oDataFileArray as $oDataFile){
//	  echo $oDataFile["PATH"]."./".$oDataFile["NAME"]."<br/>";
	  $strFileToCopy = $oDataFile["PATH"]."./".$oDataFile["NAME"];
	  exec("cp $strFileToCopy $strDownloadDirectory", $output); 
	}
	
	//create the archive under /tmp
	$oUser =& JFactory::getUser();
	$strArchiveFile = "/tmp/$oUser->username-curation-download-$strCurrentTimestamp.tar.gz";
	$strCommandToExecute = "tar -czPf $strArchiveFile $strDownloadDirectory";
	exec($strCommandToExecute, $output);
	
	//download the file
	FileHelper::download($strArchiveFile);
  }
  
  /**
   * Return a list of experiments by project id
   * @param $p_iProjectId - the current project
   */
  public function getExperiments($p_iProjectId){
  	return Curate::getExperiments($p_iProjectId);
  }
  
  /**
   * 
   *
   */
  public function getHiddenInput($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName){
  	return CurateHtml::getHiddenInput($p_sLabel, $p_sValue, $p_sEditLink, $p_iProjectCurationId, $p_sResultDivId, $p_sName);
  }
  
}//end class

?>