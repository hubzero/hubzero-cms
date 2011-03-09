<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'lib/data/ProjectHomepageURL.php';
require_once 'lib/data/ProjectHomepage.php';
require_once 'lib/data/ProjectHomepagePeer.php';
require_once 'lib/data/ProjectGrant.php';
require_once 'lib/data/ProjectGrantPeer.php';
require_once 'lib/data/NeesAwardType.php';
require_once 'lib/data/NeesAwardTypePeer.php';
require_once 'lib/data/SponsorPeer.php';
require_once 'lib/data/Authorization.php';
require_once 'lib/data/Role.php';
require_once 'lib/data/RolePeer.php';
require_once 'lib/data/PersonEntityRole.php';
require_once 'lib/data/ThumbnailPeer.php';
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/NeesResearchTypePeer.php';
require_once 'lib/security/Permissions.php';

class ProjectEditorModelProject extends ProjectEditorModelBase{


  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  public function getProjectOwner(){
    $oUser =& JFactory::getUser();
    return $oUser;
  }

  public function suggestFacilities($p_strName) {
    return OrganizationPeer::suggestFacilities($p_strName);
  }

  public function suggestOrganizations($p_strName, $p_iLimit){
    return OrganizationPeer::suggestOrganizations($p_strName, $p_iLimit);
  }

  public function suggestSponsors($p_strName, $p_iLimit){
    return SponsorPeer::suggestSponsors($p_strName, $p_iLimit);
  }

  /**
   *
   * @param string $title
   * @param string $description
   * @param string $contactName
   * @param string $contactEmail
   * @param string $sysadminName
   * @param string $sysadminEmail
   * @param string $startDate
   * @param string $endDate
   * @param string $ack
   * @param string $view
   * @param int $projectTypeId
   * @param int $nees
   * @param string $nickname
   * @param string $fundorg
   * @param string $fundorgprojid
   * @param string $status
   * @param int $creatorId
   * @return Project
   */
  public function createProject($title, $description, $contactName, $contactEmail,
                            $sysadminName, $sysadminEmail, $startDate, $endDate,
                            $ack, $view, $projectTypeId, $nees, $nickname,
                            $fundorg, $fundorgprojid, $status, $creatorId){

    switch ($projectTypeId) {
      case ProjectPeer::CLASSKEY_UNSTRUCTUREDPROJECT :
        $project = new UnstructuredProject(
                            $title, $description, $contactName, $contactEmail,
                            $sysadminName, $sysadminEmail, $startDate, $endDate,
                            $ack, $view, $projectTypeId, $nees, $nickname,
                            $fundorg, $fundorgprojid, $status, $creatorId);
      break;

      case ProjectPeer::CLASSKEY_STRUCTUREDPROJECT :
        $project = new StructuredProject(
                            $title, $description, $contactName, $contactEmail,
                            $sysadminName, $sysadminEmail, $startDate, $endDate,
                            $ack, $view, $projectTypeId, $nees, $nickname,
                            $fundorg, $fundorgprojid, $status, $creatorId);
      break;

      case ProjectPeer::CLASSKEY_HYBRIDPROJECT :
        $project = new HybridProject(
                            $title, $description, $contactName, $contactEmail,
                            $sysadminName, $sysadminEmail, $startDate, $endDate,
                            $ack, $view, $projectTypeId, $nees, $nickname,
                            $fundorg, $fundorgprojid, $status, $creatorId);
      break;

      case ProjectPeer::CLASSKEY_SUPERPROJECT :
        $project = new SuperProject(
                            $title, $description, $contactName, $contactEmail,
                            $sysadminName, $sysadminEmail, $startDate, $endDate,
                            $ack, $view, $projectTypeId, $nees, $nickname,
                            $fundorg, $fundorgprojid, $status, $creatorId);
      break;
      default:
        throw new Exception("Invalid ProjectTypeId value.");
    }
    //$project->save();

    return $project;
  }

  /**
   *
   * @param int $p_iCreatorId
   * @param int $p_iProjectId
   * @return Authorization
   */
  public function createAuthorization($p_iCreatorId, $p_iProjectId){
    /* @var $perms Permissions */
    $perms = new Permissions( Permissions::PERMISSION_ALL );

    /* @var $auth Authorization */
    $auth  = new Authorization($p_iCreatorId, $p_iProjectId,  DomainEntityType::ENTITY_TYPE_PROJECT, $perms );
    $auth->save();

    return $auth;
  }

