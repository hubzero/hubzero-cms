<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once 'api/org/nees/util/StringHelper.php';
require_once 'api/org/nees/html/UserRequest.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'api/org/nees/lib/filesystem/FileCommandAPI.php';
require_once 'lib/data/ResearcherKeywordPeer.php';
require_once 'lib/data/ResearcherKeyword.php';
require_once 'lib/data/EquipmentPeer.php';
require_once 'lib/data/ProjectHomepagePeer.php';
require_once 'lib/data/MeasurementUnitPeer.php';
require_once 'lib/data/OrganizationPeer.php';
require_once 'lib/data/ProjectOrganizationPeer.php';
require_once 'lib/data/FacilityPeer.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/ProjectGrantPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/ExperimentPeer.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/RolePeer.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/EntityActivityLogPeer.php';
include_once 'lib/data/curation/NCCuratedObjectCatalogEntryPeer.php';
require_once 'api/org/phpdb/propel/central/classes/lib/data/curation/NCCuratedObjects.php';
require_once 'api/org/phpdb/propel/central/classes/lib/data/curation/NCCuratedObjectsPeer.php';
require_once 'lib/security/Authorizer.php';

class ProjectEditorModelBase extends JModel{
	
  private $m_oTabArray;
  private $m_oSearchTabArray;
  private $m_oSearchResultsTabArray;
  private $m_oProjectSubTabArray;
  private $m_oProjectSubTabViewArray;
  private $m_oExperimentsSubTabArray;
  private $m_oExperimentsSubTabViewArray;
  
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();

    $this->m_oTabArray = array("Project", "Experiments", "Team Members");
    $this->m_oTabViewArray = array("", "experiments", "members");

    $this->m_oCreateProjectTabArray = array("Project", "Experiments", "Team Members");
    $this->m_oCreateProjectTabViewArray = array("", ProjectEditor::CREATE_PROJECT_EXPERIMENTS_ALERT, ProjectEditor::CREATE_PROJECT_MEMBERS_ALERT);

    $this->m_oCreateExperimentTabArray = array("Project", "Experiments", "Team Members");
    $this->m_oCreateExperimentTabViewArray = array(ProjectEditor::CREATE_EXPERIMENT_PROJECT_ALERT, "experiments", ProjectEditor::CREATE_EXPERIMENT_MEMBERS_ALERT);

    $this->m_oProjectSubTabArray = array("About", "Videos", "Photos","Documentation", "Analysis");
    $this->m_oProjectSubTabViewArray = array("", "projectvideos", "projectphotos","documentation", "analysis");
    
    $this->m_oExperimentsSubTabArray = array("About", "Materials", "Sensors", "Drawings", "Data", "Videos", "Photos", "Documentation", "Analysis", "Security");
    $this->m_oExperimentsSubTabViewArray = array("", "materials", "sensors","drawings", "data", "videos", "photos", "documentation", "analysis", "security");

    $this->m_oSearchTabArray = array("Search");
    $this->m_oSearchResultsTabArray = array("Results");
    $this->m_oTreeTabArray = array("Tree Browser");
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
   * @return Returns an array of tab views for the selected warehouse
   */
  public function getTabViewArray() {
    return $this->m_oTabViewArray;
  }

  /**
   *
   * @return Returns an array of tabs for the selected warehouse
   */
  public function getCreateProjectTabArray(){
    return $this->m_oCreateProjectTabArray;
  }

  /**
   *
   * @return Returns an array of tab views for the selected warehouse
   */
  public function getCreateProjectTabViewArray() {
    return $this->m_oCreateProjectTabViewArray;
  }

  /**
   *
   * @return Returns an array of tabs for the selected warehouse
   */
  public function getCreateExperimentTabArray(){
    return $this->m_oCreateExperimentTabArray;
  }

