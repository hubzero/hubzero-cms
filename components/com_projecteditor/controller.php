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
require_once 'api/org/nees/html/UserRequest.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'api/org/nees/util/StringHelper.php';
require_once 'api/org/nees/util/FileHelper.php';
require_once 'api/org/nees/util/UploadHelper.php';
require_once 'api/org/nees/exceptions/ValidationException.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'api/org/nees/lib/filesystem/FileCommandAPI.php';
require_once 'api/org/nees/lib/bulkupload/FileUploadReader.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/PersonEntityRole.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/UnstructuredProject.php';
require_once 'lib/data/HybridProject.php';
require_once 'lib/data/SuperProject.php';
require_once 'lib/data/StructuredProject.php';
require_once 'lib/data/ProjectGrant.php';
require_once 'lib/data/ProjectGrantPeer.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/ProjectOrganization.php';
require_once 'lib/data/ProjectOrganizationPeer.php';
require_once 'lib/data/ProjectHomepagePeer.php';
require_once 'lib/data/ProjectHomepageURL.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/data/StructuredExperiment.php';
require_once 'lib/data/Material.php';
require_once 'lib/data/MaterialType.php';
require_once 'lib/data/MaterialProperty.php';
require_once 'lib/data/MaterialFile.php';
require_once 'lib/data/MeasurementUnitPeer.php';
require_once 'lib/data/ResearcherKeyword.php';
require_once 'lib/data/CoordinateSpace.php';
require_once 'lib/data/CoordinateSpacePeer.php';
require_once 'lib/data/ResearcherKeywordPeer.php';
require_once 'lib/data/SponsorPeer.php';
require_once 'lib/data/SensorType.php';
require_once 'lib/data/LocationPlan.php';
require_once 'lib/data/SensorLocationPlan.php';
require_once 'lib/data/SensorLocation.php';
require_once 'lib/data/Specimen.php';
require_once 'lib/security/Permissions.php';
require_once 'lib/security/Authorizer.php';
require_once 'lib/util/DomainEntityType.php';
require_once 'lib/data/Trial.php';
require_once 'lib/data/Repetition.php';
require_once 'lib/data/DocumentFormatPeer.php';
require_once 'lib/data/DocumentFormat.php';
//require_once 'lib/data/EntityHistoryPeer.php';
//require_once 'lib/data/EntityHistory.php';
require_once 'lib/data/ThumbnailPeer.php';
require_once 'lib/data/DataFileArchiveList.php';
//require_once 'lib/data/curation/NCCuratedObjects.php';
//require_once 'lib/data/curation/NCCuratedObjectsPeer.php';
require_once 'util/Tuple.php';