  /**
   *
   * @param int $p_iCreatorId
   * @param int $p_iProjectId
   * @param int $p_iRoleId
   * @return PersonEntityRole
   */
  public function createPersonEntityRole($p_iCreatorId, $p_iProjectId, $p_strRoleDisplayName){
    /* @var $oRole Role */
    //$oRole = RolePeer::find($p_iRoleId);
    $oRole = RolePeer::findByNameEntityType($p_strRoleDisplayName, 1);

    /* @var $oPersonEntityRole PersonEntityRole */
    $oPersonEntityRole = new PersonEntityRole($p_iCreatorId, $p_iProjectId,  DomainEntityType::ENTITY_TYPE_PROJECT, $oRole);
    $oPersonEntityRole->save();

    return $oPersonEntityRole;
  }

  /**
   * Checks to see if a project already has a ProjectOrganization
   * @return true/false
   */
  private function hasProjectOrganization($p_oProject, $p_strNewOrgName){
    $bReturn = false;

    $oExistingProjectOrganizationArray = OrganizationPeer::findByProject($p_oProject->getId());
    foreach($oExistingProjectOrganizationArray as $oThisProjectOrganization){
      if($oThisProjectOrganization->getName()==$p_strNewOrgName){
            $bReturn = true;
      }
    }
    return $bReturn;
  }

  public function setSessionOrganizations($p_oOrganizationArray){
    $strInputField = "organization";
    $strInputArray = array();

    /* @var $oOrganization Organization */
    foreach($p_oOrganizationArray as $iIndex=>$oOrganization){
      array_push($strInputArray, $oOrganization->getName());
    }

    $_SESSION[$strInputField] = $strInputArray;
  }

  public function setOrganizations($p_oOrganizationArray){
    $oProjectOrganizationArray = array();
    foreach($p_oOrganizationArray as $oOrganization){
      $oProjectOrganization = new ProjectOrganization(null, $oOrganization);
      array_push($oProjectOrganizationArray, $oProjectOrganization);
    }//end loop

    return $oProjectOrganizationArray;
  }

  /**
   * Initialize the recursive setOrganizationHelper method.
   * @param $p_oProject
   * @param $p_strOrganizationName
   * @param $p_strOrganizationArray
   * @returns Project
   */
  /*
  public function setOrganizations($p_oProject, $p_strOrganizationName, $p_strOrganizationArray=null, $p_iOrganizationIndex=0){
    $oReturnArray = array();
    if(StringHelper::hasText($p_strOrganizationName)){
      $oOrganization = $this->findOrganizationByName($p_strOrganizationName);
      if(!$oOrganization){
        throw new ValidationException($p_strOrganizationName. " is not a valid organization.");
      }

      $oProjectOrganization = new ProjectOrganization(null, $oOrganization);
      //$p_oProject->addProjectOrganization($oProjectOrganization);
      if(!$this->containsProjectOrganization($oProjectOrganization, $oReturnArray)){
        array_push($oReturnArray, $oProjectOrganization);
      }
    }

    return $this->setOrganizationHelper($p_oProject, $p_strOrganizationArray, $oReturnArray);
  }
   */

  /**
   * Recursively append ProjectOrganizations to the Project.
   * @param Project $p_oProject
   * @param array $p_strOrganizationArray
   * @param int $p_iOrganizationIndex
   * @param array $p_oResultsArray
   * @return array
   */
  /*
  public function setOrganizationHelper($p_oProject, $p_strOrganizationArray=null, $p_oResultsArray=array(), $p_iOrganizationIndex=0){
    if($p_strOrganizationArray != null){
      $strOrganizationName = $p_strOrganizationArray[$p_iOrganizationIndex];
      $oOrganization = $this->findOrganizationByName($strOrganizationName);
      if(!$oOrganization){
        throw new ValidationException($p_strOrganizationName. " is not a valid organization.");
      }

      $oProjectOrganization = new ProjectOrganization(null, $oOrganization);
      //$p_oProject->addProjectOrganization($oProjectOrganization);
      if(!$this->containsProjectOrganization($oProjectOrganization, $p_oResultsArray)){
        array_push($p_oResultsArray, $oProjectOrganization);
      }
      ++$p_iOrganizationIndex;
      if( $p_iOrganizationIndex < sizeof($p_strOrganizationArray) ){
        $p_oResultsArray = $this->setOrganizationHelper($p_oProject, $p_strOrganizationArray, $p_oResultsArray, $p_iOrganizationIndex);
      }
    }

    //return $p_oProject;
    return $p_oResultsArray;
  }
   */