  /**
   *
   * @return Returns an array of tab views for the selected warehouse
   */
  public function getCreateExperimentTabViewArray() {
    return $this->m_oCreateExperimentTabViewArray;
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

  public function getExperimentsSubTabArray(){
    return $this->m_oExperimentsSubTabArray;
  }

  public function getExperimentsSubTabViewArray(){
    return $this->m_oExperimentsSubTabViewArray;
  }

  public function getProjectSubTabArray(){
    return $this->m_oProjectSubTabArray;
  }

  public function getProjectSubTabViewArray(){
    return $this->m_oProjectSubTabViewArray;
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
  public function getSubTabs($p_strOption, $p_iId, $p_strTabArray, $p_strSubTabViewArray, $p_strActive){
    return TabHtml::getSubTabs( $p_strOption, $p_iId, $p_strTabArray, $p_strSubTabViewArray, $p_strActive );
  }

  public function getOnClickTabs($p_strTabArray, $p_strTabViewArray, $p_strActive){
    return TabHtml::getOnClickTabs($p_strTabArray, $p_strTabViewArray, $p_strActive);
  }

  public function getOnClickSubTabs( $p_strAlert, $p_oTabArray, $p_strActive ) {
    return TabHtml::getOnClickSubTabs($p_strAlert, $p_oTabArray, $p_strActive);
  }
  
  /**
   * 
   * @return strTabs in html format
   */
  public function getTreeTab($p_strOption, $p_iId, $p_strTabArray, $p_strActive){
    return TabHtml::getTreeTab( $p_strOption, $p_iId, $p_strTabArray, $p_strActive );
  }

  /*
  public function computeLowerLimit($p_iLowerLimit){
    if($p_iLowerLimit==0){
      return 1;
    }
    return $p_iLowerLimit;
  }
  
  public function computeUpperLimit($p_iLowerLimit, $p_iDisplay){
    if($p_iLowerLimit==1){
      return $p_iDisplay;
    }
    return $p_iLowerLimit + $p_iDisplay;
  }
  */

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

  public function getTrialById($p_iTrialId){
    return TrialPeer::find($p_iTrialId);
  }
  
  public function getRepetitionById($p_iRepetitionId){
    return RepetitionPeer::find($p_iRepetitionId);
  }

  /**
   *
   * @param int $p_iProjectId
   * @return Project
   */
  public function getProjectById($p_iProjectId){
    return ProjectPeer::find($p_iProjectId);
  }

  /**
   *
   * @param int $p_iExperimentId
   * @return Experiment
   */
  public function getExperimentById($p_iExperimentId){
    return ExperimentPeer::find($p_iExperimentId);
  }

  /**
   *
   * @param int $p_iDataFileId
   * @return DataFile
   */
  public function getDataFileById($p_iDataFileId){
    return DataFilePeer::find($p_iDataFileId);
  }

  /**
   *
   * @param int $p_iMaterialId
   * @return Material
   */
  public function getMaterialById($p_iMaterialId){
    return MaterialPeer::find($p_iMaterialId);
  }

  /**
   *
   * @param int $p_iLocationPlan
   * @return LocationPlan
   */
  public function getLocationPlanById($p_iLocationPlanId){
    return LocationPlanPeer::find($p_iLocationPlanId);
  }

  /**
   *
   * @param int $p_iLocationId
   * @return Location
   */
  public function getLocationById($p_iLocationId){
    return LocationPeer::retrieveByPK($p_iLocationId);
  }

  /**
   *
   * @param int $p_iEntityTypeId
   * @return EntityType
   */
  public function getEntityTypeById($p_iEntityTypeId){
    return EntityTypePeer::find($p_iEntityTypeId);
  }

  /**
   *
   * @param int $p_iEntityTypeId
   * @return array <Role>
   */
  public function getRolesByEntityType($p_iEntityTypeId){
    return RolePeer::findByEntityType($p_iEntityTypeId);
  }
  
  public function findDataFileByType($p_strFileType, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
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

  public function resizePhotos($p_oDataFileArray) {
    return DataFilePeer::resizePhotos($p_oDataFileArray);
  }

  /**
   *
   * @return JUser 
   */
  public function getCurrentUser(){
    $oUser =& JFactory::getUser();
    return $oUser;
  }

  public function getPersonById($p_iPersonId){
    return PersonPeer::find($p_iPersonId);
  }

  public function getSuggestedUsername($p_strFormName){
    $strFullNameArray = explode(" ", $p_strFormName);
    $strUsername = end($strFullNameArray);
    $strUsername = str_replace("(", "", $strUsername);
    $strUsername = str_replace(")", "", $strUsername);
    
    if(!StringHelper::hasText($strUsername)){
      return null;
    }

    //check to make sure that the person is registered in hub.
    $oJuser =& JFactory::getUser($strUsername);
    
    try{
      $iId = $oJuser->id;
    }catch(Exception $e){
      throw new ValidationException("Errors with user $strUsername.  Please submit a ticket.");
    }

    return PersonPeer::findByUserName($oJuser->username);
  }

  public function getPersonByLastCommaFirstName($p_strFormName){
    $strFullNameArray = explode(",", $p_strFormName);
    if(count($strFullNameArray) < 2){
      throw new ValidationException("Both last and first names are required (Last, First).");
    }

    return PersonPeer::findByFullName(trim($strFullNameArray[1]), trim($strFullNameArray[0]));
  }

  /**
   *
   * @param string $p_strUsername
   * @return JUser
   */
  public function getMysqlUserByUsername($p_strUsername){
    if (empty($p_strUsername))
      return false;

    $oUser =& JFactory::getUser($p_strUsername);
    return $oUser;
  }

  public function getAuthorizer(){
    return Authorizer::getInstance();
  }

  public function getHubPublicProfile($p_oHubUser){
    $profile = new XProfile();
    $profile->load( $p_oHubUser->id );
    if($profile->get('public') == 1){
      return true;
    }
    return false;
  }
  
  public function getOracleUserByUsername($p_strUsername){
    return PersonPeer::findByUserName($p_strUsername);
  }

  public function getOracleUserByEmail($p_strEmail){
    return PersonPeer::findByEmail($p_strEmail);
  }
  
  public function getMultipleValues($p_strFieldName){
    return UserRequest::getMultipleValues($p_strFieldName);
  }
  
  public function getMultipleValuesHTML($p_strFieldName){
    return UserRequest::getMultipleValuesHTML($p_strFieldName);
  }
  
  public function getTupleValuesHTML($p_strFieldName){
    return UserRequest::getTupleValuesHTML($p_strFieldName);
  }
  
  public function getEquipmentByIds($p_iEquipmentIdArray){
  	return EquipmentPeer::retrieveByPKs($p_iEquipmentIdArray);
  }
  
  public function getDisplayDescription($p_strDescription){
    $strReturnDescription = "";
    if(strlen($p_strDescription) > 300){
      $strShortDescription = StringHelper::neat_trim($p_strDescription, 250);
      $strReturnDescription = <<< ENDHTML
              <div id="shortDescription">
                $strShortDescription (<a href="javascript:void(0);" onClick="document.getElementById('longDescription').style.display='';document.getElementById('shortDescription').style.display='none';">more</a>)
              </div>
              <div id="longDescription" style="display:none">
                $p_strDescription (<a href="javascript:void(0);" onClick="document.getElementById('longDescription').style.display='none';document.getElementById('shortDescription').style.display='';">hide</a>)
              </div>
ENDHTML;
    }else{
      $strReturnDescription = $p_strDescription;
    }
    return $strReturnDescription;
  }
  
  
  public function getMeasurementUnits(){
  	return MeasurementUnitPeer::findAll();
  }
  
  public function suggestMeasurementUnits($p_strName){
  	return MeasurementUnitPeer::suggestName($p_strName);
  }

  /**
   *
   * @param string $p_strName
   * @return Organization
   */
  public function findOrganizationByName($p_strName){
  	return OrganizationPeer::findByName($p_strName);
  }

  /**
   *
   * @param string $p_strName
   * @return Facility
   */
  public function findFacilityByName($p_strName){
    return FacilityPeer::findByName($p_strName);
  }

  public function validateText($p_strCategory, $p_strText){
    if(!StringHelper::hasText($p_strText)){
      throw new ValidationException($p_strCategory." is required.");
    }
    return $p_strText;
  }

  /**
   * Validate if the title is set.
   * @param <String> $p_strTitle
   * @return <String> $p_strTitle
   */
  public function validateTitle($p_strTitle){
    if(!$p_strTitle || $p_strTitle==""){
      throw new ValidationException("Title is required.");
    }
    return $p_strTitle;
  }

  /**
   *
   * @param <String> $p_strDate
   * @return <String>
   */
  public function validateStartDate($p_strDate){
    if(!StringHelper::hasText($p_strDate)){
      throw new ValidationException("Start date is required.");
    }

    if(!StringHelper::is_date($p_strDate)){
      throw new ValidationException("Start date should have format mm/dd/yyyy.");
    }
    
    return $p_strDate;
  }

  /**
   *
   * @param <String> $p_strDate
   * @return <String>
   */
  public function validateEndDate($p_strDate, $p_strStartDate=null){
    // 1. make sure we have text
    $strEndDate = ($p_strDate != "mm/dd/yyyy") ? $p_strDate : null;
    
    // 2. make sure the value is an actual date.
    $strEndDate = (StringHelper::is_date($strEndDate)) ? $strEndDate : null;

    if(StringHelper::hasText($strEndDate) && StringHelper::hasText($p_strStartDate)){
      if(StringHelper::is_date($p_strStartDate)){
        $oStartDate = strtotime($p_strStartDate);
        $oEndDate = strtotime($strEndDate);
        if ($oEndDate >= $oStartDate) {
          $valid = "yes";
        } else {
          throw new ValidationException("End date should be after start date.");
        }
      }
    }

    return $strEndDate;
  }


  /**
   * Creates a temporary upload location for logged in users.
   * The path is /components/com_projecteditor/uploads/members/<username>.
   * 
   * @param JUser $p_oHubUser
   * @return string
   */
  public function createUploadDirectory($p_oHubUser){
    $strCurrentDir = "/www/neeshub/components/com_projecteditor";

    $strDirectoryArray = array("uploads", "members", $p_oHubUser->username);
    foreach($strDirectoryArray as $strDirectory){
      $strCurrentDir .= "/".$strDirectory;
      if(!is_dir($strCurrentDir)){
        umask(0000);
        mkdir($strCurrentDir, 0770, true);
      }
    }
    return $strDirectoryArray;
  }

  /**
   *
   * @param string $p_strLikeCondition
   * @return array <EntityType>
   */
  public function getDataFileUsageTypes($p_strLikeCondition=""){
    return EntityTypePeer::findUsageType($p_strLikeCondition);
  }

  /**
   *
   * @param array $p_strUsageTypeArray
   * @return array
   */
  public function findUsageTypeList($p_strUsageTypeArray){
    return EntityTypePeer::findUsageTypeList($p_strUsageTypeArray);
  }

  public function getDataFileUsageTypesHTML($p_oUsageEntityTypeArray, $p_bAddNotApplicableSelection=true){
    $strNotApplicableOption = "<option value=''>Not Applicable</option>";
    if(!$p_bAddNotApplicableSelection){
      $strNotApplicableOption = StringHelper::EMPTY_STRING;
    }
    $strEntityTypes = "<select id='cboUsage' name='usageType'>
                        $strNotApplicableOption";
    foreach($p_oUsageEntityTypeArray as $oEntityType){
      /* @var $oEntityType EntityType */
      $iEntityTypeId = $oEntityType->getId();
      $strEntityName = $oEntityType->getDatabaseTableName();

      $strEntityTypes .= <<< ENDHTML
       <option value="$iEntityTypeId">$strEntityName</option>
ENDHTML;
    }
    $strEntityTypes .= "</select>";
    return $strEntityTypes;
  }

  public function makeDirectory($p_strParamArray=array(0,0)){
    JPluginHelper::importPlugin( 'project', 'filesystem' );
    $oDispatcher =& JDispatcher::getInstance();
    $oResultsArray = $oDispatcher->trigger('onMkDir', $p_strParamArray);
    return $oResultsArray;
  }

  public function scaleImage(){

  }

  public function getEntityListHTML($p_strPrefix, $p_oEntityArray){
    $strReturn = StringHelper::EMPTY_STRING;

    foreach ($p_oEntityArray as $iIndex=>$oEntity){
      $strInput = $oEntity->getName();
      $strInputDiv = $p_strPrefix."-".$iIndex."Input";
      $strFieldArray = $p_strPrefix."[]";
      $strFieldPicked = $p_strPrefix."Picked";
      $strRemoveDiv = $p_strPrefix."-".$iIndex."Remove";

      $strReturn .= <<< ENDHTML
          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strFieldArray" value="$strInput"/>
            $strInput
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strInput." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/remove?format=ajax', '$p_strPrefix', $iIndex, '$strFieldPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div class="clear"></div>
ENDHTML;

    }

    return $strReturn;
  }

  public function getOrganizationListHTML($p_strPrefix, $p_oEntityArray){
    $strReturn = StringHelper::EMPTY_STRING;

    foreach ($p_oEntityArray as $iIndex=>$oEntity){
      $strInput = $oEntity->getName();
      $strInputDiv = $p_strPrefix."-".$iIndex."Input";
      $strFieldArray = $p_strPrefix."[]";
      $strFieldPicked = $p_strPrefix."Picked";
      $strRemoveDiv = $p_strPrefix."-".$iIndex."Remove";

      $strReturn .= <<< ENDHTML
          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strFieldArray" value="$strInput"/>
            $strInput
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strInput." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/removeorganization?format=ajax', '$p_strPrefix', $iIndex, '$strFieldPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div class="clear"></div>
ENDHTML;

    }

    return $strReturn;
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

  public function getOntologyByProjectId($p_iProjectId){
    return NCCuratedObjectCatalogEntryPeer::getOntologyByProjectId($p_iProjectId);
  }

  public function getOntologyByExperimentId($p_iExperimentId){
    return NCCuratedObjectCatalogEntryPeer::getOntologyByExperimentId($p_iExperimentId);
  }

  public function getResearcherKeywordsByEntity($p_iEntityId, $p_iEntityTypeId){
    return ResearcherKeywordPeer::getTagsByEntity($p_iEntityId, $p_iEntityTypeId);
  }

  /**
   * Gets the HTML for the collection of keywords.
   * @param array $p_strTagArray
   * @return string
   */
  public function getTagsInputHTML($p_strTagArray){
    $strReturn = "";

    if(!empty($p_strTagArray)){
      $strTagValues = implode(",", $p_strTagArray);

      $strReturn .= <<< ENDHTML
          <input type="text" value="$strTagValues" id="actags" name="tags" autocomplete="off" style="display:none;">
ENDHTML;
    }
      
    return $strReturn;
  }

  /**
   * Gets the HTML for the collection of keywords.
   * @param array $p_oResearcherKeywordArray
   * @return string
   */
  public function getResearcherKeywordsInputHTML($p_oResearcherKeywordArray){
    $strReturn = "";

    if(!empty($p_oResearcherKeywordArray)){
      $strKeywordArray = array();

      /* @var $oResearcherKeyword ResearcherKeyword */
      foreach($p_oResearcherKeywordArray as $iIndex=>$strResearcherKeyword){
        array_push($strKeywordArray, $strResearcherKeyword);
      }

      $strTagValues = implode(",", $strKeywordArray);

      $strReturn .= <<< ENDHTML
          <input type="text" value="$strTagValues" id="actags" name="tags" autocomplete="off" style="display:none;">
ENDHTML;
    }

    return $strReturn;
  }

  /**
   *
   * @param array $p_oOntologyArray
   * @return string
   */
  public function getOntologyInputHTML($p_oOntologyArray){
    $strReturn = "";

    if(!empty($p_oOntologyArray)){
      $strTagValues = implode(",", $p_oOntologyArray);
      
      $strReturn .= <<< ENDHTML
          <input type="text" value="$strTagValues" id="actags" name="tags" autocomplete="off" style="display:none;">
ENDHTML;
    }
    return $strReturn;
  }

  /**
   * Gets the HTML for the collection of keywords.
   * @param array $p_oResearcherKeywordArray
   * @return string
   */
  public function getResearcherKeywordsHTML($p_oResearcherKeywordArray){
    $strReturn = "";

    if(!empty($p_oResearcherKeywordArray)){
      $strReturn = "<ol class=\"tags\" style=\"margin: 0;\">";

      /* @var $oResearcherKeywordy ResearcherKeyword */
      foreach($p_oResearcherKeywordArray as $iIndex=>$oResearcherKeyword){
        $strKeywordTerm = $oResearcherKeyword->getKeywordTerm();
        $strReturn .= <<< ENDHTML
                           <li style="margin: 0;"><a href="javascript:void(0);">$strKeywordTerm</a></li>
ENDHTML;
      }
      $strReturn .= "</ol>";
    }
    return $strReturn;
  }

  /**
   * Gets the list of PI and Co-PIs.
   * @return comma seperated string of names
   */
  public function getMembersByRole($p_oProjectModel, $p_oProject, $p_iEntityId, $p_strRoleArray, $p_bIncludeUsername=false){
    $oPersonResultSet = PersonPeer::findMembersByRoleForEntity($p_oProject->getId(), $p_iEntityId, $p_strRoleArray);
    $strPIs = "";
    while($oPersonResultSet->next()) {
      $strFirstName = ucfirst($oPersonResultSet->getString('FIRST_NAME'));
      $strLastName = ucfirst($oPersonResultSet->getString('LAST_NAME'));
      $strUsername = $oPersonResultSet->getString('USER_NAME');

      if($p_bIncludeUsername){
        $strPIs .= $strFirstName ." ". $strLastName ." (".$strUsername."), ";
      }else{
        $strPIs .= $strFirstName ." ". $strLastName .", ";
      }
    }

    //remove the last comma
    $iIndexLastComma = strrpos($strPIs, ",");
    $strPIs = substr($strPIs, 0, $iIndexLastComma);
    return $strPIs;
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

    /* @var $oAuthorization Authorization */
    $oAuthorization = AuthorizationPeer::findByPersonIdEntityidEntitytype($p_iPersonId, $p_iEntityId, $p_iEntityTypeId);
    $strPermissions = $oAuthorization->getPermissions()->toString();
    $strPermissionsArray = explode(",", $strPermissions);

    /* @var $oPersonEntityRole PersonEntityRole */
    foreach($oPersonEntityRoleArray as $oPersonEntityRole){
      $oRole = $oPersonEntityRole->getRole();
      array_push($oRoleArray, $oRole);
    }//end foreach $oPersonEntityRoleArray

    return array($oRoleArray, $strPermissionsArray);
  }

  /**
   *
   * @param int $p_iEntityId
   * @param int $p_iEntityTypeId
   */
  public function deleteTagsByEntity($p_iEntityId, $p_iEntityTypeId){
    ResearcherKeywordPeer::deleteTagsByEntity($p_iEntityId, $p_iEntityTypeId);
  }

  /**
   *
   * @param string $p_strFullAndUsername
   * @return string
   */
  public function extractUsername($p_strFullAndUsername){
    $strArray = explode(" ", $p_strFullAndUsername);
    $strUsername = end($strArray);
    $strUsername = str_replace("(", "", $strUsername);
    $strUsername = str_replace(")", "", $strUsername);
    return $strUsername;
  }

  public function createThumb($p_strPhoto){
    $image = explode('.',$p_strPhoto);
    $n = count($image);
    $image[$n-2] .= '_thumb';
    $end = array_pop($image);
    $image[] = $end;
    return implode('.',$image);
  }

  public function formatId($someid){
    while (strlen($someid) < 5) {
      $someid = 0 . "$someid";
    }
    return $someid;
  }

  public function getSpecimenByProjectId($p_iProjectId){
    return SpecimenPeer::findByProject($p_iProjectId);
  }

  /**
   *
   * @param array $p_strTagArray
   * @param string $p_strCreatedByUsername
   * @return array
   */
  public function setTags($p_strTagArray, $p_strCreatedByUsername){
    //unset($_SESSION[ResearcherKeywordPeer::TABLE_NAME]);
    
    $oReasearcherKeywordArray = array();

    foreach($p_strTagArray as $strTag){
      $oResearcherKeyword = new ResearcherKeyword($strTag, date("Y-m-d H:i:s"), $p_strCreatedByUsername);
      array_push($oReasearcherKeywordArray, $oResearcherKeyword);
    }

    return $oReasearcherKeywordArray;
  }

  /**
   *
   * @param int $p_iEntityId
   * @param int $p_iEntityTypeId
   * @param array $p_oReasearcherKeywordArray
   */
  public function createResearcherKeywords($p_iEntityId, $p_iEntityTypeId, $p_oReasearcherKeywordArray, $p_oConnection=null){
    if($p_oConnection){
      ResearcherKeywordPeer::deleteTagsByEntity($p_iEntityId, $p_iEntityTypeId, $p_oConnection);

      foreach($p_oReasearcherKeywordArray as $oResearcherKeyword){
        /* @var $oResearcherKeyword ResearcherKeyword */
        $oResearcherKeyword->setEntityId($p_iEntityId);
        $oResearcherKeyword->setEntityTypeId($p_iEntityTypeId);
        $oResearcherKeyword->save();
      }
    }else{
      $oConnection = Propel::getConnection();

      try{
        $oConnection->begin();

        ResearcherKeywordPeer::deleteTagsByEntity($p_iEntityId, $p_iEntityTypeId);

        foreach($p_oReasearcherKeywordArray as $oResearcherKeyword){
          /* @var $oResearcherKeyword ResearcherKeyword */
          $oResearcherKeyword->setEntityId($p_iEntityId);
          $oResearcherKeyword->setEntityTypeId($p_iEntityTypeId);
          $oResearcherKeyword->save();
        }

        $oConnection->commit();
      }catch(Exception $e){
        $oConnection->rollback();
        throw $e;
      }
    }
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

  public function findDataFileByUsage($p_strUsage, $p_iHideExperimentIdArray, $p_iLowerLimit=1, $p_iUpperLimit=25, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByUsage($p_strUsage, $p_iHideExperimentIdArray, $p_iLowerLimit, $p_iUpperLimit, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findDataFileByUsageCount($p_strUsage, $p_iHideExperimentIdArray, $p_iProjectId=0, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0) {
    return DataFilePeer::findDataFileByUsageCount($p_strUsage, $p_iHideExperimentIdArray, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  public function findProjectEditorVideos($p_strMimeType, $p_strCommaSeparatedVideoExtensions, $p_strDirectory, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0, $p_iLowerLimit=1, $p_iUpperLimit=25) {
    return DataFilePeer::findProjectEditorVideos($p_strMimeType, $p_strCommaSeparatedVideoExtensions, $p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId, $p_iLowerLimit, $p_iUpperLimit);
  }

  public function findProjectEditorVideosCount($p_strMimeType, $p_strCommaSeparatedVideoExtensions, $p_strDirectory, $p_iProjectId, $p_iExperimentId=0, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findProjectEditorVideosCount($p_strMimeType, $p_strCommaSeparatedVideoExtensions, $p_strDirectory, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }

  /**
   *
   * @param Project $p_oProject
   * @return array <PersonEntityRole>
   */
  public function getPersonEntityRole($p_oEntity){
    return PersonEntityRolePeer::findByEntity($p_oEntity);
  }

  public function clearSession(){
    unset($_SESSION["facility"]);
    unset($_SESSION["organization"]);
    unset($_SESSION["sponsor"]);
    unset($_SESSION["website"]);
    unset($_SESSION["SUGGESTED_FACILITY_EQUIPMENT"]);
    unset($_SESSION["ERRORS"]);
    unset($_SESSION["NSF_AWARD_TYPES"]);
    unset($_SESSION[ProjectGrantPeer::TABLE_NAME]);
    unset($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
    unset($_SESSION[ResearcherKeywordPeer::TABLE_NAME]);
    unset($_SESSION[ProjectOrganizationPeer::TABLE_NAME]);
    unset($_SESSION[OrganizationPeer::TABLE_NAME]);
  }

  public function isCurator(){
    $oAuthorizer = Authorizer::getInstance();
    return $oAuthorizer->canCurate();
  }

  public function getCuratedProject($p_iProjectId){
    return NCCuratedObjectsPeer::findByProjectId($p_iProjectId);
  }

  public function getCurationStates(){
    return NCCuratedObjectsPeer::getCurationStates();
  }

  public function getConformanceLevels(){
    return NCCuratedObjectsPeer::getConformanceLevels();
  }

  public function getObjectStatus(){
    return NCCuratedObjectsPeer::getObjectStatus();
  }

  public function getReturnURL($p_strCurrentUrl=""){
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

  public function getRawReturnURL($p_strCurrentUrl=""){
    // Get current page path and querystring
    $uri  =& JURI::getInstance();
    $redirectUrl = $uri->toString(array('path', 'query'));
    if(StringHelper::hasText($p_strCurrentUrl)){
      $redirectUrl = $p_strCurrentUrl;
    }

    return $redirectUrl;
  }

}//end class

?>