/*
require_once('FirePHPCore/FirePHP.class.php');
ob_start();

$firephp = FirePHP::getInstance(true);
$firephp->log('com_projecteditor/controller.php');
*/

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class ProjectEditorController extends JController{

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();

    $this->registerTask( 'facilitysearch', 'getFacilityList' );
    $this->registerTask( 'organizationsearch', 'getOrganizationList' );
    $this->registerTask( 'membersearch', 'getMemberList' );
    $this->registerTask( 'sponsorsearch', 'getSponsorList' );
    $this->registerTask( 'equipment', 'getEquipmentList' );
    $this->registerTask( 'trialsearch', 'getTrialList' );
    $this->registerTask( 'gettrialinfo', 'getTrialInfo' );
    $this->registerTask( 'repetitionsearch', 'getRepetitionList' );
    $this->registerTask( 'getrepetitioninfo', 'getRepetitionInfo' );
    $this->registerTask( 'add', 'add' );
    $this->registerTask( 'addwebsite', 'addWebsite' );
    $this->registerTask( 'addorganization', 'addOrganization' );
    $this->registerTask( 'removeorganization', 'removeOrganization' );
    $this->registerTask( 'removewebsite', 'removeWebsite' );
    $this->registerTask( 'remove', 'remove' );
    $this->registerTask( 'preview', 'previewProject' );
    $this->registerTask( 'confirmproject', 'confirmProject' );
    $this->registerTask( 'previewprojectedit', 'previewExistingProject' );
    $this->registerTask( 'previewexp', 'previewNewExperiment' );
    $this->registerTask( 'saveproject', 'saveProject' );
    $this->registerTask( 'editproject', 'editProject' );
    $this->registerTask( 'specimentypesearch', 'getSpecimenTypeList');
    $this->registerTask( 'materialtypesearch', 'getMaterialTypeList' );
    $this->registerTask( 'sensortypesearch', 'getSensorTypeList');
    $this->registerTask( 'locationplansearch', 'getLocationPlanList' );
    $this->registerTask( 'addmaterial', 'addMaterial' );
    $this->registerTask( 'removematerial', 'removeMaterial' );
    $this->registerTask( 'materialpropertytypesearch', 'getMaterialPropertyTypeList' );
    $this->registerTask( 'unitsearch', 'getMeasurementUnitList' );
    $this->registerTask( 'newmember', 'addProjectMember' );
    $this->registerTask( 'savemember', 'saveMember' );
    $this->registerTask( 'removemember', 'removeMember' );
    $this->registerTask( 'saveabout', 'saveAbout' );
    $this->registerTask( 'savematerial', 'saveMaterial' );
    $this->registerTask( 'savesensor', 'saveSensor' );
    $this->registerTask( 'uploadform', 'uploadForm' );
    $this->registerTask( 'upload', 'uploadFile' );
    $this->registerTask( 'removefile', 'removeFile' );
    $this->registerTask( 'makedirectory', 'makeDirectory' );
    $this->registerTask( 'savelocationplan', 'saveLocationPlan' );
    $this->registerTask( 'savesensorfile', 'saveSensorFile' );
    $this->registerTask( 'savedrawing', 'saveDrawing' );
    $this->registerTask( 'savedatafile', 'saveDataFile' );
    $this->registerTask( 'savedatafilephoto', 'saveDataFilePhoto' );
    $this->registerTask( 'savedatafilevideo', 'saveDataFileVideo' );
    $this->registerTask( 'savetrial', 'saveTrial' );
    $this->registerTask( 'saverepetition', 'saveRepetition' );
    $this->registerTask( 'savedatafilecuraterequest', 'saveDataFileCurateRequest');
    $this->registerTask( 'savesecurity', 'saveSecurity' );
    $this->registerTask( 'savefilmstrip', 'saveFilmstrip' );
    $this->registerTask( 'savedocument', 'saveDocument' );
    $this->registerTask( 'saveanalysis', 'saveAnalysis' );
    $this->registerTask( 'savemorephotos', 'saveMorePhotos' );
    $this->registerTask( 'setexperimentaccess', 'setExperimentAccess' );
    //$this->registerTask( 'curateconfirmproject', 'curateConfirmProject' );
  }

  /*
     * Return true if user is logged in, false otherwise. Calling the redirect()
     * function on this class after a false return, will redirect to the login
     * form with an appropiately set redirectURL for ferrying client back to
     * the correct place after they login
     */
    function userloggedin()
    {
        $juser =& JFactory::getUser();

        if ($juser->get('guest'))
        {
            // Get current page path and querystring
            $uri  =& JURI::getInstance();
            $redirectUrl = $uri->toString(array('path', 'query'));

            // Code the redirect URL
            $redirectUrl = base64_encode($redirectUrl);
            $redirectUrl = '?return=' . $redirectUrl;
            $joomlaLoginUrl = '/login';
            $finalUrl = $joomlaLoginUrl . $redirectUrl;
            $finalUrl = JRoute::_($finalUrl);
            $this->_redirect = $finalUrl;

            return false;
        }
        else
            return true;
    }

  /**
   * Method to display the view
   *
   * @access    public
   */
  function display(){
  	$sViewName	= JRequest::getVar('view', 'project');
	JRequest::setVar('view', $sViewName );
    parent::display();
  }

  /**
   * Gets a list of NEES facilities based upon what the user types.
   *
   * @return strings separated by line breaks
   */
  function getFacilityList(){
    $strFacilityList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Project');
    $oOrganizationArray = $oModel->suggestFacilities($strSearchTerm, 15);
    foreach($oOrganizationArray as $iIndex=>$oOrganization){
      $strFacilityList .= $oOrganization->getName();
      if($iIndex < 15){
        $strFacilityList .= "\n";
      }
    }

    echo $strFacilityList;
  }

  /**
   * Gets a list of NEES facilities based upon what the user types.
   *
   * @return strings separated by line breaks
   */
  function getOrganizationList(){
    $strOrganizationList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Project');
    $oOrganizationArray = $oModel->suggestOrganizations($strSearchTerm, 10);
    foreach($oOrganizationArray as $iIndex=>$oOrganization){
      $strOrganizationList .= $oOrganization->getName();
      if($iIndex < 10){
        $strOrganizationList .= "\n";
      }
    }

    echo $strOrganizationList;
  }

  /**
   * Gets a list of NEES facilities based upon what the user types.
   *
   * @return strings separated by line breaks
   */
  function getMemberList(){
    $strMemberList = "";

    $strSearchTerm	= JRequest::getVar('term');
    $strNameArray = explode(",", $strSearchTerm);

    if(sizeof($strNameArray) > 2){
      $strMemberList = "Enter only one person. Add more people in Members tab.\n";
      return $strMemberList;
    }

    /* @var $oModel ProjectEditorModelMembers */
    $oModel =& $this->getModel('Members');
    $oMemberCollectionArray = $oModel->suggestMembers($strSearchTerm, 10);

    foreach($oMemberCollectionArray as $iIndex=>$oPersonArray){
      $strMemberList .= ucfirst($oPersonArray['LAST_NAME']).", ".ucfirst($oPersonArray['FIRST_NAME'])." (".$oPersonArray['USER_NAME'].")";
      if($iIndex < 10){
        $strMemberList .= "\n";
      }
    }

    echo $strMemberList;
  }

  function getSponsorList(){
    $strSponsorList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Project');
    $oSponsorArray = $oModel->suggestSponsors($strSearchTerm, 10);
    foreach($oSponsorArray as $iIndex=>$oSponsor){
      $strSponsorList .= $oSponsor->getDisplayName();
      if($iIndex < 10){
        $strSponsorList .= "\n";
      }
    }

    echo $strSponsorList;
  }

  function getEquipmentList(){
    JRequest::setVar("view", "equipment" );
    parent::display();
  }

  function getSpecimenTypeList(){
    $strSpecimenList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Experiment');
    $oSpecimentArray = $oModel->suggestSpecimen($strSearchTerm);
    foreach($oSpecimentArray as $iIndex=>$oSpecimen){
      if($oSpecimen != null){
        $strSpecimenList .= $oSpecimen->getName();
        if($iIndex < 10){
              $strSpecimenList .= "\n";
        }
      }
    }

    echo $strSpecimenList;
  }

  function getMaterialTypeList(){
    $strMaterialTypeList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Experiment');
    $oMaterialTypetArray = $oModel->suggestMaterialType($strSearchTerm);
    foreach($oMaterialTypetArray as $iIndex=>$oMaterialType){
      if($oMaterialType != null){
        $strMaterialTypeList .= $oMaterialType->getName();
        if($iIndex < 10){
              $strMaterialTypeList .= "\n";
        }
      }
    }

    echo $strMaterialTypeList;
  }

  function getMaterialPropertyTypeList(){
    $strMaterialPropertyTypeList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Mproperties');
    $oMaterialPropertyTypetArray = $oModel->findByMaterialTypePropertyDisplayName($strSearchTerm);
    foreach($oMaterialPropertyTypetArray as $iIndex=>$oMaterialPropertyType){
      if($oMaterialPropertyType != null){
        $strMaterialPropertyTypeList .= $oMaterialPropertyType->getName();
        if($iIndex < 10){
              $strMaterialPropertyTypeList .= "\n";
        }
      }
    }

    echo $strMaterialPropertyTypeList;
  }

  function getMeasurementUnitList(){
    $strMeasurementUnitList = "";

    $strSearchTerm	= JRequest::getVar('term');

    $oModel =& $this->getModel('Experiment');
    $oMeasurementUnittArray = $oModel->suggestMeasurementUnits($strSearchTerm);
    foreach($oMeasurementUnittArray as $iIndex=>$oMeasurementUnit){
      if($oMeasurementUnit != null){
        $strMeasurementUnitList .= $oMeasurementUnit->getName();
        if($iIndex < 10){
              $strMeasurementUnitList .= "\n";
        }
      }
    }

    echo $strMeasurementUnitList;
  }

  function getTrialList(){
    $strTrialList = "";

    $strSearchTitle = JRequest::getVar('term');
    $iExperimentId = JRequest::getVar('experimentId');

    /* @var $oModel ProjectEditorModelCreateTrial */
    $oModel =& $this->getModel('CreateTrial');

    $oTrialArray = $oModel->suggestTrial($iExperimentId, $strSearchTitle, 10);
    foreach($oTrialArray as $iIndex=>$oTrial){
      /* @var $oTrial Trial */
      $strTrialList .= $oTrial->getTitle();
      if($iIndex < 10){
        $strTrialList .= "\n";
      }
    }

    echo $strTrialList;
  }

  function getTrialInfo(){
    $strTrialInfo = "";

    $strSearchTitle = JRequest::getVar('term');
    $iExperimentId = JRequest::getVar('experimentId');

    /* @var $oModel ProjectEditorModelCreateTrial */
    $oModel =& $this->getModel('CreateTrial');

    /* @var $oTrial Trial */
    $oTrial = $oModel->findByExperimentIdAndTitle($iExperimentId, $strSearchTitle);
    $iTrialId = ($oTrial) ? $oTrial->getId() : 0;
    $strTitle = (StringHelper::hasText($oTrial->getTitle())) ? $oTrial->getTitle() : "";
    $strStartDate = (StringHelper::hasText($oTrial->getStartDate())) ? $oTrial->getStartDate() : "";
    $strEndDate = (StringHelper::hasText($oTrial->getEndDate())) ? $oTrial->getEndDate() : "";
    $strObjective = (StringHelper::hasText($oTrial->getObjective())) ? $oTrial->getObjective() : "";
    $strDescription = (StringHelper::hasText($oTrial->getDescription())) ? $oTrial->getDescription() : "";

    echo $strStartDate."[trialinfo]".$strEndDate."[trialinfo]".$strObjective."[trialinfo]".$strDescription."[trialinfo]".$iTrialId."[trialinfo]".$strTitle;
  }

  function getRepetitionList(){
    $strRepetitionList = "";

    $strSearchTitle = JRequest::getVar('term');
    $iTrialId = JRequest::getVar('trial');

    /* @var $oModel ProjectEditorModelCreateRepetition */
    $oModel =& $this->getModel('CreateRepetition');

    $oRepetitionArray = $oModel->suggestRepetition($iTrialId, $strSearchTitle, 10);
    foreach($oRepetitionArray as $iIndex=>$oRepetition){
      /* @var $oRepetition Repetition */
      $strRepetitionList .= $oRepetition->getTitle();
      if($iIndex < 10){
        $strRepetitionList .= "\n";
      }
    }

    echo $strRepetitionList;
  }

  function getRepetitionInfo(){
    $iRepId = JRequest::getVar('id', 0);

    /* @var $oModel ProjectEditorModelCreateRepetition */
    $oModel =& $this->getModel('CreateRepetition');

    $strStartDate = "";
    $strEndDate = "";

    /* @var $oRepetition Repetition */
    $oRepetition = $oModel->getRepetitionById($iRepId);
    if($oRepetition){
      $strStartDate = (StringHelper::hasText($oRepetition->getStartDate())) ? $oRepetition->getStartDate() : "";
      $strEndDate = (StringHelper::hasText($oRepetition->getEndDate())) ? $oRepetition->getEndDate() : "";
    }

    echo $strStartDate."[repinfo]".$strEndDate."[repinfo]".$iRepId;
  }

  /**
   * @return string
   */
  function getSensorTypeList(){
    $strSensorTypeList = "";

    $strSearchTerm	= JRequest::getVar('term');

    /* @var $oModel ProjectEditorModelSensors */
    $oModel =& $this->getModel('Sensors');
    $oSensorTypeArray = $oModel->suggestSensorTypes($strSearchTerm);

    /* @var $oSensorType SensorType */
    foreach($oSensorTypeArray as $iIndex=>$oSensorType){
      if($oSensorType){
        $strSensorTypeList .= $oSensorType->getName();
        if($iIndex < 10){
          $strSensorTypeList .= "\n";
        }
      }
    }

    echo $strSensorTypeList;
  }

  /**
   * @return string
   */
  function getLocationPlanList(){
    $strLocationPlanList = "";

    $strSearchTerm	= JRequest::getVar('term');
    $iExperimentId      = JRequest::getInt('experimentId',0);
    if(!$iExperimentId){
      echo "Experiment not provided";
      return;
    }

    /* @var $oModel ProjectEditorModelSensors */
    $oModel =& $this->getModel('Sensors');
    $oLocationPlanArray = $oModel->suggestLocationPlans($iExperimentId, $strSearchTerm);

    /* @var $oLocationPlan LocationPlan */
    foreach($oLocationPlanArray as $iIndex=>$oLocationPlan){
      if($oLocationPlan){
        $strLocationPlanList .= $oLocationPlan->getName();
        if($iIndex < 10){
          $strLocationPlanList .= "\n";
        }
      }
    }

    echo $strLocationPlanList;
  }

  function addMaterial(){
    $strMaterialName = JRequest::getVar('material');
    $strMaterialType = JRequest::getVar('type');
    $strMaterialDesc = JRequest::getVar('desc');

    $oMaterialsModel =& $this->getModel('Materials');

    $oMaterial = new Material();
    $oMaterial->setName($strMaterialName);
    $oMaterial->setDescription($strMaterialDesc);

    $oMaterialType = null;

    //get the material type
    $oMaterialType = $oMaterialsModel->findMaterialTypeByDisplayName($strMaterialType);
    if(!$oMaterialType){
      $oMaterialType = new MaterialType();
      $oMaterialType->setDisplayName($strMaterialType);

      $strSystemName = strtolower($strMaterialType);
      $strSystemName = str_replace(" ", "_", $strSystemName);
      $oMaterialType->setSystemName($strSystemName);
    }
    $oMaterial->setMaterialType($oMaterialType);

    $oMaterialArray = null;
    if(isset($_SESSION["materials"])){
      $oMaterialArray = $_SESSION["materials"];
    }else{
      $oMaterialArray = array();
    }

    array_push($oMaterialArray, serialize($oMaterial));
    $_SESSION["materials"] = $oMaterialArray;

    JRequest::setVar( 'view', 'materials' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function add(){
    $strInputField = JRequest::getVar('name');
    $strInputFieldValue = JRequest::getVar('value');

    if(isset($_SESSION[$strInputField])){
      $strInputArray = $_SESSION[$strInputField];
    }else{
      $strInputArray = array();
    }

    array_push($strInputArray, $strInputFieldValue);
    $_SESSION[$strInputField] = $strInputArray;

    JRequest::setVar( 'view', 'add' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function addPair(){
    $strInputField1 = JRequest::getVar('field1');
    $strInputFieldValue1 = JRequest::getVar('value1');

    $strInputField2 = JRequest::getVar('field2');
    $strInputFieldValue2 = JRequest::getVar('value2');
    if(isset($_SESSION[$strInputField1])){
      $strInputArray = $_SESSION[$strInputField1];
    }else{
      $strInputArray = array();
    }

    $oTuple = new Tuple($strInputFieldValue1, $strInputFieldValue2, $strInputField1, $strInputField2);
    array_push($strInputArray, $oTuple);
    $_SESSION[$strInputField1] = $strInputArray;

    JRequest::setVar( 'view', 'addpair' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function addWebsite(){
    $strInputField1 = JRequest::getVar('field1');
    $strInputFieldValue1 = JRequest::getVar('value1');

    $strInputField2 = JRequest::getVar('field2');
    $strInputFieldValue2 = JRequest::getVar('value2');

    $strInputArray = array();
    if(isset($_SESSION[ProjectHomepagePeer::TABLE_NAME])){
      $strInputArray = unserialize($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
    }

    $oProjectHomepageURL = new ProjectHomepageURL();
    $oProjectHomepageURL->setUrl($strInputFieldValue2);
    $oProjectHomepageURL->setCaption($strInputFieldValue1);
    $oProjectHomepageURL->setDescription($strInputFieldValue1);
    $oProjectHomepageURL->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_1);
    array_push($strInputArray, $oProjectHomepageURL);

    $_SESSION[ProjectHomepagePeer::TABLE_NAME] = serialize($strInputArray);

    JRequest::setVar( 'view', 'addwebsite' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function addSponsor(){
    $strInputField1 = JRequest::getVar('field1');
    $strSponsorName = JRequest::getVar('value1');

    $strInputField2 = JRequest::getVar('field2');
    $strAward = JRequest::getVar('value2');

    $strInputArray = array();
    if(isset($_SESSION[ProjectGrantPeer::TABLE_NAME])){
      $strInputArray = unserialize($_SESSION[ProjectGrantPeer::TABLE_NAME]);
    }

    if($strAward == "Award Number" && $strSponsorName == "NSF"){
        //do nothing
    }else{
      $strAwardUrl = null;
      if($strSponsorName == "NSF" && is_numeric($strAward)){
        $strAwardUrl =  "http://www.nsf.gov/awardsearch/showAward.do?AwardNumber=".$strAward;
      }

      /* @var $oProjectGrant ProjectGrant */
      $oProjectGrant = new ProjectGrant($strSponsorName, $strAward, $strAwardUrl);
      array_push($strInputArray, $oProjectGrant);
    }

    $_SESSION[ProjectGrantPeer::TABLE_NAME] = serialize($strInputArray);

    JRequest::setVar( 'view', 'addsponsor' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function addOrganization(){
    /* @var $oModel ProjectEditorModelConfirmProject */
    $oModel =& $this->getModel('ConfirmProject');

    $strInputField = JRequest::getVar('name');
    $strValue = JRequest::getVar('value');

    $oOrganizationArray = array();
    if(isset($_SESSION[OrganizationPeer::TABLE_NAME])){
      $oOrganizationArray = unserialize($_SESSION[OrganizationPeer::TABLE_NAME]);
      $oOrganization = OrganizationPeer::findByName($strValue);
      if($oOrganization){
        array_push($oOrganizationArray, $oOrganization);
      }else{
        echo "<p class='error editorInputSize'>Submit a support ticket to add $strValue.</p>";
      }

    }

    $_SESSION[OrganizationPeer::TABLE_NAME] = serialize($oOrganizationArray);

    JRequest::setVar( 'view', 'addorganization' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function remove(){
    $strInputField = JRequest::getVar('name');
    $iArrayIndex = JRequest::getVar('value');

    //get the current field array
    $strInputArray = $_SESSION[$strInputField];

    //remove the selected element
    unset($strInputArray[$iArrayIndex]);

    //save the current field array
    $_SESSION[$strInputField] = $strInputArray;

    JRequest::setVar( 'view', 'add' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  /**
   * Removes a website from the project.
   */
  function removeWebsite(){
    $iArrayIndex = JRequest::getVar('value');

    $strInputArray = array();
    if(isset($_SESSION[ProjectHomepagePeer::TABLE_NAME])){
      $strInputArray = unserialize($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
    }

    if(!empty($strInputArray)){
      unset($strInputArray[$iArrayIndex]);
    }

    $_SESSION[ProjectHomepagePeer::TABLE_NAME] = serialize($strInputArray);

    JRequest::setVar( 'view', 'addwebsite' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  function removeOrganization(){
    $iArrayIndex = JRequest::getVar('value');

    $strInputArray = array();
    if(isset($_SESSION[OrganizationPeer::TABLE_NAME])){
      $strInputArray = unserialize($_SESSION[OrganizationPeer::TABLE_NAME]);
    }

    if(!empty($strInputArray)){
      unset($strInputArray[$iArrayIndex]);
    }

    $_SESSION[OrganizationPeer::TABLE_NAME] = serialize($strInputArray);

    JRequest::setVar( 'view', 'addorganization' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  /**
   * Removes a sponsor from the project.
   */
  function removeSponsor(){
    $iArrayIndex = JRequest::getVar('value');

    $strInputArray = array();
    if(isset($_SESSION[ProjectGrantPeer::TABLE_NAME])){
      $strInputArray = unserialize($_SESSION[ProjectGrantPeer::TABLE_NAME]);
    }

    if(!empty($strInputArray)){
      unset($strInputArray[$iArrayIndex]);
    }

    $_SESSION[ProjectGrantPeer::TABLE_NAME] = serialize($strInputArray);

    JRequest::setVar( 'view', 'addsponsor' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  /**
   * Removes a material from an experiment
   */
  function removeMaterial(){
    $strInputField = JRequest::getVar('name');
    $iArrayIndex = JRequest::getVar('value');

    //get the current field array
    $strInputArray = $_SESSION[$strInputField];

    //remove the selected element
    unset($strInputArray[$iArrayIndex]);

    //save the current field array
    $_SESSION[$strInputField] = $strInputArray;

    JRequest::setVar( 'view', 'materials' );
    JRequest::setVar( 'format', 'ajax' );
    parent::display();
  }

  /**
   * Creates the required objects for previewing a new Project.
   */
  function previewProject0(){
    $strErrorArray = array();
    unset($_SESSION[ProjectEditor::PHOTO_NAME]);

    /* @var $oModel ProjectEditorModelPreview */
    $oModel =& $this->getModel('Preview');

    /*
     * Step 1: Get and validate form fields
     */
    $strTitle = JRequest::getVar("title", "");
    try{
      $strTitle = $oModel->validateText("Title", $strTitle);
    }catch(ValidationException $oException) {
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    $strShortTitle = JRequest::getVar("shortTitle", "");
    try{
      $strShortTitle = $oModel->validateText("Short Title", $strShortTitle);
    }catch(ValidationException $oException) {
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    $strStartDate = JRequest::getVar("startdate");
    try{
      $strStartDate = $oModel->validateStartDate($strStartDate);
    }catch(ValidationException $oException) {
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    $strEndDate = $oModel->validateEndDate(JRequest::getVar("enddate"));
    $strDescription = JRequest::getVar("description", "");
    $strTags = JRequest::getVar("tags", "");

    $strAccess = "";
    $iAccess = JRequest::getInt("access", 4);
    switch($iAccess){
      case 0: $strAccess = "PUBLIC"; break;
      case 3: $strAccess = "USERS"; break;
      default: $strAccess = "MEMBERS";
    }

    $strSponsor = "";
    $strAward = "";
    $strAcknowledgement = "";
    $strProjectName = "";
    $strOwner = JRequest::getVar("owner", "");
    $strAdmin = JRequest::getVar("itperson", "");
    $iNeesProject = JRequest::getInt("nees", 0);
    $iProjectTypeId = JRequest::getInt("type", ProjectPeer::CLASSKEY_STRUCTUREDPROJECT);

    $oOrganizationArray = array();
    try{
      $oOrganizationArray = $oModel->validateOrganizations($_POST['organization']);
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    /*
     * Step 2: Decide if create or edit. If is create, else is edit.
     */
    $oProject = null;
    $iProjectId = JRequest::getInt("projectId", 0);
    if(!$iProjectId){
      if(ProjectPeer::isDuplicatedTitle($strTitle)){
        $oException = new ValidationException("Title is already taken.");
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      if(ProjectPeer::isDuplicatedNickname($strShortTitle)){
        $oException = new ValidationException("Short name is already taken.");
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      try{
        $strOwner = $oModel->validateText("PI", $strOwner);
      }catch(ValidationException $oException) {
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      try{
        $strAdmin = $oModel->validateText("Administrator", $strAdmin);
      }catch(ValidationException $oException) {
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      /*
       * Validate the owner and admin.  First, we'll
       * attempt to use the auto-suggest.  If for some reason
       * the user skipped the auto-fill, use the full name.
       */
      if(empty($strErrorArray)){
        //initialize the people involved
        /* @var $oOwnerPerson Person */
        $oOwnerPerson = null;
        $strOwnerUsername = StringHelper::EMPTY_STRING;
        $strContactName = StringHelper::EMPTY_STRING;
        $strContactEmail = StringHelper::EMPTY_STRING;

        /* @var $oThisPerson Person */
        $oThisJUser = $oModel->getCurrentUser();
        $oThisPerson = $oModel->getOracleUserByUsername($oThisJUser->username);
        $iCreatorId = $oThisPerson->getId();

        /* @var $oAdminPerson Person */
        $oAdminPerson = null;
        $strAdminUsername = StringHelper::EMPTY_STRING;
        $strSysAdminName = StringHelper::EMPTY_STRING;
        $strSysAdminEmail = StringHelper::EMPTY_STRING;

        //check if using auto-fill, otherwise use last, first name
        if(preg_match(ProjectEditor::PERSON_NAME_PATTERN, $strOwner)){
          try{
            $oOwnerPerson = $oModel->getSuggestedUsername($strOwner);
          }catch(ValidationException $oException){
            array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
          }
        }else{
          try{
            $oOwnerPerson = $oModel->getPersonByLastCommaFirstName($strOwner);
          }catch(ValidationException $oException){
            array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
          }
        }

        if(!$oOwnerPerson){
          array_push($strErrorArray, "PI $strOwner not found.");
        }

        //get the admin if entered.
        if(StringHelper::hasText($strAdmin)){
          //check if using auto-fill, otherwise use last, first name
          if(preg_match(ProjectEditor::PERSON_NAME_PATTERN, $strAdmin)){
            try{
              $oAdminPerson = $oModel->getSuggestedUsername($strAdmin);
            }catch(ValidationException $oException){
              array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
            }
          }else{
            try{
              $oAdminPerson = $oModel->getPersonByLastCommaFirstName($strAdmin);
              if(!$oAdminPerson){
                array_push($strErrorArray, "Administrator $strAdmin not found.");
              }
            }catch(ValidationException $oException){
              array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
            }
          }
        }//end strAdmin

        //ok, we should have valid usernames
        if(empty($strErrorArray)){
          $strOwnerUsername = $oOwnerPerson->getUsername();
          $_SESSION[ProjectEditor::PROJECT_OWNER_USERNAME] = $strOwnerUsername;

          $strContactName = $oOwnerPerson->getFirstName()." ".$oOwnerPerson->getLastName();
          $strContactEmail = $oOwnerPerson->getEMail();
          $iCreatorId = $oOwnerPerson->getId();

          if($oAdminPerson){
            $strAdminUsername = $oAdminPerson->getUserName();
            $_SESSION[ProjectEditor::PROJECT_ADMIN_USERNAME] = $strAdminUsername;

            $strSysAdminName = $oAdminPerson->getFirstName()." ".$oAdminPerson->getLastName();
            $strSysAdminEmail = $oAdminPerson->getEMail();
          }
        }

        //maybe a little overkill, but make sure we have a proper person.id
//        if(!$iCreatorId){
//          array_push($strErrorArray, "Warehouse user $strOwner not found.");
//        }
      }//end owner admin validation

      if(empty($strErrorArray)){
        $oProject = $oModel->createProject($strTitle, $strDescription, $strContactName, $strContactEmail,
                                   $strSysAdminName, $strSysAdminEmail, $strStartDate, $strEndDate,
                                   $strAcknowledgement, $strAccess, $iProjectTypeId, $iNeesProject, $strShortTitle,
                                   $strSponsor, $strAward, $strProjectName, $iCreatorId);
      }
    }else{
      if(empty($strErrorArray)){
        $oProject = $oModel->getProjectById($iProjectId);
        if($oProject){
          $oProject->setDescription($strDescription);
          $oProject->setView($strAccess);
          $oProject->setNEES($iNeesProject);
          $oProject->setTitle($strTitle);
          $oProject->setNickname($strShortTitle);
          $oProject->setStartDate($strStartDate);
          if(StringHelper::hasText($strEndDate)){
            $oProject->setEndDate($strEndDate);
          }
        }else{
          array_push($strErrorArray, "Project (".$iProjectId.") not found.");
        }
      }
    }//end building or getting project

    if($oProject){
      $_SESSION[ProjectPeer::TABLE_NAME] = serialize($oProject);

      /*
       * Step 3: Add the Organizations
       */
      $_SESSION[OrganizationPeer::TABLE_NAME] = serialize($oOrganizationArray);

      /*
       * Step 4: Add the Sponsors
       */
      $oProjectGrantArray = $oModel->setSponsors($_POST["sponsor"], $_POST["award"]);
      $_SESSION[ProjectGrantPeer::TABLE_NAME] = serialize($oProjectGrantArray);

      /*
       * Step 5: Add the Websites
       */
      $strWebsiteArray = $_POST['website'];
      $strUrlArray = $_POST['url'];
      $oProjectUrlArray = $oModel->setWebsites($strWebsiteArray, $strUrlArray);
      $_SESSION[ProjectHomepagePeer::TABLE_NAME] = serialize($oProjectUrlArray);

      /*
       * Step 6: Add the Tags
       */
      $oReasearcherKeywordArray = array();
      if(StringHelper::hasText($strTags)){
        $oHubUser = $oModel->getCurrentUser();

        $strTagsArray = explode(",", $strTags);
        $oReasearcherKeywordArray = $oModel->setTags($strTagsArray, $oHubUser->username);
      }
      $_SESSION[ResearcherKeywordPeer::TABLE_NAME] = serialize($oReasearcherKeywordArray);

      /*
       * Step7: Upload project photo (if available)
       * Photos are temporarily written to /com_projecteditor/uploads/members/<username>.
       */
      $strTmpFileName = $_FILES[ProjectEditor::UPLOAD_FIELD_NAME]['tmp_name'];
      if(empty($strErrorArray) && StringHelper::hasText($strTmpFileName)){
        JPluginHelper::importPlugin( 'project', 'upload' );
        $oDispatcher =& JDispatcher::getInstance();
        $strParamArray = array(0,0);
        $strResultsArray = $oDispatcher->trigger('onPhotoPreviewUpload',$strParamArray);
        $strFileName = $strResultsArray[0];
        if(is_numeric($strFileName)){
          //got error code instead of file name
          $strMessage = UploadHelper::getErrorMessage($strFileName);
          array_push($strErrorArray, $strMessage);
          //echo ComponentHtml::showError($strMessage);
        }
        if(StringHelper::hasText($strFileName)){
          $_SESSION[ProjectEditor::PHOTO_NAME] = $strFileName;

          $strCaption = JRequest::getVar("desc", "");
          if(!StringHelper::hasText($strCaption)){
            $strCaption = StringHelper::neat_trim($oProject->getDescription(), 25);
          }
          $_SESSION[ProjectEditor::PHOTO_CAPTION] = $strCaption;

          $iUsageTypeId = JRequest::getInt('usageType');
          $_SESSION[ProjectEditor::PHOTO_USAGE_TYPE_ID] = $iUsageTypeId;
        }
      }//end if upload
    }//end if oProject

    /*
     * Step 8: Get the preview or project view.
     */
    $strView = "preview";
    if(!empty($strErrorArray)){
      $strView = "project";
      //$_REQUEST["organization"] = (StringHelper::hasText($strOrganization)) ? $strOrganization : StringHelper::EMPTY_STRING;
      //$_REQUEST["sponsor"] = (StringHelper::hasText($strSponsor)) ? $strSponsor : "NSF";
      //$_REQUEST["award"] = (StringHelper::hasText($strAward)) ? $strAward : "Award Number";
      //$_REQUEST["website"] = (StringHelper::hasText($strWebsiteName)) ? $strWebsiteName : $strShortTitle;
      //$_REQUEST["url"] = (StringHelper::hasText($strWebsiteUrl)) ? $strWebsiteUrl : ProjectEditor::DEFAULT_PROJECT_URL;
    }

    $oAuthorizer = Authorizer::getInstance();

    /* @var $oThisPerson Person */
    $oThisPerson = $oAuthorizer->getUser();

    //just in case user wants to continue editing or there's an error
    $_REQUEST["ERRORS"] = $strErrorArray;
    $_REQUEST["access"] = (StringHelper::hasText($strAccess)) ? $iAccess : 4;
    $_REQUEST["nees"] = (StringHelper::hasText($iNeesProject)) ? $iNeesProject : 1;
    $_REQUEST["owner"] = (StringHelper::hasText($strOwner)) ? $strOwner : "Last, First name";
    if($iProjectId > 0){
      $_REQUEST["projid"] = $iProjectId;
      if(!$oProject){
        $oProject = $oModel->getProjectById($iProjectId);
      }
      $_REQUEST["owner"] = $oModel->getMembersByRole($oModel, $oProject, 1, array("Principal Investigator", "Co-PI"), true);
    }
    $_REQUEST["itperson"] = (StringHelper::hasText($strAdmin)) ? $strAdmin : ucfirst($oThisPerson->getLastName()).", ".ucfirst($oThisPerson->getFirstName())." (".$oThisPerson->getUserName().")";
    $_REQUEST["title"] = $strTitle;
    $_REQUEST["shortTitle"] = $strShortTitle;
    $_REQUEST["startdate"] = (StringHelper::hasText($strStartDate)) ? $strStartDate : date("m/d/Y");
    $_REQUEST["enddate"] = (StringHelper::hasText($strEndDate)) ? $strEndDate : "mm/dd/yyyy";
    $_REQUEST["description"] = (StringHelper::hasText($strDescription)) ? $strDescription : StringHelper::EMPTY_STRING;
    $_REQUEST["tags"] = (StringHelper::hasText($strTags)) ? $strTags : StringHelper::EMPTY_STRING;

    JRequest::setVar( 'view', $strView );
    parent::display();
  }

  /**
   * Creates the required objects for previewing a new Project.
   */
  function confirmProject(){
    $strErrorArray = array();
    unset($_SESSION[ProjectEditor::PHOTO_NAME]);

    /* @var $oModel ProjectEditorModelConfirmProject */
    $oModel =& $this->getModel('ConfirmProject');

    /*
     * Step 1: Get and validate form fields
     */
    $strTitle = JRequest::getVar("title", "");
    try{
      $strTitle = $oModel->validateText("Title", $strTitle);
    }catch(ValidationException $oException) {
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    $strShortTitle = JRequest::getVar("shortTitle", "");
    try{
      $strShortTitle = $oModel->validateText("Short Title", $strShortTitle);
    }catch(ValidationException $oException) {
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    $strStartDate = JRequest::getVar("startdate");
    try{
      $strStartDate = $oModel->validateStartDate($strStartDate);
    }catch(ValidationException $oException) {
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    $strEndDate = $oModel->validateEndDate(JRequest::getVar("enddate"));
    $strDescription = JRequest::getVar("description", "");
    $strTags = JRequest::getVar("tags", "");

    $strAccess = "";
    $iAccess = JRequest::getInt("access", 4);
    switch($iAccess){
      case 0: $strAccess = "PUBLIC"; break;
      case 3: $strAccess = "USERS"; break;
      default: $strAccess = "MEMBERS";
    }

    $strSponsor = "";
    $strAward = "";
    $strAcknowledgement = "";
    $strProjectName = "";
    $strOwner = JRequest::getVar("owner", "");
    $strAdmin = JRequest::getVar("itperson", "");
    $iNeesProject = JRequest::getInt("nees", 0);
    $iProjectTypeId = JRequest::getInt("type", ProjectPeer::CLASSKEY_STRUCTUREDPROJECT);

    $oOrganizationArray = array();
    try{
      $oOrganizationArray = $oModel->validateOrganizations($_POST['organization']);
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
    }

    /*
     * Step 2: Decide if create or edit. If is create, else is edit.
     */
    $oProject = null;
    $iProjectId = JRequest::getInt("projectId", 0);
    if(!$iProjectId){
      if(ProjectPeer::isDuplicatedTitle($strTitle)){
        $oException = new ValidationException("Title is already taken.");
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      if(ProjectPeer::isDuplicatedNickname($strShortTitle)){
        $oException = new ValidationException("Short name is already taken.");
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      try{
        $strOwner = $oModel->validateText("PI", $strOwner);
      }catch(ValidationException $oException) {
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      try{
        $strAdmin = $oModel->validateText("Administrator", $strAdmin);
      }catch(ValidationException $oException) {
        array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
      }

      /*
       * Validate the owner and admin.  First, we'll
       * attempt to use the auto-suggest.  If for some reason
       * the user skipped the auto-fill, use the full name.
       */
      if(empty($strErrorArray)){
        //initialize the people involved
        /* @var $oOwnerPerson Person */
        $oOwnerPerson = null;
        $strOwnerUsername = StringHelper::EMPTY_STRING;
        $strContactName = StringHelper::EMPTY_STRING;
        $strContactEmail = StringHelper::EMPTY_STRING;

        /* @var $oThisPerson Person */
        $oThisJUser = $oModel->getCurrentUser();
        $oThisPerson = $oModel->getOracleUserByUsername($oThisJUser->username);
        $iCreatorId = $oThisPerson->getId();

        /* @var $oAdminPerson Person */
        $oAdminPerson = null;
        $strAdminUsername = StringHelper::EMPTY_STRING;
        $strSysAdminName = StringHelper::EMPTY_STRING;
        $strSysAdminEmail = StringHelper::EMPTY_STRING;

        //check if using auto-fill, otherwise use last, first name
        if(preg_match(ProjectEditor::PERSON_NAME_PATTERN, $strOwner)){
          try{
            $oOwnerPerson = $oModel->getSuggestedUsername($strOwner);
          }catch(ValidationException $oException){
            array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
          }
        }else{
          try{
            $oOwnerPerson = $oModel->getPersonByLastCommaFirstName($strOwner);
          }catch(ValidationException $oException){
            array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
          }
        }

        if(!$oOwnerPerson){
          array_push($strErrorArray, "PI $strOwner not found.");
        }

        //get the admin if entered.
        if(StringHelper::hasText($strAdmin)){
          //check if using auto-fill, otherwise use last, first name
          if(preg_match(ProjectEditor::PERSON_NAME_PATTERN, $strAdmin)){
            try{
              $oAdminPerson = $oModel->getSuggestedUsername($strAdmin);
            }catch(ValidationException $oException){
              array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
            }
          }else{
            try{
              $oAdminPerson = $oModel->getPersonByLastCommaFirstName($strAdmin);
              if(!$oAdminPerson){
                array_push($strErrorArray, "Administrator $strAdmin not found.");
              }
            }catch(ValidationException $oException){
              array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
            }
          }
        }//end strAdmin

        //ok, we should have valid usernames
        if(empty($strErrorArray)){
          $strOwnerUsername = $oOwnerPerson->getUsername();
          $_SESSION[ProjectEditor::PROJECT_OWNER_USERNAME] = $strOwnerUsername;

          $strContactName = $oOwnerPerson->getFirstName()." ".$oOwnerPerson->getLastName();
          $strContactEmail = $oOwnerPerson->getEMail();
          //$iCreatorId = $oOwnerPerson->getId();

          if($oAdminPerson){
            $strAdminUsername = $oAdminPerson->getUserName();
            $_SESSION[ProjectEditor::PROJECT_ADMIN_USERNAME] = $strAdminUsername;

            $strSysAdminName = $oAdminPerson->getFirstName()." ".$oAdminPerson->getLastName();
            $strSysAdminEmail = $oAdminPerson->getEMail();
          }
        }

        //maybe a little overkill, but make sure we have a proper person.id
//        if(!$iCreatorId){
//          array_push($strErrorArray, "Warehouse user $strOwner not found.");
//        }
      }//end owner admin validation

      if(empty($strErrorArray)){
        $oProject = $oModel->createProject($strTitle, $strDescription, $strContactName, $strContactEmail,
                                   $strSysAdminName, $strSysAdminEmail, $strStartDate, $strEndDate,
                                   $strAcknowledgement, $strAccess, $iProjectTypeId, $iNeesProject, $strShortTitle,
                                   $strSponsor, $strAward, $strProjectName, $iCreatorId);
      }
    }else{
      if(empty($strErrorArray)){
        $oProject = $oModel->getProjectById($iProjectId);
        if($oProject){
          $oProject->setDescription($strDescription);
          $oProject->setView($strAccess);
          $oProject->setNEES($iNeesProject);
          $oProject->setTitle($strTitle);
          $oProject->setNickname($strShortTitle);
          $oProject->setStartDate($strStartDate);
          if(StringHelper::hasText($strEndDate)){
            $oProject->setEndDate($strEndDate);
          }
        }else{
          array_push($strErrorArray, "Project (".$iProjectId.") not found.");
        }
      }
    }//end building or getting project

    if($oProject){
      $_SESSION[ProjectPeer::TABLE_NAME] = serialize($oProject);

      /*
       * Step 3: Add the Organizations
       */
      $_SESSION[OrganizationPeer::TABLE_NAME] = serialize($oOrganizationArray);

      /*
       * Step 4: Add the Sponsors
       */
      $oProjectGrantArray = $oModel->setSponsors($_POST["sponsor"], $_POST["award"]);
      $_SESSION[ProjectGrantPeer::TABLE_NAME] = serialize($oProjectGrantArray);

      /*
       * Step 5: Add the Websites
       */
      $strWebsiteArray = $_POST['website'];
      $strUrlArray = $_POST['url'];
      $oProjectUrlArray = $oModel->setWebsites($strWebsiteArray, $strUrlArray);
      $_SESSION[ProjectHomepagePeer::TABLE_NAME] = serialize($oProjectUrlArray);

      /*
       * Step 6: Add the Tags
       */
      $oReasearcherKeywordArray = array();
      if(StringHelper::hasText($strTags)){
        $oHubUser = $oModel->getCurrentUser();

        $strTagsArray = explode(",", $strTags);
        $oReasearcherKeywordArray = $oModel->setTags($strTagsArray, $oHubUser->username);
      }
      $_SESSION[ResearcherKeywordPeer::TABLE_NAME] = serialize($oReasearcherKeywordArray);

      /*
       * Step7: Upload project photo (if available)
       * Photos are temporarily written to /com_projecteditor/uploads/members/<username>.
       */
      $strTmpFileName = $_FILES[ProjectEditor::UPLOAD_FIELD_NAME]['tmp_name'];
      if(empty($strErrorArray) && StringHelper::hasText($strTmpFileName)){
        JPluginHelper::importPlugin( 'project', 'upload' );
        $oDispatcher =& JDispatcher::getInstance();
        $strParamArray = array(0,0);
        $strResultsArray = $oDispatcher->trigger('onPhotoPreviewUpload',$strParamArray);
        $strFileName = $strResultsArray[0];
        if(is_numeric($strFileName)){
          //got error code instead of file name
          $strMessage = UploadHelper::getErrorMessage($strFileName);
          array_push($strErrorArray, $strMessage);
          //echo ComponentHtml::showError($strMessage);
        }
        if(StringHelper::hasText($strFileName)){
          $_SESSION[ProjectEditor::PHOTO_NAME] = $strFileName;

          $strCaption = JRequest::getVar("desc", "");
          if(!StringHelper::hasText($strCaption)){
            $strCaption = StringHelper::neat_trim($oProject->getDescription(), 25);
          }
          $_SESSION[ProjectEditor::PHOTO_CAPTION] = $strCaption;

          $iUsageTypeId = JRequest::getInt('usageType');
          $_SESSION[ProjectEditor::PHOTO_USAGE_TYPE_ID] = $iUsageTypeId;
        }
      }//end if upload
    }//end if oProject

    /*
     * Step 8: Get the confirmation or project view.
     */
    $strView = "confirmproject";
    if(!empty($strErrorArray)){
      $strView = "project";
      //$_REQUEST["organization"] = (StringHelper::hasText($strOrganization)) ? $strOrganization : StringHelper::EMPTY_STRING;
      //$_REQUEST["sponsor"] = (StringHelper::hasText($strSponsor)) ? $strSponsor : "NSF";
      //$_REQUEST["award"] = (StringHelper::hasText($strAward)) ? $strAward : "Award Number";
      //$_REQUEST["website"] = (StringHelper::hasText($strWebsiteName)) ? $strWebsiteName : $strShortTitle;
      //$_REQUEST["url"] = (StringHelper::hasText($strWebsiteUrl)) ? $strWebsiteUrl : ProjectEditor::DEFAULT_PROJECT_URL;
    }

    $oAuthorizer = Authorizer::getInstance();

    /* @var $oThisPerson Person */
    $oThisPerson = $oAuthorizer->getUser();

    //just in case user wants to continue editing or there's an error
    $_REQUEST["ERRORS"] = $strErrorArray;
    $_REQUEST["access"] = (StringHelper::hasText($strAccess)) ? $iAccess : 4;
    $_REQUEST["nees"] = (StringHelper::hasText($iNeesProject)) ? $iNeesProject : 1;
    $_REQUEST["owner"] = (StringHelper::hasText($strOwner)) ? $strOwner : "Last, First name";
    if($iProjectId > 0){
      $_REQUEST["projid"] = $iProjectId;
      if(!$oProject){
        $oProject = $oModel->getProjectById($iProjectId);
      }
      $_REQUEST["owner"] = $oModel->getMembersByRole($oModel, $oProject, 1, array("Principal Investigator", "Co-PI"), true);
    }
    $_REQUEST["itperson"] = (StringHelper::hasText($strAdmin)) ? $strAdmin : ucfirst($oThisPerson->getLastName()).", ".ucfirst($oThisPerson->getFirstName())." (".$oThisPerson->getUserName().")";
    $_REQUEST["title"] = $strTitle;
    $_REQUEST["shortTitle"] = $strShortTitle;
    $_REQUEST["startdate"] = (StringHelper::hasText($strStartDate)) ? $strStartDate : date("m/d/Y");
    $_REQUEST["enddate"] = (StringHelper::hasText($strEndDate)) ? $strEndDate : "mm/dd/yyyy";
    $_REQUEST["description"] = (StringHelper::hasText($strDescription)) ? $strDescription : StringHelper::EMPTY_STRING;
    $_REQUEST["tags"] = (StringHelper::hasText($strTags)) ? $strTags : StringHelper::EMPTY_STRING;

    JRequest::setVar( 'view', $strView );
    parent::display();
  }


//  function curateConfirmProject(){
//    $strErrorArray = array();
//    unset($_SESSION[ProjectEditor::PHOTO_NAME]);
//
//    /* @var $oModel ProjectEditorModelConfirmProject */
//    $oModel =& $this->getModel('ConfirmProject');
//
//    $iProjectId = JRequest::getInt("projectId", 0);
//    $oProject = $oModel->getProjectById($iProjectId);
//    if(!$oProject){
//      array_push($strErrorArray, "Project (".$iProjectId.") not found.");
//      return;
//    }
//
//    $_SESSION[ProjectPeer::TABLE_NAME] = serialize($oProject);
//
//    /*
//     * Step 1: Get and validate form fields
//     */
//    $strTitle = JRequest::getVar("title", "");
//    try{
//      $strTitle = $oModel->validateText("Title", $strTitle);
//    }catch(ValidationException $oException) {
//      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
//    }
//
//    $strShortTitle = JRequest::getVar("shortTitle", "");
//    try{
//      $strShortTitle = $oModel->validateText("Short Title", $strShortTitle);
//    }catch(ValidationException $oException) {
//      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
//    }
//
//    $strStartDate = JRequest::getVar("startdate");
//    try{
//      $strStartDate = $oModel->validateStartDate($strStartDate);
//    }catch(ValidationException $oException) {
//      array_push($strErrorArray, $oException->getEntityMessage("Create Project"));
//    }
//
//    $strEndDate = $oModel->validateEndDate(JRequest::getVar("enddate"));
//    $strDescription = JRequest::getVar("description", "");
//    $strTags = JRequest::getVar("tags", "");
//
//    $strAccess = "";
//    $iAccess = JRequest::getInt("access", 4);
//    switch($iAccess){
//      case 0: $strAccess = "Public"; break;
//      case 3: $strAccess = "Users"; break;
//      default: $strAccess = "Project Members";
//    }
//
//    $strConformanceLevel = JRequest::getVar('conformanceLevel', '');
//    try{
//      $strConformanceLevel = $oModel->validateText("Conformance Level", $strConformanceLevel);
//    }catch(ValidationException $oException) {
//      array_push($strErrorArray, $oException->getEntityMessage("Curate Project"));
//    }
//
//    $strCurationState = JRequest::getVar('curationState', '');
//    try{
//      $strCurationState = $oModel->validateText("Curation State", $strCurationState);
//    }catch(ValidationException $oException) {
//      array_push($strErrorArray, $oException->getEntityMessage("Curate Project"));
//    }
//
//    $strObjectStatus = JRequest::getVar('objectStatus', '');
//    try{
//      $strObjectStatus = $oModel->validateText("Object Status", $strObjectStatus);
//    }catch(ValidationException $oException) {
//      array_push($strErrorArray, $oException->getEntityMessage("Object Status"));
//    }
//
//    $strObjectCreationDate = null;
//    $oEntityHistory = EntityHistoryPeer::findByEntity($oProject->getId(), 1, EntityHistoryPeer::ACTION_TYPE_INSERT);
//    if(!$oEntityHistory){
//      $strObjectCreationDate = $oProject->getStartDate();
//    }
//
//    $oHubUser = $oModel->getCurrentUser();
//
//    /* @var $oThisPerson Person */
//    $oThisPerson = $oModel->getOracleUserByUsername($oHubUser->username);
//
//    /* @var $oNCCuratedObjects NCCuratedObjects */
//    $oNCCuratedObject = null;
//
//    $iObjectId = JRequest::getInt("objectId", 0);
//    if(!$iObjectId){
//      //create
//      $oNCCuratedObject = new NCCuratedObjects("Project", $strTitle,
//                              $strShortTitle, $strDescription,
//                              $strObjectCreationDate, $strCurationState,
//                              $strAccess, $strObjectStatus,
//                              $strConformanceLevel, $oThisPerson->getUserName(),
//                              date("m/d/Y"), $oThisPerson->getUserName(),
//                              date("m/d/Y"));
//    }else{
//      //edit
//      $oNCCuratedObject = $oModel->getCuratedProject($oProject->getId());
//      $oNCCuratedObject->setModifiedBy($oThisPerson->getUserName());
//      $oNCCuratedObject->setModifiedDate(date("m/d/Y"));
//    }
//    $_SESSION[NCCuratedObjectsPeer::TABLE_NAME] = serialize($oNCCuratedObject);
//
//
//    /*
//     * Step 8: Get the confirmation or project view.
//     */
//    $strView = "curateconfirmproject";
//    if(!empty($strErrorArray)){
//      $strView = "project";
//    }
//
//    $oAuthorizer = Authorizer::getInstance();
//
//    /* @var $oThisPerson Person */
//    $oThisPerson = $oAuthorizer->getUser();
//
//    //just in case user wants to continue editing or there's an error
//    $_REQUEST["ERRORS"] = $strErrorArray;
//    $_REQUEST["access"] = (StringHelper::hasText($strAccess)) ? $iAccess : 4;
//    //$_REQUEST["nees"] = (StringHelper::hasText($iNeesProject)) ? $iNeesProject : 1;
//    //$_REQUEST["owner"] = (StringHelper::hasText($strOwner)) ? $strOwner : "Last, First name";
//    if($iProjectId > 0){
//      $_REQUEST["projid"] = $iProjectId;
//      if(!$oProject){
//        $oProject = $oModel->getProjectById($iProjectId);
//      }
//      $_REQUEST["owner"] = $oModel->getMembersByRole($oModel, $oProject, 1, array("Principal Investigator", "Co-PI"), true);
//    }
//    //$_REQUEST["itperson"] = (StringHelper::hasText($strAdmin)) ? $strAdmin : ucfirst($oThisPerson->getLastName()).", ".ucfirst($oThisPerson->getFirstName())." (".$oThisPerson->getUserName().")";
//    $_REQUEST["title"] = $strTitle;
//    $_REQUEST["shortTitle"] = $strShortTitle;
//    $_REQUEST["startdate"] = (StringHelper::hasText($strStartDate)) ? $strStartDate : date("m/d/Y");
//    $_REQUEST["enddate"] = (StringHelper::hasText($strEndDate)) ? $strEndDate : "mm/dd/yyyy";
//    $_REQUEST["description"] = (StringHelper::hasText($strDescription)) ? $strDescription : StringHelper::EMPTY_STRING;
//    $_REQUEST["tags"] = (StringHelper::hasText($strTags)) ? $strTags : StringHelper::EMPTY_STRING;
//
//    JRequest::setVar( 'view', $strView );
//    parent::display();
//  }


  /**
   * Creates or edits a Project and it's attributes.
   *
   */
  function saveProject(){
    $strErrorArray = array();
    $strOwnerUsername = StringHelper::EMPTY_STRING;
    $strAdminUsername = StringHelper::EMPTY_STRING;

    //get the keywords.  if all goes well, we will use it twice.
    $oReasearcherKeywordArray = unserialize($_SESSION[ResearcherKeywordPeer::TABLE_NAME]);
    $bUpload = false;
    $bNewDirs = false;

    /* @var $oModel ProjectEditorModelProject */
    $oModel =& $this->getModel('Project');

    /*
     * Step 1: Save the project.
     * @var $oProject Project
     */
    $oProject = unserialize($_SESSION[ProjectPeer::TABLE_NAME]);

    $oConnection = Propel::getConnection();

    try{
      $oConnection->begin();

      $oProject->save();
      $_SESSION[ProjectEditor::ACTIVE_PROJECT] = $oProject->getId();

      $bEditProject = JRequest::getInt('edit', 0);

      /*
       * Step 2: If creating, set the project's name, PI role, and Admin role.
       */
      $strProjectName = "";
      if( empty($strErrorArray) && $oProject->getId() > 0 ){
        $iProjectId = $oProject->getId();
        $strProjectName = StringHelper::EMPTY_STRING;
        if(!$bEditProject){
          $strProjectName = sprintf("NEES-%04s-%04d",date("Y"), $iProjectId);
          $oProject->setName($strProjectName);
        }else{
          $strProjectName = $oProject->getName();
        }

        /*
         * We are creating a project.  Set authorization and roles.
         */
        if(!$bEditProject){
          $strOwnerUsername = $_SESSION[ProjectEditor::PROJECT_OWNER_USERNAME];

          /*@var $oOwnerPerson Person */
          $oOwnerPerson = $oModel->getOracleUserByUsername($strOwnerUsername);

          //create the authorization entry for the pi
          $oPiAuthorization = $oModel->createAuthorization($oOwnerPerson->getId(), $oProject->getId());

          //create the person_entity_role entry for the pi
          $oPersonEntityRole = $oModel->createPersonEntityRole($oOwnerPerson->getId(), $oProject->getId(), "Principal Investigator");

          $strAdminUsername = $_SESSION[ProjectEditor::PROJECT_ADMIN_USERNAME];
          if(StringHelper::hasText($strAdminUsername)){
            /*@var $oAdminPerson Person */
            $oAdminPerson = $oModel->getOracleUserByUsername($strAdminUsername);

            //if admin is not pi, create the authorization entry for the sys admin
            if($oAdminPerson->getId() != $oOwnerPerson->getId()){
              $oAdminAuthorization = $oModel->createAuthorization($oAdminPerson->getId(), $oProject->getId());
            }

            //create the person_entity_role entry for the sys admin
            $oPersonEntityRole = $oModel->createPersonEntityRole($oAdminPerson->getId(), $oProject->getId(), "IT Administrator");
          }
        }//end role and authorization
      }//end if project created

      /*
       * Step 3: If insert orgs, sponsors, websites, and keywords
       */
      if( empty($strErrorArray) && $oProject->getId() > 0 ){
        $oOrganizationArray = (isset($_POST['organization'])) ? $_POST['organization'] : array();
        $oModel->createProjectOrganizations($oProject, $oOrganizationArray, $oConnection);

        $oProjectGrantArray = unserialize($_SESSION[ProjectGrantPeer::TABLE_NAME]);
        $oModel->createProjectGrants($oProject, $oProjectGrantArray, $oConnection);

        $oProjectUrlArray = unserialize($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
        $oModel->createProjectHomepages($oProject, $oProjectUrlArray, $oConnection);

        $oModel->createResearcherKeywords($oProject->getId(), 1, $oReasearcherKeywordArray, $oConnection);
      }//end orgs, sponsors, websites, keywords

      if( empty($strErrorArray) && $oProject->getId() > 0 ){
        /*
         * Create directories for new projects.
         */
        if(!$bEditProject){
          $oProjectDir = FileCommandAPI::create("/$strProjectName");
          $oProjectDir->mkdir(TRUE);

          $fileDoc = FileCommandAPI::create("/$strProjectName/Documentation");
          $fileDoc->mkdir(TRUE);

          $fileDocPhoto = FileCommandAPI::create("/$strProjectName/Documentation/Photos");
          $fileDocPhoto->mkdir(TRUE);

          $fileDocVideo = FileCommandAPI::create("/$strProjectName/Documentation/Videos");
          $fileDocVideo->mkdir(TRUE);

          $fileDocMovie = FileCommandAPI::create("/$strProjectName/Documentation/Videos/Movies");
          $fileDocMovie->mkdir(TRUE);

          $fileDocFrame = FileCommandAPI::create("/$strProjectName/Documentation/Videos/Frames");
          $fileDocFrame->mkdir(TRUE);

          $filePub = FileCommandAPI::create("/$strProjectName/Public");
          $filePub->mkdir(TRUE);

          $fileAna = FileCommandAPI::create("/$strProjectName/Analysis");
          $fileAna->mkdir(TRUE);

          //$filePhotos = FileCommandAPI::create("/$strProjectName/Photos");
          //$filePhotos->mkdir(TRUE);

          //$fileVideoMovies = FileCommandAPI::create("/$strProjectName/Videos/Movies");
          //$fileVideoMovies->mkdir(TRUE);

          //$fileVideoPhotos = FileCommandAPI::create("/$strProjectName/Videos/Frames");
          //$fileVideoPhotos->mkdir(TRUE);

          $bNewDirs = true;
        }//end new directories

        $strProjectPath = "/nees/home/$strProjectName.groups";
        $strResultsArray = array();

        //Check to see if a new file was uploaded.
        if( isset($_SESSION[ProjectEditor::PHOTO_NAME]) ){
          $strImageFileName = $_SESSION[ProjectEditor::PHOTO_NAME];
          $strImageCaption = $_SESSION[ProjectEditor::PHOTO_CAPTION];
          $iUsageTypeId = $_SESSION[ProjectEditor::PHOTO_USAGE_TYPE_ID];

          //create the source
          $oHubUser = $oModel->getCurrentUser();
          $strSourcePath = ProjectEditor::PROJECT_UPLOAD_DIR."/".$oHubUser->username."/".$strImageFileName;

          //create the destination
          $strDestinationPath = $strProjectPath.ProjectEditor::PHOTO_DESTINATION_SUFFIX;
          $oFileCommand = FileCommandAPI::create($strDestinationPath);
          $oFileCommand->mkdir(TRUE);

          //if destination file exists, remove original
          $bCopied = copy($strSourcePath, $strDestinationPath."/".$strImageFileName);
          if($bCopied){
            unlink($strSourcePath);
          }

          //delete the old thumbnail and update the icon as the new thumbnail
          $oModel->clearOldProjectImages($oProject->getId());

          //setup the plugin
          JRequest::setVar('name', $strImageFileName);
          JRequest::setVar('title', $oProject->getTitle());
          JRequest::setVar('desc', $strImageCaption);
          JRequest::setVar('path', $strDestinationPath);
          JRequest::setVar('usageType', $iUsageTypeId);
          JRequest::setVar('fixPermissions', 0);

          //execute plugin
          JPluginHelper::importPlugin( 'project', 'upload' );
          $oDispatcher =& JDispatcher::getInstance();
          $strParamArray = array(0,0);
          $strResultsArray = $oDispatcher->trigger('onProjectPhotoUpload',$strParamArray);

          $bUpload = true;
        }//end photo upload
      }

      $oConnection->commit();
    }catch(Exception $e){
      $oConnection->rollback();
      array_push($strErrorArray, "Unable to save Project.");
    }//end project and attributes creation

    $bGroupCreated = false;
    if(empty($strErrorArray)  && !$bEditProject){
      /*
       * Create group
       */
      try{
        $bAddAdmin = false;

        $oPiJuser =& JFactory::getUser($strOwnerUsername);
        $oAdminJuser =& JFactory::getUser($strAdminUsername);
        if($oAdminJuser){
          //we have an admin user, make sure that it's not the pi
          if($oPiJuser->id != $oAdminJuser->id){
            $bAddAdmin = true;
          }
        }

        //PI and admin are different users. Add both to group
        if($bAddAdmin){
          $bGroupCreated = $oModel->createNewGroup($oProject, $oReasearcherKeywordArray, $oPiJuser, $oAdminJuser);
        }else{
          //Just add the PI to the group
          $bGroupCreated = $oModel->createNewGroup($oProject, $oReasearcherKeywordArray, $oPiJuser);
        }

        if($bGroupCreated){
          sleep(3);
        }
      }catch(Exception $oCreateGroupException){
        array_push($strErrorArray, "Create Project - Unable to create project group");
      }
    }

    if(empty($strErrorArray)){
      if($bNewDirs || $bUpload || $bGroupCreated){
        FileHelper::fixPermissions($strProjectPath);
      }
    }

    if(empty($strErrorArray)){
      $this->setRedirect("/warehouse/projecteditor/project/".$iProjectId."/experiments");
    }else{
      $strView = "project";
      $_REQUEST["ERRORS"] = $strErrorArray;
      JRequest::setVar( 'view', $strView );
      parent::display();
    }

  }

  /**
   * a) Add a member to a project and experiments or
   * b) Edit information about an existing person.
   *
   * For clean update, first, remove all roles and authorizations.
   * Then, insert new roles and authorizations.
   *
   * Make sure that at least 1 member has full access.
   */
  public function saveMember0(){
    $_SESSION["MEMBER_ERRORS"] = null;
    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelEditMember */
    $oModel =& $this->getModel('EditMember');

    $iProjectId = JRequest::getInt("projectId",0);
    $iPersonId = JRequest::getInt("personId",0);
    $iRoleId = JRequest::getInt("role",0);

    $oCurrentRoleArray = array();
    if(isset($_SESSION["USER_ROLES"])){
      $oCurrentRoleArray = unserialize($_SESSION["USER_ROLES"]);
    }
    //echo 'current roles: <br>';
    //var_dump($oCurrentRoleArray);
    //echo '<br>';

    /* @var $oProject Project */
    $oProject = $oModel->getProjectById($iProjectId);
    if(!$oProject){
      array_push( $strErrorArray, "Project not provided." );
    }

    /* @var $oEditPerson Person */
    $oEditPerson = $oModel->getPersonById($iPersonId);
    if(!$oEditPerson){
      array_push( $strErrorArray, "Person not provided." );
    }

    $bNewMember = false;
    $oExistingProjectRoleArray = $oEditPerson->getRolesForEntity($oProject);
    //echo 'existing roles: <br>';
    //var_dump($oExistingProjectRoleArray);
    //echo '<br>';
    if(empty($oExistingProjectRoleArray)){
      $bNewMember = true;
    }

    if(empty($strErrorArray)){
      //clean out any previous roles
      $oEditPerson->removeFromEntity($oProject);

      /* @var $oDefaultRole Role */
      $oDefaultRole = null;
      $oRoleArray = array();
      if($iRoleId === 0){
        $oDefaultRole = RolePeer::getDefaultRoleByEntityTypeId($iProjectId);
        array_push( $oRoleArray, $oDefaultRole );
      }else{
        $oDefaultRole = RolePeer::retrieveByPK($iRoleId);
        array_push( $oRoleArray, $oDefaultRole );
      }

      $oInsertRoleArray = array_merge($oCurrentRoleArray, $oRoleArray);
      foreach($oInsertRoleArray as $oThisRole){
        /* @var $oThisRole Role */
        if($oThisRole){
          $oEditPerson->addRoleForEntity($oThisRole, $oProject);
        }
      }

      // Make sure that at least one member has all permissions
      $forceGrantall = $oModel->shouldDisableRevocation($oEditPerson, $oProject);

      // Explicitly set permissions, if they're overridden from the Role-based defaults
      // make sure they have at least 'view' access
      $oPermissions = new Permissions(Permissions::PERMISSION_VIEW);

      if ( JRequest::getVar("canEdit") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_EDIT );
      }
      if ( JRequest::getVar("canDelete") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_DELETE );
      }
      if ( JRequest::getVar("canCreate") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_CREATE );
      }
      if ( JRequest::getVar("canGrant") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_GRANT );
      }

      /* @var $oAuthorization Authorization */
      $oAuthorization = new Authorization($oEditPerson->getId(), $oProject->getId(), 1, $oPermissions);
      $oAuthorization->save();

      // allow the project member has same access to all experiments
      if ( JRequest::getVar("copyToExp") ) {
        $oExperimentArray = $oProject->getExperiments();

        /* @var $oExperiment Experiment */
        foreach ($oExperimentArray as $oExperiment) {
          $oEditPerson->removeFromEntity($oExperiment);
          if(
            ! AuthorizationPeer::insertProjectAuthsForAllExperiments($oProject->getId(), $oExperiment->getId(),$oEditPerson->getId()) ||
            ! PersonEntityRolePeer::insertProjectPERforAllExperiments($oProject->getId(), $oExperiment->getId(),$oEditPerson->getId())){
            array_push($strErrorArray, $oExperiment->getName()." - Authorization and Role errors. ");
          }
        }
      }

      if($bNewMember && empty($strErrorArray)){
        /* $oModel->inviteWarehouseMemberToHubGroup($oProject, $oEditPerson); */
        $oModel->addWarehouseMemberToHubGroup($oProject, $oEditPerson);
      }//end if bNewMember

    }//end if empty

    $_SESSION["MEMBER_ERRORS"] = $strErrorArray;
    $strUrl = "/warehouse/projecteditor/project/".$oProject->getId()."/members";
    $this->setRedirect($strUrl);
  }

  public function saveMember(){
    $_SESSION["MEMBER_ERRORS"] = null;
    unset ($_SESSION["MEMBER_ERRORS"]);

    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelEditMember */
    $oModel =& $this->getModel('EditMember');

    $iProjectId = JRequest::getInt("projectId",0);
    $iPersonId = JRequest::getInt("personId",0);
    $iRoleId = JRequest::getInt("role",0);

    $oCurrentRoleArray = array();
    if(isset($_SESSION["USER_ROLES"])){
      $oCurrentRoleArray = unserialize($_SESSION["USER_ROLES"]);
    }
    //echo 'current roles: <br>';
    //var_dump($oCurrentRoleArray);
    //echo '<br>';

    /* @var $oProject Project */
    $oProject = $oModel->getProjectById($iProjectId);
    if(!$oProject){
      array_push( $strErrorArray, "Project not provided." );
    }

    /* @var $oEditPerson Person */
    $oEditPerson = $oModel->getPersonById($iPersonId);
    if(!$oEditPerson){
      array_push( $strErrorArray, "Person not provided." );
    }

    $bNewMember = false;
    $oExistingProjectRoleArray = $oEditPerson->getRolesForEntity($oProject);
    //echo 'existing roles: <br>';
    //var_dump($oExistingProjectRoleArray);
    //echo '<br>';
    if(empty($oExistingProjectRoleArray)){
      $bNewMember = true;
    }

    if(!$iRoleId){
      if(empty($oCurrentRoleArray)){
        array_push( $strErrorArray, "Please select a role for ".$oEditPerson->getFirstName()." ".$oEditPerson->getLastName() );
      }
    }

    if(empty($strErrorArray)){
      //clean out any previous roles
      $oEditPerson->removeFromEntity($oProject);

      /* @var $oDefaultRole Role */
      $oDefaultRole = null;
      $oRoleArray = array();
      if($iRoleId === 0){
        $oDefaultRole = RolePeer::getDefaultRoleByEntityTypeId($iProjectId);
        array_push( $oRoleArray, $oDefaultRole );
      }else{
        $oDefaultRole = RolePeer::retrieveByPK($iRoleId);
        array_push( $oRoleArray, $oDefaultRole );
      }

      $oInsertRoleArray = array_merge($oCurrentRoleArray, $oRoleArray);
      foreach($oInsertRoleArray as $oThisRole){
        /* @var $oThisRole Role */
        if($oThisRole){
          $oEditPerson->addRoleForEntity($oThisRole, $oProject);
        }
      }

      // Make sure that at least one member has all permissions
      $forceGrantall = $oModel->shouldDisableRevocation($oEditPerson, $oProject);

      // Explicitly set permissions, if they're overridden from the Role-based defaults
      // make sure they have at least 'view' access
      $oPermissions = new Permissions(Permissions::PERMISSION_VIEW);

      if ( JRequest::getVar("canEdit") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_EDIT );
      }
      if ( JRequest::getVar("canDelete") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_DELETE );
      }
      if ( JRequest::getVar("canCreate") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_CREATE );
      }
      if ( JRequest::getVar("canGrant") || $forceGrantall) {
        $oPermissions->setPermission(Permissions::PERMISSION_GRANT );
      }

      /* @var $oAuthorization Authorization */
      $oAuthorization = new Authorization($oEditPerson->getId(), $oProject->getId(), 1, $oPermissions);
      $oAuthorization->save();

      /*
       * Clean out experiments.  Only add users to experiments that are
       * checked in the form.
       */
      $oExperimentArray = $oProject->getExperiments();
      /* @var $oExperiment Experiment */
      foreach ($oExperimentArray as $oExperiment) {
        $oEditPerson->removeFromEntity($oExperiment);
      }

      // allow the project member has same access to all experiments
      $iExperimentIdArray = $_REQUEST['experiment'];
      while (list ($key,$iThisExperimentId) = @each ($iExperimentIdArray)) {
        /* @var $oExperiment Experiment */
        $oExperiment = $oModel->getExperimentById($iThisExperimentId);
          $bAuthorizations = AuthorizationPeer::insertProjectAuthsForAllExperiments($oProject->getId(), $oExperiment->getId(),$oEditPerson->getId());
          $bPersonEntityRoles = PersonEntityRolePeer::insertProjectPERforAllExperiments($oProject->getId(), $oExperiment->getId(),$oEditPerson->getId());
          if(!$bAuthorizations || !$bPersonEntityRoles){
            array_push($strErrorArray, $oExperiment->getName()." - Authorization and Role errors. ");
          }
        }

      if($bNewMember && empty($strErrorArray)){
        $oModel->addWarehouseMemberToHubGroup($oProject, $oEditPerson);
      }//end if bNewMember

    }//end if empty

    $_SESSION["MEMBER_ERRORS"] = $strErrorArray;

    $strErrors = StringHelper::EMPTY_STRING;
    if(!empty($strErrorArray)){
      $strErrors = "?errors=1";
    }

    $strUrl = "/warehouse/projecteditor/project/".$oProject->getId()."/members".$strErrors;
    $this->setRedirect($strUrl);
  }

  /**
   *
   * @param Project $p_oProject
   * @param Person $p_oEditPerson
   */
  public function removeMember(){
    $strErrorArray = array();

    //Incoming
    $iPersonId = JRequest::getInt('personId', 0);
    if(!$iPersonId){
      echo "<p class='error'>Member not selected.</p>";
      return;
    }

    $iProjectId = JRequest::getInt('projectId', 0);
    if(!$iProjectId){
      echo "<p class='error'>Project not selected.</p>";
      return;
    }

    /* @var $oModel ProjectEditorModelEditMember */
    $oModel =& $this->getModel('EditMember');

    /* @var $p_oEditPerson Person */
    $p_oEditPerson = $oModel->getPersonById($iPersonId);

    /* @var $p_oProject Project */
    $p_oProject = $oModel->getProjectById($iProjectId);

    $oThisUser = $oModel->getCurrentUser();

    /* @var $auth Authorizer */
    $auth = Authorizer::getInstance();
    $auth->setUser($oThisUser->username);
    $can_grant = $auth->canGrant($p_oProject);

    if(!$can_grant) {
      array_push($strErrorArray, "Error: You do not have permission to revoke the membership of members on this project.");
    }

    // remove person from repository (oracle)
    if(!$oModel->shouldDisableRevocation($p_oEditPerson, $p_oProject)) {
      try {

        // First, remove the membership from all experiments in the project
        $oExperiments = $p_oProject->getExperiments();
        foreach ($oExperiments as $e) {
          $p_oEditPerson->removeFromEntity($e);
        }

        // Then remove the membership from this project
        $p_oEditPerson->removeFromEntity($p_oProject);
      }catch (Exception $e) {
        array_push($strErrorArray, "Error: Cannot revoke the membership of this user on the project. Unknown error.");
      }
      //exit($editPersonId);
    } else {
      array_push($strErrorArray, "Error: Cannot revoke the only full-permissions member on this project.");
    }

    // remove perosn from hub group (mysql)
    ximport('xgroup');
    ximport('xuserhelper');

    //get group cn
    $strGroupCn = str_replace("-",  "_",  $p_oProject->getName());
    $strGroupCn = strtolower(trim($strGroupCn));

    // Load the group
    $group = new XGroup();
    $group->select( $strGroupCn );

    //get invitee hub id
    $oMemberJuser =& JFactory::getUser($p_oEditPerson->getUserName());
    if(!$oMemberJuser){
      array_push($strErrorArray, "Error: Selected user is not a member of the group.");
    }
    $oGroupMemberArray = array($oMemberJuser->get('id'));

    // Remove users from members list
    $group->remove('members', $oGroupMemberArray);
    $group->save();

    $_SESSION["MEMBER_ERRORS"] = $strErrorArray;
    $strUrl = "/warehouse/projecteditor/project/".$p_oProject->getId()."/members";
    $this->setRedirect($strUrl);
  }

  public function uploadForm(){
    JRequest::setVar("view", "uploadform");
    JRequest::setVar("format", "ajax");
    parent::display();
  }

  public function uploadFile(){
    $oModel =& $this->getModel('UploadForm');

    $strUrl = "";

    //Incoming
    $iRequestType = JRequest::getInt('requestType',-999);
    if($iRequestType==-999){
      echo ComponentHtml::showError("Unable to process request.");
      return;
    }

    $strPath = JRequest::getVar('path');
    if(!$strPath){
      echo ComponentHtml::showError("Destination path not found.");
      return;
    }

    $strTitle = JRequest::getVar('title');
    if(!$strTitle){
      echo ComponentHtml::showError("Title is required.");
      return;
    }

    $_REQUEST[Files::CURRENT_DIRECTORY] = $strPath;
    $iProjectId = JRequest::getInt('projId', 0);
    $iExperimentId = JRequest::getInt('experimentId',0);

    //invoke the upload plugin
    JPluginHelper::importPlugin( 'project', 'upload' );
    $oDispatcher =& JDispatcher::getInstance();
    $strParamArray = array(0,0);

    switch ($iRequestType){
      case Files::DRAWING:
        $oResultsArray = $oDispatcher->trigger('onDrawingUpload',$strParamArray);
        $oResults = $oResultsArray[0];
        if(is_numeric($oResults)){
          //got an error code
          $strMessage = UploadHelper::getErrorMessage($oResults);
          echo ComponentHtml::showError($strMessage);
        }else{
          $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/drawings?path=$strFilePath";
        }
	break;
      case Files::DATA:
        $iNumFiles = JRequest::getInt('files_num', 1);
        /*
         * Check to see if we are dealing with a video upload.
         * If yes, determine if the upload is a set of frames or an
         * actual movie.
         */
        if(preg_match(ProjectEditor::VIDEO_FRAMES_PATTERN, $strPath)){
          $oEntityType = EntityTypePeer::findByTableName("Video-Frames");
          $bValid = $oModel->validateVideoFrames($oEntityType, $iNumFiles);
          if($bValid){
            /*
             * Set the usage type for the upload plugin.
             */
            JRequest::setVar('usageType', $oEntityType->getId());
            JRequest::setVar('path', $strPath);  //may not be necessary
          }else{
            echo ComponentHtml::showError("Please upload an archive directory of zip, tar, or gz format.");
            return;
          }
        }elseif(preg_match(ProjectEditor::VIDEO_MOVIES_PATTERN, $strPath)){
          $oEntityType = EntityTypePeer::findByTableName("Video-Movies");
          $bValid = $oModel->validateVideoMovies($oEntityType, $iNumFiles);
          if($bValid){
            /*
             * Set the usage type for the upload plugin.
             */
            JRequest::setVar('usageType', $oEntityType->getId());
            JRequest::setVar('path', $strPath);  //may not be necessary
          }else{
            echo ComponentHtml::showError("Please provide a file with a valid mime-type of 'video'.");
            return;
          }
        }

        $oResultsArray = $oDispatcher->trigger('onDataUpload',$strParamArray);
        $oResults = $oResultsArray[0];
        if(is_numeric($oResults)){
          //got an error code
          $strMessage = UploadHelper::getErrorMessage($oResults);
          echo ComponentHtml::showError($strMessage);
        }else{
          /* @var $oDataFile DataFile */
          $oDataFile = unserialize($oResultsArray[0]);
          if($oDataFile){
            $strFileName = $oDataFile->getName();
            $strFilePath = $oDataFile->getPath();
            $strSource = $strFilePath."/".$strFileName;

            //get the file's extension
            $uploadedFileNameParts = explode('.', $strFileName);
            $uploadedFileExtension = array_pop($uploadedFileNameParts);

            //validate extension
            $extOk = false;
            $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
            foreach($validFileExts as $key => $value){
              if( preg_match("/$value/i", $uploadedFileExtension ) ){
                $extOk = true;
              }
            }

            //check mime type
            if($extOk){
              $okMIMETypes = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
              $validFileTypes = explode(",", $okMIMETypes);

              //if the source file has a width, height, and ok MIME, scale
              $imageinfo = getimagesize($strSource);
              if( is_int($imageinfo[0]) && is_int($imageinfo[1]) &&  in_array($imageinfo['mime'], $validFileTypes) ){
                $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
                $oResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);
              }
            }
          }

          $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/data?path=$strFilePath";
        }

	break;
      case Files::IMAGE:
        $oResultsArray = $oDispatcher->trigger('onPhotoUpload',$strParamArray);
        $oResults = $oResultsArray[0];
        if(is_numeric($oResults)){
          //got an error code
          $strMessage = UploadHelper::getErrorMessage($oResults);
          echo ComponentHtml::showError($strMessage);
        }else{
          if($iExperimentId){
            $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/photos?path=$strFilePath";
          }else{
            $strUrl = "/warehouse/projecteditor/project/$iProjectId/projectphotos?path=$strFilePath";
          }
        }
	break;
      case Files::DOCUMENT:
        $oResultsArray = $oDispatcher->trigger('onDataUpload',$strParamArray);
        if($iExperimentId){
          $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/documentation";
        }else{
          $strUrl = "/warehouse/projecteditor/project/$iProjectId/documentation";
        }
        break;
      case Files::ANALYSIS:
        $oResultsArray = $oDispatcher->trigger('onDataUpload',$strParamArray);
        if($iExperimentId){
          $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/analysis";
        }else{
          $strUrl = "/warehouse/projecteditor/project/$iProjectId/analysis";
        }
        break;
      case Files::VIDEO:
        //addition on 20100820
        $iNumFiles = JRequest::getInt('files_num', 1);
        $iUsageTypeId = JRequest::getVar('usageType');
        $oEntityType = EntityTypePeer::retrieveByPK($iUsageTypeId);
        $bValid = $oModel->validateVideoFrames($oEntityType, $iNumFiles);
        if($bValid){
          $oResultsArray = $oDispatcher->trigger('onDataUpload',$strParamArray);
          if($iExperimentId){
            $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/videos";
          }else{
            $strUrl = "/warehouse/projecteditor/project/$iProjectId/projectvideos";
          }
        }else{
          echo ComponentHtml::showError("Please zip or tar the folder containing the frames.");
        }

        if($oEntityType && !empty($oResultsArray)){
          $strEntityName = $oEntityType->getDatabaseTableName();
          if($strEntityName=="Video-Frames"){
            /* @var $oMovieArchiveDataFile DataFile */
            $oMovieArchiveDataFile = unserialize($oResultsArray[0]);

            $oDataFileArchiveList = new DataFileArchiveList();
            $oDataFileArchiveList->setId($oMovieArchiveDataFile->getId());
            $oDataFileArchiveList->setProcessedDate(date("m/d/Y"));
            $oDataFileArchiveList->save();
          }
        }
        break;
    }

    $this->setRedirect($strUrl);
  }

  public function saveAbout(){
    unset($_SESSION["ERRORS"]);

    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelExperiment */
    $oModel =& $this->getModel('Experiment');

    //get the current project
    $iProjectId = JRequest::getVar('projid');
    $oProject = $oModel->getProjectById($iProjectId);

    $oExperimentDomain = null;
    $iExperimentDomainId = 0;
    try{
      $iExperimentDomainId = $oModel->validateText("Domain", JRequest::getVar("experimentDomainId"));
      $oExperimentDomain = $oModel->getExperimentDomainById($iExperimentDomainId);
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Create Experiment"));
    }

    $iExperimentId = JRequest::getInt('experimentId',0);

    /*
     * Validate required fields
     */
    $strTitle = null;
    try{
      $strTitle = $oModel->validateText("Title", JRequest::getVar("title"));
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Create Experiment"));
    }

    $strStartDate = null;
    try{
      $strStartDate = $oModel->validateStartDate(JRequest::getVar("startdate"));
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Create Experiment"));
    }

    $strEndDate = $oModel->validateEndDate(JRequest::getVar("enddate"));
    $strDescription = JRequest::getVar("description", StringHelper::EMPTY_STRING);

    //validate facilities
    $oNewFacilityArray = null;
    try{
      $oNewFacilityArray = $oModel->validateFacilitiesByName($_REQUEST['facility']);
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Create Experiment"));
    }

    $strStatus = "unpublished";
    $strCurationStatus = "Uncurated";
    $iDeleted = 0;
    $strAccess = "MEMBERS";

    $oHubUser = $oModel->getCurrentUser();

    /* @var $oCreator Person */
    $oCreator = $oModel->getOracleUserByUsername($oHubUser->username);
    $creatorId = $oCreator->getId();

    /* @var $oExperiment Experiment */
    if(empty($strErrorArray)){
      $oConnection = Propel::getConnection();
      try{
        $oConnection->begin();

        if($iExperimentId > 0){
          $oExperiment = $oModel->getExperimentById($iExperimentId);
          $oExperiment->setStartDate($strStartDate);
          $oExperiment->setEndDate($strEndDate);
          $oExperiment->setExperimentDomain($oExperimentDomain);
          $oExperiment->setTitle($strTitle);
          $oExperiment->setDescription($strDescription);
        }else{
          $oExperiment = new StructuredExperiment( $oProject, $strTitle,
                            StringHelper::EMPTY_STRING, $strDescription,
                            $strStartDate, $strEndDate, $strStatus, $strAccess,
                            $oExperimentDomain, $strCurationStatus, $iDeleted);
          $oExperiment->setName(ExperimentPeer::getNextAvailableName($oExperiment));
          $oExperiment->setCreatorId($creatorId);
        }
        $oExperiment->save();

        /*
         * save specimen for project.  ellen thinks that this should be for
         * experiments.  we will have to change data modal.
         *
         * both title and description are required by oracle.  display only
         * shows the name.
         */
        $strSpecimenType = JRequest::getVar("specimenType");
        if(StringHelper::hasText($strSpecimenType)){
          $oModel->deleteSpecimenByProject($iProjectId, $oConnection);

          $oSpecimen = new Specimen();
          $oSpecimen->setProject($oProject);
          $oSpecimen->setName($strSpecimenType);
          $oSpecimen->setTitle($strSpecimenType);
          $oSpecimen->save();
        }

        //add validated facility objects
        $oExperiment = $oModel->addFacilities($oExperiment, $oNewFacilityArray, $oConnection);

        // add equipment
        $oExperimentEquipmentArray = $oModel->addEquipment($oExperiment, $_REQUEST['equipment'], $oConnection);

        //save values
        $_SESSION[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);

        $strTags = JRequest::getVar("tags", "");
        if(StringHelper::hasText($strTags)){
          $strTagsArray = explode(",", $strTags);
          $oReasearcherKeywordArray = $oModel->setTags($strTagsArray, $oHubUser->username);
          $oModel->createResearcherKeywords($oExperiment->getId(), 3, $oReasearcherKeywordArray, $oConnection);
        }

        $expdir = $oExperiment->getPathname();
        $bMkDir = false;
        $bUpload = false;

        /*
         * Check if we're creating a new experiment.  If yes,
         * create roles, authorizations, and directories.
         */
        if(!$iExperimentId){
          $entityId = $oExperiment->getId();
          $entityTypeId = DomainEntityType::ENTITY_TYPE_EXPERIMENT;
          /*
          $projRoles = $oCreator->getRolesForEntity($oExperiment->getProject());

          // give them a default role to match their project role
          foreach($projRoles as $role) {
            $oCreator->addRoleForEntity($role, $oExperiment);
          }

          // Explicitly set permissions
          $auth = new Authorization($creatorId, $entityId, $entityTypeId, new Permissions(Permissions::PERMISSION_ALL));
          $auth->save();

          //add all existing team members
          $oAuthorizer = Authorizer::getInstance();
          $oPersonEntityRoleArray = $oModel->getPersonEntityRole($oProject);
          foreach($oPersonEntityRoleArray as $oPersonEntityRole){
            // @var $oPersonEntityRole PersonEntityRole
            $oMemberPerson = $oPersonEntityRole->getPerson();
            if(!$oAuthorizer->personCanDo($oExperiment, "View", $oMemberPerson->getId())){
              AuthorizationPeer::insertProjectAuthsForAllExperiments($oProject->getId(), $oExperiment->getId(), $oMemberPerson->getId());
            }
          }
          */

//          $bMkDir = $this->makedir(array(
//                                  $expdir,
//                                  "$expdir/Analysis",
//                                  "$expdir/Documentation",
//                                  "$expdir/Photos",
//                                  "$expdir/Public",
//                                  "$expdir/Setup",
//                                  "$expdir/Videos",
//                                  "$expdir/Videos/Movies",
//                                  "$expdir/Videos/Photos"));

          $bMkDir = $this->makedir(array(
                                  $expdir,
                                  "$expdir/Documentation/Drawings",         //20101102
                                  "$expdir/Documentation/Photos",           //20101102
                                  "$expdir/Documentation/Videos/Movies",    //20101102
                                  "$expdir/Documentation/Videos/Frames",    //20101102
                                  "$expdir/Public",
                                  "$expdir/Analysis",
                                  "$expdir/Setup",
                                  "$expdir/N3DV",
                                  "$expdir/Models"
                                  ));

          $requiredN3DVFiles = array('container1.iv', 'default_behaviors.bhv', 'moment.iv', 'disp.iv');
          $n3dvTarget = FileCommandAPI::create("$expdir/N3DV");

//          foreach($requiredN3DVFiles as $n3dv_file) {
//            $origin = FileCommandAPI::create('/opt/central/htdocs/downloads/N3DV/' . $n3dv_file);
//            $origin->copy($n3dvTarget);
//          }
          CoordinateSpace::createDefaultCoordSpace($oExperiment);
        }//end role, authorization, folders

        /*
         * Check to see if we are uploading a photo.
         */
        $strImageFileName = (is_array($_FILES[ProjectEditor::UPLOAD_FIELD_NAME]['tmp_name'])) ? $_FILES[ProjectEditor::UPLOAD_FIELD_NAME]['tmp_name'][0] : $_FILES[ProjectEditor::UPLOAD_FIELD_NAME]['tmp_name'];
        if(StringHelper::hasText($strImageFileName)){
          $strDestinationPath = $expdir.ProjectEditor::PHOTO_DESTINATION_SUFFIX;
          //echo "path=$strDestinationPath<br>";
          $oFileCommand = FileCommandAPI::create($strDestinationPath);
          $oFileCommand->mkdir(TRUE);

          $strCaption = JRequest::getVar("desc", "");
          if(!StringHelper::hasText($strCaption)){
            $strCaption = StringHelper::neat_trim($oExperiment->getDescription(), 25);
          }

          JRequest::setVar('title', $oExperiment->getTitle());
          JRequest::setVar('desc', $strCaption);
          JRequest::setVar('path', $strDestinationPath);
          JRequest::setVar('fixPermissions', 0);

          //execute plugin
          JPluginHelper::importPlugin( 'project', 'upload' );
          $oDispatcher =& JDispatcher::getInstance();
          $strParamArray = array(0,0);

          $strResultsArray = $oDispatcher->trigger('onExperimentPhotoUpload',$strParamArray);
          //print_r($strResultsArray);
          $oResults = $strResultsArray[0];
          if(is_numeric($oResults)){
            //got an error code
            $strMessage = UploadHelper::getErrorMessage($oResults);
            array_push($strErrorArray, $strMessage);
          }else{
            $bUpload = true;
          }
        }

        if($bMkDir || $bUpload){
          FileHelper::fixPermissions($expdir);
        }

        $oConnection->commit();
      }catch(Exception $e){
        $oConnection->rollback();
        array_push($strErrorArray, "Unable to save Experiments-About.");
      }
    }

    if(!empty($strErrorArray)){
      $strFacilities = "";
      while (list ($key,$strFacilityName) = @each ($_REQUEST['facility'])) {
        if(StringHelper::hasText($strFacilityName)){
          $strFacilities .= "&facility[]=".$strFacilityName;
        }
      }

      $strUrl = "/warehouse/projecteditor/project/".$oProject->getId().
                        "/experiment/".$iExperimentId."/about?title=".$strTitle."&description=".$strDescription.
                        "&startdate=".$strStartDate."&enddate=".$strEndDate.
                        $strFacilities;

      $_SESSION["ERRORS"] = $strErrorArray;
    }else{
      $strUrl = "/warehouse/projecteditor/project/".$oProject->getId().
                        "/experiment/".$oExperiment->getId()."/materials";
      $_SESSION[ExperimentPeer::TABLE_NAME] = serialize($oExperiment);
    }

    $this->setRedirect($strUrl);
  }

  public function saveMaterial(){
    unset($_SESSION["ERRORS"]);
    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelMaterials */
    $oModel =& $this->getModel('Materials');

    $iProjectId = JRequest::getVar('projid');
    $oProject = $oModel->getProjectById($iProjectId);

    //get the current experiment
    $iExperimentId = JRequest::getVar('experimentId');
    $oExperiment = $oModel->getExperimentById($iExperimentId);

    //validate incoming
    $iMaterialId = JRequest::getInt('materialId', 0);

    /* @var $oMaterialType MaterialType */
    $oMaterialType = null;
    try{
      $oMaterialType = $oModel->validateMaterialType("Material Type", JRequest::getVar('materialType'));
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Edit Experiment"));
    }

    $strMaterialName = StringHelper::EMPTY_STRING;
    try{
      $strMaterialName = $oModel->validateMaterial("Material Name", JRequest::getVar('material'));
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Edit Experiment"));
    }

    $strMaterialDesc = StringHelper::EMPTY_STRING;
    try{
      $strMaterialDesc = $oModel->validateMaterialDescription("Material Description", JRequest::getVar('materialDesc'));
    }catch(ValidationException $oException){
      array_push($strErrorArray, $oException->getEntityMessage("Edit Experiment"));
    }

    // no errors, so manipulate the material objects
    if(empty($strErrorArray)){
      $oMaterial = null;
      if(!$iMaterialId){
        $oMaterial = new Material($oExperiment, $oMaterialType, $strMaterialName, $strMaterialDesc);
      }else{
        $oMaterial = $oModel->getMaterialById($iMaterialId);
        $oMaterial->setName($strMaterialName);
        $oMaterial->setDescription($strMaterialDesc);
        $oMaterial->setMaterialType($oMaterialType);
      }

      // TODO: Attach a prototype
      $oPrototype = null;
      $protofield = 'prototype'.$oMaterialType->getId();

      if( JRequest::getVar($protofield) ) {
        $oPrototype = MaterialPeer::find(JRequest::getVar($protofield));
      }
      $oMaterial->setPrototype($oPrototype);
      $oMaterial->save();

      // Remove old material properties.
      $oOldMaterialProperties = $oMaterial->getMaterialProperties();
      foreach( $oOldMaterialProperties as $oOldMaterialProperty ) {
        $oOldMaterialProperty->delete();
      }

      $newprops = array();

      $TypeProperties = $oMaterialType->getMaterialTypeProperties();

      foreach( $TypeProperties as $TypeProperty ) {
        $propertyValue = null;
        $unit = null;
        $fieldname = 'property' . $TypeProperty->getId();
        if( JRequest::getVar($fieldname) ) {
          $propertyValue = JRequest::getVar($fieldname);
        }

        // Handle units.
        $category = $TypeProperty->getUnitCategory();
        if( $category ) {
          $unitid = JRequest::getVar('units' . $TypeProperty->getId());
          if( $unitid ) {
            $unit = MeasurementUnitPeer::find($unitid);
          }
          if( !$unit ) {
            //get default unit from experiment
            if(!($unit = $oExperiment->getUnit($category))){
              $units = $category->getUnits();
              $unit = $unit[0];
            }
          }
        }
        // If they used a prototype for this material, use the prototype's name for 'type'
        if( preg_match('/^Type of/', $TypeProperty->getName()) && $oMaterial->getPrototype() ) {
          $propertyValue = $oMaterial->getPrototype()->getName();
        }
        //$newprops->add( new MaterialProperty($TypeProperty, $oMaterial, $propertyValue, $unit) );
        $newprops[] = new MaterialProperty($TypeProperty, $oMaterial, $propertyValue, $unit);
      }//end foreach

      $oMaterial->setMaterialProperties($newprops);
      $oMaterial->save();

      // Handle File uploads.
      if( isset($_FILES) && count($_FILES) ) {
        //$fileCollection = $this->material->getFiles();
        //$dest = $exp->getPathname() . "/" . "Material" . $this->material->getId();
        $dest = $oMaterial->getPathname();

        foreach( $_FILES as $file ) {
          $newdatafile = DataFile::newDataFileByUpload($file, $dest);
          if( $newdatafile ) {
            $matfile = new MaterialFile($oMaterial, $newdatafile );
            $matfile->save();

            //@todo: Check, if we don't need this any more !!!
            //$fileCollection[] = $matfile;
          }
        }

        FileHelper::fixPermissions($dest);
      }//end if files

    }//end if(empty)


    $_SESSION["ERRORS"] = $strErrorArray;
    $strUrl = "/warehouse/projecteditor/project/".$oProject->getId()."/experiment/".$oExperiment->getId()."/materials";
    $this->setRedirect($strUrl);
  }

  private function makedir($dirs) {
    foreach ($dirs as $dir) {
      $dirObj = FileCommandAPI::create($dir);
      $dirObj->mkdir();
    }
  }

  public function saveSensor(){
    unset($_SESSION["ERRORS"]);
    $strErrorArray = array();

    /* @var $oModel ProjectEditorModelSensors */
    $oModel =& $this->getModel('Sensors');

    /* @var $oHubUser JUser */
    $oHubUser = $oModel->getCurrentUser();

    /* @var $auth Authorizer */
    $auth = $oModel->getAuthorizer();
    $auth->setUser($oHubUser->username);

    $iProjectId = JRequest::getInt("projectId", 0);
    if(!$iProjectId){
      echo "Project not selected";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not selected";
      return;
    }

    /* @var $oExperiment Experiment */
    $oExperiment = $oModel->getExperimentById($iExperimentId);

    $iLocationPlanId = JRequest::getInt("locationPlanId", 0);
    if(!$iLocationPlanId){
      echo "Sensor list not selected";
      return;
    }

    /* @var $oLocationPlan LocationPlan */
    $oLocationPlan = $oModel->findLocationPlanById($iLocationPlanId);
    $lpid = $oLocationPlan->getId();

    $xyzUnitId       = JRequest::getVar('xyzUnits');
    $xyzUnit = null;
    if( $xyzUnitId ) {
      $xyzUnit = MeasurementUnitPeer::find($xyzUnitId);
    }

    $degreeUnit = MeasurementUnitPeer::findByName('degree');

    // Location Info - these should each represent an array
    $locId           = JRequest::getVar('locId');
    $locType         = JRequest::getVar('Type');
    $locLabel        = JRequest::getVar('Label'); //
    $coordinateSpace = JRequest::getVar('coordinateSpace'); //
    $comments        = JRequest::getVar('comments');
    $locX            = JRequest::getVar('locX');
    $locY            = JRequest::getVar('locY');
    $locZ            = JRequest::getVar('locZ');
    $orientI         = JRequest::getVar('orientI');
    $orientJ         = JRequest::getVar('orientJ');
    $orientK         = JRequest::getVar('orientK');

    /* @var $oSensorType SensorType */
    $oSensorType = SensorTypePeer::findByName($locType);

    /* @var $oCoordinateSpace CoordinateSpace */
    $oCoordinateSpace = CoordinateSpacePeer::retrieveByPK($coordinateSpace);

    list($normalI, $normalJ, $normalK) = Location::normalize( array($orientI, $orientJ, $orientK) );

    if($normalI === "") $normalI = null;
    if($normalJ === "") $normalJ = null;
    if($normalK === "") $normalK = null;

    /* @var $oLocation Location */
    $oLocation = LocationPeer::retrieveByPK($locId);
    $oLocation->setLabel($locLabel);
    $oLocation->setCoordinateSpace($oCoordinateSpace);
    $oLocation->setSensorType($oSensorType);
    $oLocation->setLocationPlan($oLocationPlan);
    $oLocation->setX($locX);
    $oLocation->setY($locY);
    $oLocation->setZ($locZ);
    $oLocation->setI($normalI);
    $oLocation->setJ($normalJ);
    $oLocation->setK($normalK);

    if($oCoordinateSpace->getSystem()->getName() != "Geographic") {
      $oLocation->setMeasurementUnitRelatedByXUnit($xyzUnit);
      $oLocation->setMeasurementUnitRelatedByYUnit($xyzUnit);
    }else {
      $oLocation->setMeasurementUnitRelatedByXUnit($degreeUnit);
      $oLocation->setMeasurementUnitRelatedByYUnit($degreeUnit);
    }

    $oLocation->setMeasurementUnitRelatedByZUnit($xyzUnit);
    $oLocation->setComment($comments);
    $oLocation->save();

    $strUrl="/warehouse/projecteditor/sensorlist?locationPlanId=".$iLocationPlanId.
                                        "&projid=$iProjectId&experimentId=$iExperimentId";

    $this->setRedirect($strUrl);
  }

  public function removeFile(){
    //echo "done";
  }

  public function setExperimentAccess(){
    $iPersonId = JRequest::getInt("personId", 0);
    $iExperimentId = $_REQUEST["experimentId"];

    echo "<input type='hidden' name='experiments-Person-$iPersonId' value='$iExperimentId'/>";
  }

  public function makeDirectory(){
    /* @var $oModel ProjectEditorModelMkDir */
    $oModel =& $this->getModel('MkDir');

    //incoming
    $iProjectId = JRequest::getInt("projectId", 0);
    $iExperimentId = JRequest::getInt("experimentId", 0);
    $strPath = JRequest::getVar("path");
    $strNewDirectory = JRequest::getVar("newdir");

    //echo "p=$iProjectId, e=$iExperimentId, pa=$strPath, d=$strNewDirectory<br>";

    $oProject = $oModel->getProjectById($iProjectId);
    if($oProject){
      $_REQUEST[Files::PROJECT_NAME] = $oProject->getName();
    }

    $strAbsolutePathofNewDirectory = $strPath."/".$strNewDirectory;

    $bMkDir = false;
    if(!is_dir($strAbsolutePathofNewDirectory)){
      $oFileCommand = FileCommandAPI::create($strAbsolutePathofNewDirectory);
      $bMkDir = $oFileCommand->mkdir(TRUE);
    }//end if is_dir

    if($bMkDir){
      FileHelper::fixPermissions($strAbsolutePathofNewDirectory);
    }

    $strUrl = "/warehouse/projecteditor/project/$iProjectId/experiment/$iExperimentId/data?path=$strPath";
    $this->setRedirect($strUrl);

    //exit;
  }

  public function saveLocationPlan(){
    $strLocationPlanName = JRequest::getVar('lpName', '');
    $iUnitId = JRequest::getInt('unit', 0);
    $iExperimentId = JRequest::getInt('experimentId', 0);

    if(!$strLocationPlanName || $strLocationPlanName==""){
      echo "Please enter sensor list name.";
      return;
    }

//    if(!$iUnitId){
//      echo "Please select units.";
//      return;
//    }

    if(!$iExperimentId){
      echo "Please select an experiment.";
      return;
    }

    /* @var $oModel ProjectEditorModelCreateLocationPlan */
    $oModel =& $this->getModel('CreateLocationPlan');
    $oExperiment = $oModel->getExperimentById($iExperimentId);

    try{
      $oLocationPlan = new SensorLocationPlan($oExperiment, $strLocationPlanName);
      $oLocationPlan->save();
    }catch(Exception $e){
      echo "Unable to create or save Sensor List.";
      return;
    }

    $strUrl = "/warehouse/projecteditor/project/".$oExperiment->getProject()->getId()."/experiment/".$oExperiment->getId()."/sensors";
    $this->setRedirect($strUrl);
  }

  /**
   * Uploads Excel spreadsheets that represent sensors
   * for Sensor Location Plans.  Source Location Plans
   * were removed.
   *
   */
  public function saveSensorFile(){
    //Incoming
    $strPlanType = "Sensor";
    $iExperimentId = JRequest::getInt('experimentId', 0);
    $iLocationPlanId = JRequest::getInt('locationPlanId', 0);

    if(!$iExperimentId){
      echo ComponentHtml::showError("Experiment not provided.");
      return;
    }

    if(!$iLocationPlanId){
      echo ComponentHtml::showError("Sensor list not provided.");
      return;
    }

    /* @var $oModel ProjectEditorModelSensors */
    $oModel =& $this->getModel('Sensors');

    /* @var $oExperiment Experiment */
    $oExperiment = $oModel->getExperimentById($iExperimentId);

    /* @var $oLocationPlan LocationPlan */
    $oLocationPlan = $oModel->findLocationPlanById($iLocationPlanId);

    // Keep an array of all of the objects we're creating
    $objectsArray = array();

    if(!isset($_FILES["uploadFile"]['name']) || empty($_FILES["uploadFile"]['name'])) {
      return;
    }

    //$newFilePath = "/tmp/" . $_FILES["uploadFile"]["name"];
    //move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $newFilePath);

    $strFileName = $_FILES["uploadFile"]["name"];
    $strSensorDir = $oExperiment->getPathname()."/Documentation/Sensors";
    if(!is_dir($strSensorDir)){
      $oFileCommand = FileCommandAPI::create($strSensorDir);
      $bSensorDir = $oFileCommand->mkdir(TRUE);
      if($bSensorDir){
        FileHelper::fixPermissions($strSensorDir);
      }
    }
    $newFilePath = $strSensorDir ."/". $strFileName;
    move_uploaded_file($_FILES["uploadFile"]["tmp_name"], $newFilePath);

    $reader = new FileUploadReader($newFilePath);

    $cells = $reader->getData();

    if(!is_array($cells)) {
      echo ComponentHtml::showError("Unable to parse your uploaded data file. Make sure you are uploading a valid document.<br/>Remember, the accepted formats are Excel (95, 97, 2000, 2003) (*.xls), and comma-delimited text files (*.csv), and tab-delimited text files (*.txt), and XML Speadsheet (*.xml)");
      return;
    }

    //$columns = array(1=>"Label",	2=>$strPlanType . "Type",	3=>"Comment",	4=>"X",	5=>"Y",	6=>"Z",	7=>"I",	8=>"J",	9=>"K",	10=>"CoordinateSpace");
    $columns = array(1=>"Label", 2=>$strPlanType . "Type", 3=>"Comment", 4=>"Direction", 5=>"", 6=>"", 7=>"Orientation", 8=>"", 9=>"", 10=>"CoordinateSpace", 11=>"Serial Number", 12=>"DA Channel", 13=>"Units", 14=>"Calibration Constant", 15=>"", 16=>"Zero Offset", 17=>"Nonlinearity", 18=>"", 19=>"Excitation Voltage");

    $numRows = count($cells);
    $numCols = isset($cells[1]) ? count($cells[1]) : 0;
    //print_r($cells[1]);

    $msg = "Either, the file does not have correct number of columns or column labels are not correct. <br/>These column labels must be: " . implode(", ", $columns) . " <br/>and must be in the same of this order";

    //print_r($columns)."<br>";
    //echo $numCols." vs ".count($columns)."<br>";
    if( $numCols != count($columns)) {
      echo ComponentHtml::showError($msg);
      return;
    }

    for ($col = 1; $col <= count($columns); $col++) {
      if( $cells[1][$col] != $columns[$col]) {
        echo ComponentHtml::showError($msg);
        return;
      }
    }

    $degreeUnit = MeasurementUnitPeer::findByName('degree');

    $oLocationArray = LocationPeer::findByLocationPlan($oLocationPlan->getId());
    $locLabelMap = array();

    foreach($oLocationArray as $loc) {
      $locLabelMap[$loc->getLabel()] = $loc;
    }

    $defaultUnit = $oModel->findDefaultUnit($oLocationArray, $oExperiment);

    $sTypes = SensorTypePeer::findAll();
    $sTypeNames = array();
    foreach($sTypes as $st) {
      $sTypeNames[strtolower($st->getName())] = $st;
    }


    $coordinateSpaces = CoordinateSpacePeer::findByExperiment($oExperiment->getId());
    $coorNames = array();
    foreach($coordinateSpaces as $coor) {
      $coorNames[strtolower($coor->getName())] = $coor;
    }

    // Start from Line 2, as Line 1 is the header.
    // Start from Line 3, as Line 2 is a sub-header
    for ($row = 3; $row <= $numRows; $row++) {

      $locLabel = $cells[$row][1];

      $sTypeName = $cells[$row][2];

      if( ! isset($sTypeNames[strtolower($sTypeName)])) {
        echo ComponentHtml::showError("Error Line $row: " . $strPlanType . "Type cannot be found in database.");
        return;
      }

      $sType = $sTypeNames[strtolower($sTypeName)];

      $comment = $cells[$row][3];
      $locX = $cells[$row][4];
      $locY = $cells[$row][5];
      $locZ = $cells[$row][6];

      $orientI = $cells[$row][7];
      $orientJ = $cells[$row][8];
      $orientK = $cells[$row][9];

      list($normalI, $normalJ, $normalK) = Location::normalize( array($orientI, $orientJ, $orientK) );

      if($normalI === "") $normalI = null;
      if($normalJ === "") $normalJ = null;
      if($normalK === "") $normalK = null;

      if( !is_numeric($locX) || !is_numeric($locY) || !is_numeric($locZ)) {
        echo ComponentHtml::showError("Error Line $row: X, Y, Z is required and must be a number.");
        return;
      }

      $coorName = $cells[$row][10];

      if( ! isset($coorNames[strtolower($coorName)])) {
        echo ComponentHtml::showError("Error Line $row: CoordinateSpace: '$coorName' associated with this Experiment cannot be found in database.");
        return;
      }
      $coordinateSpace = $coorNames[strtolower($coorName)];

      if(isset($locLabelMap[$locLabel])) {
        $loc = $locLabelMap[$locLabel];
      }
      else {
        $loc = new SensorLocation();
      }

      $loc->setLocationPlan($oLocationPlan);
//      $this->planType == "Sensor" ? $loc->setSensorType($sType) : $loc->setSourceType($sType);
//      $this->planType == "Sensor" ? $loc->setSourceType(null) : $loc->setSensorType(null);
      $loc->setSensorType($sType);
      $loc->setLabel($locLabel);
      $loc->setX($locX);
      $loc->setY($locY);
      $loc->setZ($locZ);
      $loc->setI($normalI);
      $loc->setJ($normalJ);
      $loc->setK($normalK);
      $loc->setCoordinateSpace($coordinateSpace);
      $loc->setComment($comment);

      if($coordinateSpace->getSystem()->getName() != "Geographic") {
        $loc->setMeasurementUnitRelatedByXUnit($defaultUnit);
        $loc->setMeasurementUnitRelatedByYUnit($defaultUnit);
      }
      else {
        $loc->setMeasurementUnitRelatedByXUnit($degreeUnit);
        $loc->setMeasurementUnitRelatedByYUnit($degreeUnit);
      }
      $loc->setMeasurementUnitRelatedByZUnit($defaultUnit);

      if( ! $loc->validate()) {
        $fArray = $loc->getValidationFailures();

        $m = "<ul>";
        foreach($fArray as $v) {
          $m .= "<li>" . $v->getMessage() . "</li>";
        }
        $m .= "</ul>";
        echo ComponentHtml::showError("Error Line $row: " . $m);
        return;
      }

      if( is_null($locX)) {
        echo ComponentHtml::showError("Error Line $row: X can not be NULL");
        return;
      }

      $objectsArray[] = $loc;
    }

    foreach($objectsArray as $obj) {
      $obj->save();
    }

    //fixPermissions on uploaded file
    FileHelper::fixPermissionsOneFileOrDir($newFilePath);

    /* @var $oDataFile DataFile */
    $oDataFile = new DataFile();
    $oDataFile = $oDataFile->newDataFileByFilesystem($strFileName, $strSensorDir, false);
    $oDataFile->save();

    //assign data file to LocationPlan
    $oLocationPlan->setDataFile($oDataFile);
    $oLocationPlan->save();

    $strUrl = "/warehouse/projecteditor/sensorlist?locationPlanId=".$iLocationPlanId."&projid=".$oExperiment->getProject()->getId()."&experimentId=".$oExperiment->getId();
    $this->setRedirect($strUrl);
  }

  public function saveDataFile(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo "Project not selected.";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not selected.";
      return;
    }

    $iDataFileId = JRequest::getInt("dataFileId", 0);
    if(!$iDataFileId){
      echo "Data file not selected.";
      return;
    }

//    $iEntityTypeId = JRequest::getVar("usageType", 0);
//    $iUsageTypeId = ($iEntityTypeId > 0) ? $iEntityTypeId : null;

    $strTool = (StringHelper::hasText(JRequest::getVar("tool"))) ? JRequest::getVar("tool") : null;

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //edit the data file
    $strTitle = JRequest::getVar("title", "");
    $strDescription = JRequest::getVar("desc","");

    $oDataFile = DataFilePeer::retrieveByPK($iDataFileId);
    $oDataFile->setTitle($strTitle);
    $oDataFile->setDescription($strDescription);
    //$oDataFile->setUsageTypeId($iUsageTypeId);
    $oDataFile->setOpeningTool($strTool);
    try{
      $oDataFile->save();
    }catch(Exception $e){
      echo "Unable to save data file.";
      return;
    }

    $strPath = JRequest::getVar("path", "");
    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/data";

    if(StringHelper::hasText($strPath)){
      $strUrl .= "?path=".$strPath;
    }

    $this->setRedirect($strUrl);
  }

  public function saveAnalysis(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo ComponentHtml::showError("Project not selected.");
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);

    $iDataFileId = JRequest::getInt("dataFileId", 0);
    if(!$iDataFileId){
      echo ComponentHtml::showError("Data file not selected.");
      return;
    }

    $iRequestType = JRequest::getInt("requestType", 0);
    if(!$iRequestType){
      echo ComponentHtml::showError("Request type not provided.");
      return;
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //edit the data file
    $strTitle = JRequest::getVar("title", "");
    $strDescription = JRequest::getVar("desc","");

    $oDataFile = DataFilePeer::retrieveByPK($iDataFileId);
    $oDataFile->setTitle($strTitle);
    $oDataFile->setDescription($strDescription);

    try{
      $oDataFile->save();
    }catch(Exception $e){
      echo ComponentHtml::showError("Unable to save file.");
      return;
    }

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId."/analysis";
    if($iExperimentId){
      $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId."/analysis";
    }

    $this->setRedirect($strUrl);
  }

  public function saveDocument(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo ComponentHtml::showError("Project not selected.");
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);

    $iDataFileId = JRequest::getInt("dataFileId", 0);
    if(!$iDataFileId){
      echo ComponentHtml::showError("Data file not selected.");
      return;
    }

    $iRequestType = JRequest::getInt("requestType", 0);
    if(!$iRequestType){
      echo ComponentHtml::showError("Request type not provided.");
      return;
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //edit the data file
    $strTitle = JRequest::getVar("title", "");
    $strDescription = JRequest::getVar("desc","");

    $oDataFile = DataFilePeer::retrieveByPK($iDataFileId);
    $oDataFile->setTitle($strTitle);
    $oDataFile->setDescription($strDescription);

    try{
      $oDataFile->save();
    }catch(Exception $e){
      echo ComponentHtml::showError("Unable to save file.");
      return;
    }

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId."/documentation";
    if($iExperimentId){
      $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId."/documentation";
    }

    $this->setRedirect($strUrl);
  }

  public function saveDrawing(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo "Project not selected.";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not selected.";
      return;
    }

    /* @var $oModel ProjectEditorModelEditDrawing */
    $oModel =& $this->getModel('EditDrawing');

    /* @var $oExperiment Experiment */
    $oExperiment = $oModel->getExperimentById($iExperimentId);

    $iDataFileId = JRequest::getInt("dataFileId", 0);

    //use upload plugin
    if($iDataFileId===0){
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);
      $oResultsArray = $oDispatcher->trigger('onDrawingUpload',$strParamArray);
      $oResults = $oResultsArray[0];
      if(is_numeric($oResults)){
        //got an error code
        $strMessage = UploadHelper::getErrorMessage($oResults);
        echo ComponentHtml::showError($strMessage);
      }
    }else{
      $strTitle = JRequest::getVar("title", "");
      $strDescription = JRequest::getVar("desc","");
      $iEntityTypeId = JRequest::getVar("usageType","");

      $oDataFile = DataFilePeer::retrieveByPK($iDataFileId);
      $oDataFile->setTitle($strTitle);
      $oDataFile->setDescription($strDescription);
      $oDataFile->setUsageTypeId($iEntityTypeId);
      $strFileName = $oDataFile->getName();
      $uploadedFileNameParts = explode('.', $strFileName);
      if(sizeof($uploadedFileNameParts) == 2){
        $uploadedFileExtension = array_pop($uploadedFileNameParts);

        /* @var $oDocumentFormat DocumentFormat */
        $oDocumentFormat = DocumentFormatPeer::findByDefaultExtension($uploadedFileExtension);
        var_dump($oDocumentFormat);
        $oDataFile->setDocumentFormat($oDocumentFormat);
      }
      try{
        $oDataFile->save();
      }catch(Exception $e){
        echo "Unable to save drawing.";
        return;
      }
    }

    $strUrl = "/warehouse/projecteditor/project/"
                        .$oExperiment->getProject()->getId()
                        ."/experiment/".$oExperiment->getId()
                        ."/drawings";

    $this->setRedirect($strUrl);
  }

  public function saveDataFilePhoto(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo "Project not selected.";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not selected.";
      return;
    }

    $iDataFileId = JRequest::getInt("dataFileId", 0);
    if(!$iDataFileId){
      echo "Data file not selected.";
      return;
    }

    $iEntityTypeId = JRequest::getVar("usageType", 0);
    $iUsageTypeId = ($iEntityTypeId > 0) ? $iEntityTypeId : null;

    $iPhotoType = JRequest::getInt("photoType", 0);

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //edit the data file
    $strTitle = JRequest::getVar("title", "");
    $strDescription = JRequest::getVar("desc","");

    $oDataFile = DataFilePeer::retrieveByPK($iDataFileId);
    $oDataFile->setTitle($strTitle);
    $oDataFile->setDescription($strDescription);
    $oDataFile->setUsageTypeId($iUsageTypeId);
    try{
      $oDataFile->save();
    }catch(Exception $e){
      echo "Unable to save data file.";
      return;
    }

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/photos";

    if($iPhotoType){
      $strUrl .= "?photoType=".$iPhotoType;
    }

    $this->setRedirect($strUrl);
  }

  public function saveDataFileVideo(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo "Project not selected.";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "Experiment not selected.";
      return;
    }

    $iDataFileId = JRequest::getInt("dataFileId", 0);
    if(!$iDataFileId){
      echo "Data file not selected.";
      return;
    }

    $iEntityTypeId = JRequest::getVar("usageType", 0);
    $iUsageTypeId = ($iEntityTypeId > 0) ? $iEntityTypeId : null;

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //edit the data file
    $strTitle = JRequest::getVar("title", "");
    $strDescription = JRequest::getVar("desc","");

    $oDataFile = DataFilePeer::retrieveByPK($iDataFileId);
    $oDataFile->setTitle($strTitle);
    $oDataFile->setDescription($strDescription);
    $oDataFile->setUsageTypeId($iUsageTypeId);
    try{
      $oDataFile->save();
    }catch(Exception $e){
      echo "Unable to save data file.";
      return;
    }

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/videos";

    $this->setRedirect($strUrl);
  }

  public function saveTrial(){
    //incoming
    $iExperimentId = JRequest::getVar('experimentId','');
    $iTrialId = JRequest::getVar('trialId', 0);

    $trtitle = trim(JRequest::getVar('title',''));
    $trobj   = trim(JRequest::getVar('objective',''));
    $trdesc  = trim(JRequest::getVar('description',''));
    $startDate = trim(JRequest::getVar('startdate','') );
    $endDate   = trim(JRequest::getVar('enddate',''));
    $s_epoch = $e_epoch = 0;

    //validation
    if (!StringHelper::hasText($trtitle)) {
      $alert = "Please enter a valid Trial Title";
      echo $alert;
      return;
    }

    require_once 'api/org/nees/lib/ui/FormValidator.php';

    if( empty($startDate) )
      $startDate = null;
    else {
      $s_epoch = FormValidator::getHubEpochFromString($startDate);

      if($s_epoch == -1) {
        echo "Start date is out of range. NEEShub does not support this date.";
        return;
      }
      elseif($s_epoch == false) {
        echo "Please enter a valid start date. (MM/DD/YYYY)";
        return;
      }
      else {
        $startDate = date("Y-m-d", $s_epoch);
      }
    }

    if( empty($endDate) )
      $endDate = null;
    else {
      $e_epoch = FormValidator::getHubEpochFromString($endDate);

      if($e_epoch == -1) {
        $this->setAlertmsg("End date is out of range. NEEShub does not support this date."); return;
      }
      elseif($e_epoch == false) {
        $this->setAlertmsg("Please enter a valid end date. (MM/DD/YYYY)"); return;
      }
      elseif($endDate && $s_epoch > $e_epoch) {
        $this->setAlertmsg("Start date must be before or same as end date"); return;
      }
      else {
        $endDate = date("Y-m-d", $e_epoch);
      }
    }

    /* @var $oModel ProjectEditorModelCreateTrial */
    $oModel =& $this->getModel('CreateTrial');

    /* @var $oExperiment Experiment */
    $oExperiment = $oModel->getExperimentById($iExperimentId);
    if(!$oExperiment){
      echo "Experiment not selected";
      return;
    }

    /* @var $oTrial Trial */
    if($iTrialId){
      var_dump($startDate);
      echo "<br>";
      var_dump($endDate);
      $oTrial = $oModel->getTrialById($iTrialId);
      $oTrial->setDescription($trdesc);
      $oTrial->setObjective($trobj);
      $oTrial->setTitle($trtitle);
      $oTrial->setStartDate($startDate);
      $oTrial->setEndDate($endDate);

      $oTrial->save();

      //var_dump($oTrial);
    }else{
      $oTrial = new Trial(
        $oExperiment,
        TrialPeer::getNextAvailableName($oExperiment),
        $trtitle,
        $trobj,
        $trdesc,
        $startDate,
        $endDate
      );

      $oTrial->save();

      $trialdir = $oTrial->getPathname();

      FileCommandAPI::create("$trialdir")->mkdir();
      //FileCommandAPI::create("$trialdir/Configuration")->mkdir();
      FileCommandAPI::create("$trialdir/Analysis")->mkdir();
      FileCommandAPI::create("$trialdir/Documentation")->mkdir();
      //FileCommandAPI::create("$trialdir/InputMotion")->mkdir();


      $repetition = new Repetition(
        $oTrial,
        RepetitionPeer::getNextRepetitionName($oTrial),
        $startDate,
        $endDate
      );

      $repetition->save();

      $repdir = $repetition->getPathname();

      FileCommandAPI::create($repdir)->mkdir();
      FileCommandAPI::create("$repdir/Unprocessed_Data/Photos")->mkdir();
      FileCommandAPI::create("$repdir/Unprocessed_Data/Videos/Frames")->mkdir();
      FileCommandAPI::create("$repdir/Unprocessed_Data/Videos/Movies")->mkdir();
      FileCommandAPI::create("$repdir/Converted_Data/Photos")->mkdir();
      FileCommandAPI::create("$repdir/Converted_Data/Videos/Frames")->mkdir();
      FileCommandAPI::create("$repdir/Converted_Data/Videos/Movies")->mkdir();
      FileCommandAPI::create("$repdir/Corrected_Data/Photos")->mkdir();
      FileCommandAPI::create("$repdir/Corrected_Data/Videos/Frames")->mkdir();
      FileCommandAPI::create("$repdir/Corrected_Data/Videos/Movies")->mkdir();
      FileCommandAPI::create("$repdir/Derived_Data/Photos")->mkdir();
      FileCommandAPI::create("$repdir/Derived_Data/Videos/Frames")->mkdir();
      FileCommandAPI::create("$repdir/Derived_Data/Videos/Movies")->mkdir();

      FileHelper::fixPermissions($trialdir);
    }

    $strUrl = "/warehouse/projecteditor/project/"
                        .$oExperiment->getProject()->getId()
                        ."/experiment/".$oExperiment->getId()
                        ."/data";
    $this->setRedirect($strUrl);
  }

  public function saveRepetition(){
    //Incoming
    $iTrialId = JRequest::getInt("trial",0);
    $iRepetitionId = JRequest::getInt("repetition",0);
    $startDate = trim(JRequest::getVar('startdate','') );
    $endDate   = trim(JRequest::getVar('enddate',''));
    $s_epoch = $e_epoch = 0;

    /* @var $oModel ProjectEditorModelCreateRepetition */
    $oModel =& $this->getModel('CreateRepetition');

    /* @var $trial Trial */
    $trial = $oModel->getTrialById($iTrialId);
    if(!trial){
      echo "<p class='error'>Please select or create a Trial.</p>";
      return;
    }

    /* @var $repetition Repetition */
    $repetition = $oModel->getRepetitionById($iRepetitionId);

    ##
    ## form validation
    $alert = "";

    require_once 'api/org/nees/lib/ui/FormValidator.php';

    if( empty($startDate) )
      $startDate = null;
    else {
      $s_epoch = FormValidator::getHubEpochFromString($startDate);

      if($s_epoch == -1) {
        echo "Start date is out of range. NEEShub does not support this date.";
        return;
      }
      elseif($s_epoch == false) {
        echo "Please enter a valid start date. (MM/DD/YYYY)";
        return;
      }
      else {
        $startDate = date("Y-m-d", $s_epoch);
      }
    }

    if( empty($endDate) )
      $endDate = null;
    else {
      $e_epoch = FormValidator::getHubEpochFromString($endDate);

      if($e_epoch == -1) {
        $this->setAlertmsg("End date is out of range. NEEShub does not support this date."); return;
      }
      elseif($e_epoch == false) {
        $this->setAlertmsg("Please enter a valid end date. (MM/DD/YYYY)"); return;
      }
      elseif($endDate && $s_epoch > $e_epoch) {
        $this->setAlertmsg("Start date must be before or same as end date"); return;
      }
      else {
        $endDate = date("Y-m-d", $e_epoch);
      }
    }


    if($repetition) {
      $repetition->setStartDate($startDate);
      $repetition->setEndDate($endDate);
    }else {
      $repetition = new Repetition(
        $trial,
        RepetitionPeer::getNextRepetitionName($trial),
        $startDate,
        $endDate
      );
    }
    $repetition->save();

    $repdir = $repetition->getPathname();

//    FileCommandAPI::create($repdir)->mkdir(true);
//    FileCommandAPI::create("$repdir/Unprocessed_Data")->mkdir();
//    FileCommandAPI::create("$repdir/Converted_Data")->mkdir();
//    FileCommandAPI::create("$repdir/Corrected_Data")->mkdir();
//    FileCommandAPI::create("$repdir/Derived_Data")->mkdir();

    FileCommandAPI::create($repdir)->mkdir();
    FileCommandAPI::create("$repdir/Unprocessed_Data/Photos")->mkdir();
    FileCommandAPI::create("$repdir/Unprocessed_Data/Videos/Frames")->mkdir();
    FileCommandAPI::create("$repdir/Unprocessed_Data/Videos/Movies")->mkdir();
    FileCommandAPI::create("$repdir/Converted_Data/Photos")->mkdir();
    FileCommandAPI::create("$repdir/Converted_Data/Videos/Frames")->mkdir();
    FileCommandAPI::create("$repdir/Converted_Data/Videos/Movies")->mkdir();
    FileCommandAPI::create("$repdir/Corrected_Data/Photos")->mkdir();
    FileCommandAPI::create("$repdir/Corrected_Data/Videos/Frames")->mkdir();
    FileCommandAPI::create("$repdir/Corrected_Data/Videos/Movies")->mkdir();
    FileCommandAPI::create("$repdir/Derived_Data/Photos")->mkdir();
    FileCommandAPI::create("$repdir/Derived_Data/Videos/Frames")->mkdir();
    FileCommandAPI::create("$repdir/Derived_Data/Videos/Movies")->mkdir();

    FileHelper::fixPermissions($repdir);

    $strUrl = "/warehouse/projecteditor/project/"
                        .$trial->getExperiment()->getProject()->getId()
                        ."/experiment/".$trial->getExperiment()->getId()
                        ."/data";
    $this->setRedirect($strUrl);
  }

  public function saveDataFileCurateRequest(){
    $iProjectId = JRequest::getInt("projid" ,0);
    if(!$iProjectId){
      echo "<p class='error'>Project not selected.</p>";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "<p class='error'>Experiment not selected.</p>";
      return;
    }

    $iDataFileIdArray = null;
    if(!isset($_POST['dataFile'])){
      echo "<p class='error'>Please select at least 1 directory or file.</p>";
      return;
    }else{
      $iDataFileIdArray=$_POST['dataFile'];
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    while (list ($key,$iDataFileId) = @each ($iDataFileIdArray)) {
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $oDataFile->setCurationStatus(ProjectEditor::CURATION_REQUEST);
      $oDataFile->save();

      if($oDataFile->getDirectory()==1){
        $dfs = DataFilePeer::findAllInDir($oDataFile->getFullPath());
        foreach($dfs as $df) {
          /* @var $df DataFile */
          $fullPath = $df->getFullPath();
          if(file_exists($fullPath)) {
            $df->setCurationStatus(ProjectEditor::CURATION_REQUEST);
            $df->save();
          }
        }//end foreach
      }//end if directory
    }//end while

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/data";

    $strPath = JRequest::getVar('path', '');
    if(StringHelper::hasText($strPath)){
      $strUrl .= "?path=".$strPath;
    }

    $this->setRedirect($strUrl);
  }

  public function saveSecurity(){
    $iProjectId = JRequest::getInt("projectId" ,0);
    if(!$iProjectId){
      echo "<p class='error'>Project not selected.</p>";
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "<p class='error'>Experiment not selected.</p>";
      return;
    }

    /* @var $oModel ProjectEditorModelExperiment */
    $oModel =& $this->getModel('Experiment');

    /* @var $oExperiment Experiment */
    $oExperiment = $oModel->getExperimentById($iExperimentId);
    if(!$oExperiment){
      echo "<p class='error'>Experiment not selected.</p>";
      return;
    }

    $strAccess = "";
    $iAccess = JRequest::getInt("access", 4);
    switch($iAccess){
      case 0: $strAccess = "PUBLIC"; break;
      case 3: $strAccess = "USERS"; break;
      default: $strAccess = "MEMBERS";
    }

    $oExperiment->setView($strAccess);
    $oExperiment->save();

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/security";
    $this->setRedirect($strUrl);
  }

  /**
   *
   *
   */
  public function saveFilmstrip1(){
    $iProjectId = JRequest::getInt("projid" ,0);
    if(!$iProjectId){
      echo ComponentHtml::showError("Project not selected.");
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo ComponentHtml::showError("Experiment not selected.");
      return;
    }

    $iDataFileIdArray = null;
    if(!isset($_POST['dataFile'])){
      echo ComponentHtml::showError("Please select at least 1 file.");
      return;
    }else{
      $iDataFileIdArray=$_POST['dataFile'];
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //validate extensions
    $strInvalidFileArray = array();
    while (list ($key,$iDataFileId) = @each ($iDataFileIdArray)) {
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $strFileName = $oDataFile->getName();

      //get the file's extension
      $uploadedFileNameParts = explode('.', $strFileName);
      if(sizeof($uploadedFileNameParts) != 2){
        array_push($strInvalidFileArray, $strFileName);
      }
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      //validate extension

      $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
      if(!in_array(strtolower($uploadedFileExtension), $validFileExts)){
        array_push($strInvalidFileArray, $strFileName);
      }
    }

    if(!empty($strInvalidFileArray)){
      $strInvalidFiles = implode(",", $strInvalidFileArray);
      echo ComponentHtml::showError("Invalid file type(s): $strInvalidFiles");
      return;
    }

    /* @var $oPhotoEntityType EntityType */
    $oPhotoEntityType = EntityTypePeer::findByTableName(ProjectEditor::FILMSTRIP_IMAGE);

    //update and scale
    $iCount = 0;
    foreach($iDataFileIdArray as $iDataFileId){
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $oDataFile->setUsageTypeId($oPhotoEntityType->getId());
      $oDataFile->save();

      //invoke the upload plugin
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      $_REQUEST["fixPermissionsLater"] = true;
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
      $oResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);

      ++$iCount;
    }//end while

    $strPath = JRequest::getVar('path');
    if($iCount > 0){
      FileHelper::fixPermissions($strPath);
    }

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/data?path=".$strPath;

    $this->setRedirect($strUrl);
  }

  /**
   *
   *
   */
  public function saveFilmstrip(){
    $iProjectId = JRequest::getInt("projid" ,0);
    if(!$iProjectId){
      echo ComponentHtml::showError("Project not selected.");
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo ComponentHtml::showError("Experiment not selected.");
      return;
    }

    $iDataFileIdArray = null;
    if(!isset($_REQUEST['dataFile'])){
      echo ComponentHtml::showError("Please select at least 1 file.");
      return;
    }else{
      $iDataFileIdArray=$_REQUEST['dataFile'];
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //validate extensions
    $strInvalidFileArray = array();
    while (list ($key,$iDataFileId) = @each ($iDataFileIdArray)) {
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $strFileName = $oDataFile->getName();

      //get the file's extension
      $uploadedFileNameParts = explode('.', $strFileName);
      if(sizeof($uploadedFileNameParts) != 2){
        array_push($strInvalidFileArray, $strFileName);
      }
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      //validate extension

      $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
      if(!in_array(strtolower($uploadedFileExtension), $validFileExts)){
        array_push($strInvalidFileArray, $strFileName);
      }
    }

    if(!empty($strInvalidFileArray)){
      $strInvalidFiles = implode(",", $strInvalidFileArray);
      echo ComponentHtml::showError("Invalid file type(s): $strInvalidFiles");
      return;
    }

    /* @var $oPhotoEntityType EntityType */
    $oPhotoEntityType = EntityTypePeer::findByTableName(ProjectEditor::FILMSTRIP_IMAGE);

    //update and scale
    $iCount = 0;
    foreach($iDataFileIdArray as $iDataFileId){
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $oDataFile->setUsageTypeId($oPhotoEntityType->getId());
      $oDataFile->save();

      //invoke the upload plugin
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      $_REQUEST["fixPermissionsLater"] = true;
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
      $oResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);

      ++$iCount;
    }//end while

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/photos";

    $strPath = JRequest::getVar("path", "");
    if(StringHelper::hasText($strPath)){
      if($iCount > 0){
        FileHelper::fixPermissions($strPath);
      }

      $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/data?path=".$strPath;
    }

    $this->setRedirect($strUrl);
  }

  /**
   *
   *
   */
  public function saveMorePhotos1(){
    $iProjectId = JRequest::getInt("projid" ,0);
    if(!$iProjectId){
      echo ComponentHtml::showError("Project not selected.");
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo ComponentHtml::showError("Experiment not selected.");
      return;
    }

    $iDataFileIdArray = null;
    if(!isset($_POST['dataFile'])){
      echo ComponentHtml::showError("Please select at least 1 file.");
      return;
    }else{
      $iDataFileIdArray=$_POST['dataFile'];
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //validate extensions
    $strInvalidFileArray = array();
    while (list ($key,$iDataFileId) = @each ($iDataFileIdArray)) {
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $strFileName = $oDataFile->getName();

      //get the file's extension
      $uploadedFileNameParts = explode('.', $strFileName);
      if(sizeof($uploadedFileNameParts) != 2){
        array_push($strInvalidFileArray, $strFileName);
      }
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      //validate extension

      $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
      if(!in_array(strtolower($uploadedFileExtension), $validFileExts)){
        array_push($strInvalidFileArray, $strFileName);
      }
    }

    if(!empty($strInvalidFileArray)){
      $strInvalidFiles = implode(",", $strInvalidFileArray);
      echo ComponentHtml::showError("Invalid file type(s): $strInvalidFiles");
      return;
    }

    /* @var $oPhotoEntityType EntityType */
    $oPhotoEntityType = EntityTypePeer::findByTableName(ProjectEditor::GENERAL_IMAGE);

    //update and scale
    $iCount = 0;
    foreach($iDataFileIdArray as $iDataFileId){
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $oDataFile->setUsageTypeId($oPhotoEntityType->getId());
      $oDataFile->save();

      //invoke the upload plugin
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      $_REQUEST["fixPermissionsLater"] = true;
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
      $oResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);

      ++$iCount;
    }//end while

    $strPath = JRequest::getVar('path');
    if($iCount > 0){
      FileHelper::fixPermissions($strPath);
    }

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/data?path=".$strPath;

    $this->setRedirect($strUrl);
  }

  /**
   *
   *
   */
  public function saveMorePhotos(){
    $iProjectId = JRequest::getInt("projid" ,0);
    if(!$iProjectId){
      echo ComponentHtml::showError("Project not selected.");
      return;
    }

    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo ComponentHtml::showError("Experiment not selected.");
      return;
    }

    $iDataFileIdArray = null;
    if(!isset($_POST['dataFile'])){
      echo ComponentHtml::showError("Please select at least 1 file.");
      return;
    }else{
      $iDataFileIdArray=$_POST['dataFile'];
    }

    /* @var $oModel ProjectEditorModelEditDataFile */
    $oModel =& $this->getModel('EditDataFile');

    //validate extensions
    $strInvalidFileArray = array();
    while (list ($key,$iDataFileId) = @each ($iDataFileIdArray)) {
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $strFileName = $oDataFile->getName();

      //get the file's extension
      $uploadedFileNameParts = explode('.', $strFileName);
      if(sizeof($uploadedFileNameParts) != 2){
        array_push($strInvalidFileArray, $strFileName);
      }
      $uploadedFileExtension = array_pop($uploadedFileNameParts);

      //validate extension

      $validFileExts = explode(',', ProjectEditor::VALID_IMAGE_EXTENSIONS);
      if(!in_array(strtolower($uploadedFileExtension), $validFileExts)){
        array_push($strInvalidFileArray, $strFileName);
      }
    }

    if(!empty($strInvalidFileArray)){
      $strInvalidFiles = implode(",", $strInvalidFileArray);
      echo ComponentHtml::showError("Invalid file type(s): $strInvalidFiles");
      return;
    }

    /* @var $oPhotoEntityType EntityType */
    $oPhotoEntityType = EntityTypePeer::findByTableName(ProjectEditor::GENERAL_IMAGE);

    //update and scale
    $iCount = 0;
    foreach($iDataFileIdArray as $iDataFileId){
      /* @var $oDataFile DataFile */
      $oDataFile = $oModel->getDataFileById($iDataFileId);
      $oDataFile->setUsageTypeId($oPhotoEntityType->getId());
      $oDataFile->save();

      //invoke the upload plugin
      JPluginHelper::importPlugin( 'project', 'upload' );
      $oDispatcher =& JDispatcher::getInstance();
      $strParamArray = array(0,0);

      $_REQUEST["fixPermissionsLater"] = true;
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oDataFile);
      $oResultsArray = $oDispatcher->trigger('onScaleImageDataFile',$strParamArray);

      ++$iCount;
    }//end while

    $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/photos";

    $strPath = JRequest::getVar("path", "");
    if(StringHelper::hasText($strPath)){
      if($iCount > 0){
        FileHelper::fixPermissions($strPath);
      }

      $strUrl = "/warehouse/projecteditor/project/".$iProjectId
                        ."/experiment/".$iExperimentId
                        ."/data?path=".$strPath;
    }

    $this->setRedirect($strUrl);
  }

}

?>