  public function setSessionWebsites($oProjectHomepageArray){
    $strInputField1 = "website";
    $strInputField2 = "url";

    $strInputArray = array();

    /* @var $oProjectHomepage ProjectHomepage */
    foreach($oProjectHomepageArray as $oProjectHomepage){
      $oTuple = new Tuple($oProjectHomepage->getCaption(), $oProjectHomepage->getUrl(), $strInputField1, $strInputField2);
      array_push($strInputArray, serialize($oTuple));
    }

    $_SESSION[$strInputField1] = $strInputArray;
  }

  public function setSessionProjectGrants($p_oProjectGrantArray){
    $strInputField1 = "sponsor";
    $strInputField2 = "award";

    $strInputArray = array();

    /* @var $oProjectGrant ProjectGrant */
    foreach($p_oProjectGrantArray as $oProjectGrant){
      $oTuple = new Tuple($oProjectGrant->getFundingOrg(), $oProjectGrant->getAwardNumber(), $strInputField1, $strInputField2);
      array_push($strInputArray, serialize($oTuple));
    }

    $_SESSION[$strInputField1] = $strInputArray;
  }

  public function setWebsites($p_strWebsiteArray, $p_strUrlArray){
    unset ($_SESSION[ProjectHomepagePeer::TABLE_NAME]);

    if(count($p_strWebsiteArray) != count($p_strUrlArray)){
      throw new ValidationException("Missing website or url.");
    }

    $iIndex=0;
    $oResultsArray = array();
    while (list ($key, $strWebsite) = @each ($p_strWebsiteArray)) {
      if(StringHelper::hasText($strWebsite) && $strWebsite != "Website Title"){
        $oProjectHomepageURL = new ProjectHomepageURL();
        $oProjectHomepageURL->setUrl($p_strUrlArray[$iIndex]);
        $oProjectHomepageURL->setCaption($strWebsite);
        $oProjectHomepageURL->setDescription($strWebsite);
        $oProjectHomepageURL->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_1);
        array_push($oResultsArray, $oProjectHomepageURL);
      }//if hasText

      ++$iIndex;
    }//end loop

    return $oResultsArray;
  }

  /**
   *
   * @param ProjectOrganization $p_oProjectOrganization
   * @param array $p_oOrganizationArray
   * @return boolean
   */
  public function containsProjectOrganization($p_oProjectOrganization, $p_oOrganizationArray){
    $bFound = false;

    if(empty($p_oOrganizationArray)){
      return false;
    }

    $iOrgIdNeedle = $p_oProjectOrganization->getId();

    /* @var $oThisOrganization ProjectOrganization */
    foreach($p_oOrganizationArray as $oThisOrganization){
      $iThisOrgIdNeedle = $oThisOrganization->getId();
      if($iOrgIdNeedle === $iThisOrgIdNeedle){
        $bFound = true;
        break;
      }
    }

    return $bFound;
  }

  /**
   *
   * @param ProjectHomepageURL $p_oProjectHomepageURL
   * @param array $p_oHomepageArray
   * @return boolean
   */
  public function containsWebsite($p_oProjectHomepageURL, $p_oHomepageArray){
    $bFound = false;
    if(empty ($p_oHomepageArray)){
      return $bFound;
    }

    $strCaptionNeedle = $p_oProjectHomepageURL->getCaption();

    /* @var $oThisProjectHomepageURL ProjectHomepageURL */
    foreach($p_oHomepageArray as $oThisProjectHomepageURL){
      $strThisCaption = $oThisProjectHomepageURL->getCaption();
      if($strCaptionNeedle == $strThisCaption){
        $bFound = true;
        break;
      }
    }

    return $bFound;
  }

  public function containsSponsor($p_oProjectGrant, $p_oSponsorTupleArray){
    if(empty($p_oSponsorTupleArray)){
      return false;
    }

    return in_array($p_oProjectGrant, $p_oSponsorTupleArray);
  }

  public function setSponsors($p_strSponsorNameArray, $p_strAwardNumberArray, $p_strNsfAwardTypeArray){
    $iIndex = 0;
    $oProjectGrantArray = array();
    while (list ($key,$strSponsorName) = @each ($p_strSponsorNameArray)) {
      $strAward = (isset($p_strAwardNumberArray[$iIndex])) ? $p_strAwardNumberArray[$iIndex] : null;
      if(StringHelper::hasText($strSponsorName) && StringHelper::hasText($strAward)){
        if($strAward == "Award Number" && $strSponsorName == "NSF"){
          //do nothing
        }else{
          $strAwardUrl = null;
          $iNsfAwardTypeId = 0;
          if($strSponsorName == "NSF"){
            if(is_numeric($strAward)){
              $strAwardUrl =  "http://www.nsf.gov/awardsearch/showAward.do?AwardNumber=".$strAward;
            }

            if(isset($p_strNsfAwardTypeArray[$iIndex])){
              $iNsfAwardTypeId = (is_numeric($p_strNsfAwardTypeArray[$iIndex])) ? $p_strNsfAwardTypeArray[$iIndex] : 0;
            }
          }

          /* @var $oOrganization Organization */
          $oProjectGrant = new ProjectGrant($strSponsorName, $strAward, $strAwardUrl);
          if($iNsfAwardTypeId > 0){
            $oProjectGrant->setNeesAwardTypeId($iNsfAwardTypeId);
          }
          array_push($oProjectGrantArray, $oProjectGrant);
        }
      }
      ++$iIndex;
    }//end loop

    return $oProjectGrantArray;
  }

  /**
   *
   * @param Project $p_oProject
   * @param array $p_iOrganizationArray
   * @param Connection $p_oConnection
   */
  public function createProjectOrganizations($p_oProject, $p_iOrganizationArray, $p_oConnection=null){
    if($p_oConnection){
      ProjectOrganizationPeer::deleteByProject($p_oProject->getId(), $p_oConnection);

      foreach($p_iOrganizationArray as $iOrganizationId){
        /* @var $oOrganization Organization */
        $oOrganization = OrganizationPeer::retrieveByPK($iOrganizationId);

        $oProjectOrganization = new ProjectOrganization($p_oProject, $oOrganization);
        $oProjectOrganization->save();
      }
    }else{
      try{
        $oConnection->begin();
        ProjectOrganizationPeer::deleteByProject($p_oProject->getId());

        foreach($p_iOrganizationArray as $iOrganizationId){
          /* @var $oOrganization Organization */
          $oOrganization = OrganizationPeer::retrieveByPK($iOrganizationId);

          /* @var $oProjectOrganization ProjectOrganization */
          $oProjectOrganization = new ProjectOrganization($p_oProject, $oOrganization);
          $oProjectOrganization->save();
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
   * @param Project $p_oProject
   * @param array $p_oProjectGrantArray
   */
  public function createProjectGrants($p_oProject, $p_oProjectGrantArray, $p_oConnection=null){
    if($p_oConnection){
      ProjectGrantPeer::deleteByProject($p_oProject->getId(), $p_oConnection);

      foreach($p_oProjectGrantArray as $oProjectGrant){
        /* @var $oProjectGrant ProjectGrant */
        $oProjectGrant->setProject($p_oProject);
        $oProjectGrant->save();
      }
    }else{
      $oConnection = Propel::getConnection();

      try{
        $oConnection->begin();
        ProjectGrantPeer::deleteByProject($p_oProject->getId());

        foreach($p_oProjectGrantArray as $oProjectGrant){
          /* @var $oProjectGrant ProjectGrant */
          $oProjectGrant->setProject($p_oProject);
          $oProjectGrant->save();
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
   * @param Project $p_oProject
   * @param array $p_oProjectUrlArray
   */
  public function createProjectHomepages($p_oProject, $p_oProjectUrlArray, $p_oProjectHomepageTypeId=1, $p_oConnection=null){
    if($p_oConnection){
      // remove existing homepages
      ProjectHomepagePeer::deleteByProject($p_oProject->getId(), $p_oProjectHomepageTypeId, $p_oConnection);

      foreach($p_oProjectUrlArray as $oProjectHomepage){
        /* @var $oProjectHomepage ProjectHomepageURL */
        $strUrl = $oProjectHomepage->getUrl();
        if(StringHelper::endsWith($strUrl, ProjectEditor::DEFAULT_PROJECT_URL)){
          $strUrl = str_replace("[id]", $p_oProject->getId(), $strUrl);
        }
        $oProjectHomepage->setUrl($strUrl);
        $oProjectHomepage->setProject($p_oProject);
        $oProjectHomepage->save();
      }
    }else{
      $oConnection = Propel::getConnection();

      try{
        $oConnection->begin();

        // remove existing homepages
        ProjectHomepagePeer::deleteByProject($p_oProject->getId());

        foreach($p_oProjectUrlArray as $oProjectHomepage){
          /* @var $oProjectHomepage ProjectHomepageURL */
          $strUrl = $oProjectHomepage->getUrl();
          if(StringHelper::endsWith($strUrl, ProjectEditor::DEFAULT_PROJECT_URL)){
            $strUrl = str_replace("[id]", $p_oProject->getId(), $strUrl);
          }
          $oProjectHomepage->setUrl($strUrl);
          $oProjectHomepage->setProject($p_oProject);
          $oProjectHomepage->save();
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
   * @param Project $p_oProject
   * @param array $p_oResearcherKeywordArray
   */
  public function createNewGroup($p_oProject, $p_oResearcherKeywordArray, $p_oPiJuser, $p_oAdminJuser=null){
    ximport('xgroup');
    ximport('xuserhelper');

    $bGroupCreated = true;

    $strTagArray = array();
    if(!empty($p_oResearcherKeywordArray)){
      foreach($p_oResearcherKeywordArray as $oResearcherKeyword){
        /* @var $oResearcherKeyword ResearcherKeyword */
        array_push($strTagArray, $oResearcherKeyword->getKeywordTerm());
      }
    }

    $strGroupCn = str_replace("-",  "_",  $p_oProject->getName());

    // Incoming
    $g_cn           = strtolower(trim($strGroupCn));
    $g_description  = "Project: ".trim($p_oProject->getTitle());
    $g_privacy      = 4;
    $g_access       = 4;
    $g_gidNumber    = 0;
    $g_public_desc  = trim($p_oProject->getDescription());
    $g_private_desc = trim($p_oProject->getDescription());
    $g_restrict_msg = "";
    $g_join_policy  = 2;
    $tags = (!empty($strTagArray)) ? implode(",", $strTagArray) : "";

    $iGroupMembershipIdArray = array($p_oPiJuser->get('id'));
    if($p_oAdminJuser){
      array_push($iGroupMembershipIdArray, $p_oAdminJuser->get('id'));
    }

    // Instantiate an XGroup object
    $group = new XGroup();

    // Set the group changes and save
    $group->set('cn', $g_cn );
    $group->set('type', 1 );
    $group->set('published', 1 );

    //$group->add('managers',array($p_oPiJuser->get('id')));
    $group->add('members', $iGroupMembershipIdArray);
    $group->set('description', $g_description );
    $group->set('access', $g_access );
    $group->set('privacy', $g_privacy );
    $group->set('public_desc', $g_public_desc );
    $group->set('private_desc', $g_private_desc );
    $group->set('restrict_msg',$g_restrict_msg);
    $group->set('join_policy',$g_join_policy);
    $group->save();

    // Process tags
    $database =& JFactory::getDBO();
    $gt = new ProjectEditorTags( $database );
    $gt->tag_object($p_oPiJuser->get('id'), $group->get('gidNumber'), $tags, 1, 1);

    // Log the group save
    $database =& JFactory::getDBO();
    $log = new XGroupLog( $database );
    $log->gid = $group->get('gidNumber');
    $log->uid = $p_oPiJuser->get('id');
    $log->timestamp = date( 'Y-m-d H:i:s', time() );
    $log->actorid = $p_oPiJuser->get('id');

    // Rename the temporary upload directory if it exist
//    $lid = JRequest::getInt( 'lid', 0, 'post' );
//    if ($lid != $group->get('gidNumber')) {
//      $config = $this->config;
//      $bp = JPATH_ROOT;
//      if (substr($config->get('uploadpath'), 0, 1) != DS) {
//        $bp .= DS;
//      }
//      $bp .= $config->get('uploadpath');
//      if (is_dir($bp.DS.$lid)) {
//        rename($bp.DS.$lid, $bp.DS.$group->get('gidNumber'));
//      }
//    }

    $log->action = 'group_created';
    if (!$log->store()) {
      $this->setError( $log->getError() );
      $bGroupCreated = false;
    }

    return $bGroupCreated;
  }

  public function getProjectImage($p_iProjectId){
    require_once 'lib/data/DataFilePeer.php';

    return DataFilePeer::getProjectImage($p_iProjectId);
  }

  /**
   *
   *
   */
  public function getProjectLinks($p_oProject){
    return ProjectHomepagePeer::findProjectURLsByProjectId($p_oProject->getId());
  }

  public function getProjectLinksHtml($p_strPrefix, $p_oEntityArray){
    $strReturn = StringHelper::EMPTY_STRING;

    /* @var $oEntity ProjectHomepage */
    foreach ($p_oEntityArray as $iIndex=>$oEntity){
      $strCaption = $oEntity->getCaption();
      $strUrl = $oEntity->getUrl();
      $strInputDiv = $p_strPrefix."-".$iIndex."Input";
      $strCaptionFieldArray = "website[]";
      $strCaptionFieldPicked = $p_strPrefix."CaptionPicked";
      $strUrlFieldArray = "url[]";
      $strUrlFieldPicked = $p_strPrefix."UrlPicked";
      $strRemoveDiv = $p_strPrefix."-".$iIndex."Remove";

      $strReturn .= <<< ENDHTML

          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strCaptionFieldArray" value="$strCaption"/>
            <input type="hidden" name="$strUrlFieldArray" value="$strUrl"/>
            $strCaption (<a href='$strUrl'>view</a>)
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strCaption." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/removewebsite?format=ajax', '$p_strPrefix', $iIndex, 'websitePicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div class="clear"></div>

ENDHTML;

    }

    return $strReturn;
  }


  public function getProjectGrants($p_oProject){
    return ProjectGrantPeer::findByProjectId($p_oProject->getId());
  }

  /**
   *
   * @param string $p_strPrefix
   * @param array <ProjectGrant> $p_oEntityArray
   * @return string
   */
  public function getProjectGrantHTML($p_strPrefix, $p_oProjectGrantArray){
    $strReturn = StringHelper::EMPTY_STRING;

    $oNeesAwardTypeArray = NeesAwardTypePeer::findAll();

    /* @var $oProjectGrant ProjectGrant */
    foreach ($p_oProjectGrantArray as $iIndex=>$oProjectGrant){
      $strAwardInput = $oProjectGrant->getAwardNumber();
      $strSponsorInput = $oProjectGrant->getFundingOrg();
      $strUrlInput = $oProjectGrant->getAwardUrl();
      $iNeesAwardTypeId = 0;
      if($oProjectGrant->getNeesAwardType()){
        $iNeesAwardTypeId = $oProjectGrant->getNeesAwardType()->getId();
      }

      $strAwardDisplay = (StringHelper::hasText($strAwardInput)) ? " - $strAwardInput" : "";
      $strUrlDisplay = (StringHelper::hasText($strUrlInput)) ? " (<a href=\"$strUrlInput\">view</a>)" : "";

      $strInputDiv = $p_strPrefix."-".$iIndex."Input";
      $strRemoveDiv = $p_strPrefix."-".$iIndex."Remove";
      $strTypeDiv = $p_strPrefix."-".$iIndex."Type";

      $strAwardFieldArray = "award[]";
      $strAwardFieldPicked = $p_strPrefix."AwardPicked";

      $strSponsorFieldArray = "sponsor[]";
      $strSponsorFieldPicked = $p_strPrefix."SponsorPicked";

      $strUrlFieldArray = "sponsorUrl[]";
      $strUrlFieldPicked = $p_strPrefix."UrlPicked";

      $strNSFAwardOptions = "<input type='hidden' name='nsfAwardType[]' value='n/a'>";
      if(strtoupper($strSponsorInput)=="NSF"){
        $strNSFAwardOptions = <<< ENDHTML
          NSF Award Type: &nbsp;&nbsp;
          <select name="nsfAwardType[]" onChange="setNsfAwardType('/warehouse/projecteditor/nsfawardtype?format=ajax&index=$iIndex', this.value, '$strTypeDiv')">
            <option value="n/a">Non-Applicable</option>
ENDHTML;
        foreach($oNeesAwardTypeArray as $oNeesAwardType){
          $strSelected = ($iNeesAwardTypeId==$oNeesAwardType->getId()) ? "selected" : "";
          if(!StringHelper::hasText($strSelected) && isset($_SESSION["NSF_AWARD_TYPES"])){
            $iSelectedNsfAwardTypeIdArray = $_SESSION["NSF_AWARD_TYPES"];
            if(isset($iSelectedNsfAwardTypeIdArray[$iIndex])){
              if(is_numeric($iSelectedNsfAwardTypeIdArray[$iIndex])){
                $strSelected = ($iSelectedNsfAwardTypeIdArray[$iIndex]==$oNeesAwardType->getId()) ? "selected" : "";
              }
            }
          }

          /* @var $oNeesAwardType NeesAwardType */
          $strNSFAwardOptions .= "<option value=".$oNeesAwardType->getId()." $strSelected>".$oNeesAwardType->getDisplayName()."</option>";
        }
        $strNSFAwardOptions .= "</select>";
      }

      $strReturn .= <<< ENDHTML

          <div id="$strInputDiv" class="editorInputFloat editorInputSize">
            <input type="hidden" name="$strAwardFieldArray" value="$strAwardInput"/>
            <input type="hidden" name="$strSponsorFieldArray" value="$strSponsorInput"/>
            <input type="hidden" name="$strUrlFieldArray" value="$strUrlInput"/>
            <input type="hidden" name="$strUrlFieldArray" value="$strUrlInput"/>
            <table style="border: 0pt none;">
             <tr>
               <td style="padding: 0pt;">$strSponsorInput $strAwardDisplay $strUrlDisplay</td>
               <td align="right" style="padding: 0pt;">$strNSFAwardOptions</td>
             </tr>
            </table>
          </div>
          <div id="$strRemoveDiv" class="editorInputFloat editorInputButton">
            <a href="javascript:void(0);" title="Remove $strSponsorInput $strAwardInput." style="border-bottom: 0px" onClick="removeInputViaMootools('/projecteditor/removesponsor?format=ajax', '$p_strPrefix', $iIndex, 'sponsorPicked');"><img src="/components/com_projecteditor/images/icons/removeButton.png" border="0"/></a>
          </div>
          <div id="$strTypeDiv" class="editorInputFloat"></div>
          <div class="clear"></div>

ENDHTML;

    }

    return $strReturn;
  }

  public function deleteWebsitesByProject($p_iProjectId){
    ProjectHomepagePeer::deleteByProject($p_iProjectId);
  }

  public function deleteGrantsByProject($p_iProjectId){
    ProjectGrantPeer::deleteByProject($p_iProjectId);
  }

  public function deleteProjectOrganizationByProject($p_iProjectId){
    ProjectOrganizationPeer::deleteByProject($p_iProjectId);
  }

  /**
   * NEEScentral had a thumbnail table that isn't being used any more.
   * When changing project image, we'll want to clean up references to the
   * table.
   *
   * If a project image already exists during the NEEShub era, set the
   * usage type identifier to null.  The new uploaded file will have the
   * proper usage type identifier.
   * @param int $p_iProjectId
   * @param Person $p_oPerson
   */
  public function clearOldProjectImages($p_iProjectId, $p_oPerson){
    $this->clearCentralOldProjectThumbnail($p_iProjectId, $p_oPerson);
    $this->clearHubOldProjectThumbnail($p_iProjectId, $p_oPerson);

  }

  private function clearCentralOldProjectThumbnail($p_iProjectId, $p_oPerson){
    //get the current thumbnail
    $oCurrentThumbnail = ThumbnailPeer::findByEntityAndType($p_iProjectId, 1);
    if($oCurrentThumbnail){
      //get the current data_file id from thumbnail
      $oCurrentProjectImageDataFile = $oCurrentThumbnail->getDataFile();
      $iCurrentProjectImageDataFileId = $oCurrentProjectImageDataFile->getId();
      $strCurrentProjectImageDataFileName = $oCurrentProjectImageDataFile->getName();
      $strCurrentProjectImageDataFilePath = $oCurrentProjectImageDataFile->getPath();

      //delete old thumb (it's bigger than 90x60)
      $strThumb = "thumb_".$iCurrentProjectImageDataFileId."_".$strCurrentProjectImageDataFileName;
      $oThumbDataFile = DataFilePeer::findOneMatch($strThumb, $strCurrentProjectImageDataFilePath."/".Files::GENERATED_PICS);
      if($oThumbDataFile){
        $iThumbUsageTypeId = $oThumbDataFile->getUsageTypeId();
        $oThumbDataFile->delete();

        //make the icon the thumbnail
        $strIcon = "icon_".$iCurrentProjectImageDataFileId."_".$strCurrentProjectImageDataFileName;
        $oIconDataFile = DataFilePeer::findOneMatch($strIcon, $strCurrentProjectImageDataFilePath."/".Files::GENERATED_PICS);
        $oIconDataFile->setName($strThumb);
        $oIconDataFile->setUsageTypeId($iThumbUsageTypeId);
        $oIconDataFile->setModifiedById($p_oPerson->getId());
        $oIconDataFile->setModifiedDate(date("m/d/Y"));
        $oIconDataFile->setAppId(ProjectEditor::APP_ID);
        $oIconDataFile->save();


        //set usage_type of current project image equal to null
        $oCurrentProjectImageDataFile->setUsageTypeId(null);
        $oCurrentProjectImageDataFile->setModifiedById($p_oPerson->getId());
        $oCurrentProjectImageDataFile->setModifiedDate(date("m/d/Y"));
        $oCurrentProjectImageDataFile->setAppId(ProjectEditor::APP_ID);
        $oCurrentProjectImageDataFile->save();
      }else{

      }
    }
  }

  /**
   *
   * @param int $p_iProjectId
   * @param Person $p_oPerson
   */
  private function clearHubOldProjectThumbnail($p_iProjectId, $p_oPerson){
    //we should only find one file, but just in case...
    $oDataFileArray = DataFilePeer::findDataFileByUsage("Project Image", array(), 1, 100, $p_iProjectId);
    foreach($oDataFileArray as $oCurrentProjectImageDataFile){
      /* @var $oCurrentProjectImageDataFile DataFile */
      $oCurrentProjectImageDataFile->setUsageTypeId(null);
      $oCurrentProjectImageDataFile->setModifiedById($p_oPerson->getId());
      $oCurrentProjectImageDataFile->setModifiedDate(date("m/d/Y"));
      $oCurrentProjectImageDataFile->setAppId(ProjectEditor::APP_ID);
      $oCurrentProjectImageDataFile->save();
    }

  }
  /**
   * Gets the HTML for a collection of ProjectGrants.
   * @param array $p_oProjectGrantArray
   * @return string
   */
  public function getSponsorsHTML($p_oProjectGrantArray){
    $strReturn = "";

    /* @var $oProjectGrant ProjectGrant */
    foreach($p_oProjectGrantArray as $iIndex=>$oProjectGrant){
      $oProjectGrant = $oProjectGrant;
      $strSponsor = $oProjectGrant->getFundingOrg();
      $strAwardNumber = $oProjectGrant->getAwardNumber();
      $strUrl = $oProjectGrant->getAwardUrl();

      $strDisplay = $strSponsor;
      if(StringHelper::hasText($strAwardNumber)){
        $strDisplay .= " - " .$strAwardNumber;
        if(StringHelper::hasText($strUrl)){
          $strDisplay .= " (view)";
        }
      }

      if($iIndex < sizeof($p_oProjectGrantArray)-1){
        $strDisplay .= "<br>";
      }

      $strReturn .= $strDisplay;
    }
    return $strReturn;
  }

  /**
   * Gets the HTML of a Project's links.
   * @param array $p_oProjectHomepageURLArray
   * @return string
   */
  public function getWebsiteHTML($p_oProjectHomepageURLArray){
    $strReturn = "";

    /* @var $oProjectHomepage ProjectHomepageURL */

    foreach($p_oProjectHomepageURLArray as $iIndex=>$oProjectHomepage){
      $strUrl = $oProjectHomepage->getUrl();
      $strCaption = $oProjectHomepage->getCaption();
      $strBreak = "";
      if( $iIndex < sizeof($p_oProjectHomepageURLArray)-1 ){
        $strBreak = "<br>";
      }
      $strReturn .= <<< ENDHTML
                    $strCaption (view) $strBreak
ENDHTML;
    }
    return $strReturn;
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
          <input tabindex="13" type="text" value="$strTagValues" id="actags" name="tags" autocomplete="off" style="display:none;">
ENDHTML;
    }

    return $strReturn;
  }

  /**
   *
   * @param int $p_iProjectId
   * @param int $p_iTypeId
   * @return ProjectHomepage
   */
  public function getProjectHomepages($p_iProjectId, $p_iTypeId){
    return ProjectHomepagePeer::findByProjectIdAndFileTypeId($p_iProjectId, $p_iTypeId);
  }

  /**
   *
   * @param int $p_iProjectId
   * @param int $p_iDataFileId
   * @return ProjectHomepage
   */
  public function getProjectHomepageByDataFileId($p_iProjectId, $p_iDataFileId){
    return ProjectHomepagePeer::findByProjectIdAndDataFileId($p_iProjectId, $p_iDataFileId);
  }

  public function getNeesResearchTypes(){
    return NeesResearchTypePeer::findAll();
  }

}

?>