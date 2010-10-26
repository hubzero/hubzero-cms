<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/Person.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/data/OrganizationPeer.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/Facility.php';
require_once 'lib/data/EntityType.php';
require_once 'lib/data/EntityTypePeer.php';
require_once 'api/org/nees/static/Files.php';
require_once 'api/org/nees/static/ProjectEditor.php';
require_once 'api/org/nees/static/Search.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';
require_once 'lib/data/curation/NCCuratedObjects.php';
require_once 'lib/data/curation/NCCuratedObjectsPeer.php';

class ProjectEditorViewProject extends JView{
	
  function display($tpl = null){
    /* @var $oProjectModel ProjectEditorModelProject */
    $oProjectModel =& $this->getModel();

    $bClearSession = true;

    /*
     * If iContinueEdit equals 1, don't clear session.
     */
    $iContinueEdit = JRequest::getInt("continue", 0);
    if($iContinueEdit==1){
      $bClearSession = false;
    }

    /*
     * If we have errors in the request, check the size of array.
     * If the array is not empty, don't clear session.
     */
    if(isset($_REQUEST["ERRORS"])){
      $strErrorArray = $_REQUEST["ERRORS"];
      if(!empty($strErrorArray)){
        $bClearSession = false;
      }
    }

    /*
     * If true, go ahead and clear session.
     */
    if($bClearSession){
      $oProjectModel->clearSession();
    }

    $iProjectId = JRequest::getVar('projid', 0);
    $this->assignRef( "iProjectId", $iProjectId );
    
    //get the tabs to display on the page
    $strTabArray = $oProjectModel->getTabArray();
    $strTabViewArray = $oProjectModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oProjectModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "" );
    if(!$iProjectId){
      /*
       * We're working with a new project.  Don't allow
       * users to click around until they save.
       */
      $strTabArray = $oProjectModel->getCreateProjectTabArray();
      $strTabViewArray = $oProjectModel->getCreateProjectTabViewArray();
      $strOption = "";
      $strTabHtml = $oProjectModel->getOnClickTabs( $strTabArray, $strTabViewArray, "" );
    }
    $this->assignRef( "strTabs", $strTabHtml );

    //get the sub tabs to display on the page
    $strSubTab = JRequest::getVar('subtab', 'about');

    $strSubTabArray = $oProjectModel->getProjectSubTabArray();
    $strSubTabHtml = $oProjectModel->getSubTabs( "/warehouse/projecteditor/project/$iProjectId", "", $strSubTabArray, $strSubTab );
    if(!$iProjectId){
      $strSubTabHtml = $oProjectModel->getOnClickSubTabs( ProjectEditor::CREATE_PROJECT_SUBTAB_ALERT, $strSubTabArray, $strSubTab );
    }
    $this->assignRef( "strSubTabs", $strSubTabHtml );

    /* @var $oHubUser JUser */
    $oHubUser = $oProjectModel->getCurrentUser();
    $this->assignRef( "oUser", $oHubUser );

    /* @var $oPerson Person */
    $oPerson = $oProjectModel->getOracleUserByUsername($oHubUser->username);
    if(!$oPerson){
      echo "<p class='error'>You must be logged in to edit projects.</p>";
      return;
    }

    /*
     * Create a temporary upload space for the user if it's
     * not available.
     */
    $oProjectModel->createUploadDirectory($oHubUser);

    //set form fields
    $strBreadCrumbs = "Create Project";
    $strPIs = JRequest::getVar("owner", StringHelper::EMPTY_STRING);
    if(!StringHelper::hasText($strPIs)){
      $strPIs = ProjectEditor::DEFAULT_NAME;
    }
    $strAdministrator = JRequest::getVar("itperson", ucfirst($oPerson->getLastName()).", ".ucfirst($oPerson->getFirstName())." (".$oPerson->getUserName().")");
    $strTitle = JRequest::getVar("title", StringHelper::EMPTY_STRING);
    $strShortTitle = JRequest::getVar("shortTitle", StringHelper::EMPTY_STRING);
    $strStartDate = JRequest::getVar("startdate", "");
    if(!StringHelper::hasText($strStartDate)){
      $strStartDate = date("m/d/Y");
    }
    $strEndDate = JRequest::getVar("enddate", "mm/dd/yyyy");
    $strDescription = JRequest::getVar("description", StringHelper::EMPTY_STRING);
    $iAccess = JRequest::getInt("access", 4);
    $strTags = JRequest::getVar("tags", StringHelper::EMPTY_STRING);
    $strSpecimenType = StringHelper::EMPTY_STRING;
    $strOrganization = StringHelper::EMPTY_STRING;
    $strOrganizationPicked = StringHelper::EMPTY_STRING;
    $strSponsor = ProjectEditor::DEFAULT_SPONSOR;
    $strSponsorPicked = StringHelper::EMPTY_STRING;
    $strAward = ProjectEditor::DEFAULT_AWARD_NUMBER;
    $strWebsite = ProjectEditor::DEFAULT_WEBSITE_TITLE;
    $strWebsitePicked = StringHelper::EMPTY_STRING;
    $strUrl = "https://".$_SERVER['SERVER_NAME'].ProjectEditor::DEFAULT_PROJECT_URL;
    $iNees = JRequest::getInt("nees", 1);
    $strProjectImage = ProjectEditor::DEFAULT_PROJECT_IMAGE;
    $strProjectImageCaption = ProjectEditor::DEFAULT_PROJECT_CAPTION;
    $bHasPhoto = false;
    $bEditProject = false;
    $iEntityViews = 0;
    $iEntityDownloads = 0;
    $bCanCurate = false;
    $iProjectTypeId = ProjectPeer::CLASSKEY_STRUCTUREDPROJECT;

    //check to see if tags are passed in by form
    if(StringHelper::hasText($strTags)){
      $strTags = $oProjectModel->getTagsInputHTML(explode(",", $strTags));
    }

    //check to see if orgs are passed in by form
    if(isset($_SESSION[OrganizationPeer::TABLE_NAME])){
      $oOrganizationArray = unserialize($_SESSION[OrganizationPeer::TABLE_NAME]);
      //$strOrganizationPicked = $oProjectModel->getEntityListHTML("organization", $oOrganizationArray);
      $strOrganizationPicked = $oProjectModel->getOrganizationListHTML("organization", $oOrganizationArray);
    }

    if(isset($_SESSION[ProjectGrantPeer::TABLE_NAME])){
      $oSponsorArray = unserialize($_SESSION[ProjectGrantPeer::TABLE_NAME]);
      $strSponsorPicked = $oProjectModel->getSponsorsHTML($oSponsorArray);
    }

    if(isset($_SESSION[ProjectHomepagePeer::TABLE_NAME])){
      $oWebsiteArray = unserialize($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
      $strWebsitePicked = $oProjectModel->getProjectLinksHtml("website", $oWebsiteArray);
    }

    $oProject = null;
    if($iProjectId > 0){
      $_SESSION[ProjectEditor::ACTIVE_PROJECT] = $iProjectId;
      
      $strBreadCrumbs = "Edit Project";
      $bEditProject = true;

      /* @var $oProject Project */
      $oProject = $oProjectModel->getProjectById($iProjectId);
      if(!$oProject){
        echo ComponentHtml::showError(ProjectEditor::PROJECT_ERROR_MESSAGE);
        return;
      }

      $iProjectTypeId = $oProject->getProjectTypeId();

      $oAuthorizer = Authorizer::getInstance();
      $bCanCurate = $oAuthorizer->canCurate();

      if(!$oAuthorizer->canCurate()){
        if(!$oAuthorizer->canEdit($oProject)){
          echo ComponentHtml::showError(ProjectEditor::AUTHORIZER_PROJECT_EDIT_ERROR);
          return;
        }
      }
      

      $strTitle = $oProject->getTitle();
      $strShortTitle = $oProject->getNickname();
      $strStartDate = $oProject->getStartDate();
      $strEndDate = $oProject->getEndDate();
      $strDescription = $oProject->getDescription();

      $strPIs = $oProjectModel->getMembersByRole($oProjectModel, $oProject, 1, array("Principal Investigator", "Co-PI"), true);
      $strAdministrator = $oProjectModel->getMembersByRole($oProjectModel, $oProject, 1, array("IT Administrator"), true);

      /* @var $oProjectImageDataFile DataFile */
      $oProjectImageDataFile = $oProjectModel->getProjectImage($iProjectId);
      if($oProjectImageDataFile){
        $bHasPhoto = true;
        $strProjectImage = $oProjectImageDataFile->getGeneratedPic("thumb", Files::GENERATED_PICS);
        $strProjectImageCaption = $oProjectImageDataFile->getDescription();
      }

      //only get the orgs if the user hasn't submitted the form
      if(!StringHelper::hasText($strOrganizationPicked)){
        $oOrganizationArray = $this->getOrganizations($oProject);
        //$strOrganizationPicked = $oProjectModel->getEntityListHTML("organization", $oOrganizationArray);
        $strOrganizationPicked = $oProjectModel->getOrganizationListHTML("organization", $oOrganizationArray);
        //$oProjectModel->setSessionOrganizations($oOrganizationArray);
        $_SESSION[OrganizationPeer::TABLE_NAME] = serialize($oOrganizationArray);
      }

      if(!StringHelper::hasText($strWebsitePicked)){
        $oProjectHomepageArray = $oProjectModel->getProjectLinks($oProject);
        $strWebsitePicked = $oProjectModel->getProjectLinksHtml("website", $oProjectHomepageArray);
        //$oProjectModel->setSessionWebsites($oProjectHomepageArray);
        $_SESSION[ProjectHomepagePeer::TABLE_NAME] = serialize($oProjectHomepageArray);
      }

      if(!StringHelper::hasText($strSponsorPicked)){
        $oProjectGrantArray = $oProjectModel->getProjectGrants($oProject);
        $strSponsorPicked = $oProjectModel->getProjectGrantHTML("sponsor", $oProjectGrantArray);
        //$oProjectModel->setSessionProjectGrants($oProjectGrantArray);
        $_SESSION[ProjectGrantPeer::TABLE_NAME] = serialize($oProjectGrantArray);
      }

      if(!StringHelper::hasText($strTags)){
        $oResearcherKeywordArray = $oProjectModel->getResearcherKeywordsByEntity($iProjectId, 1);
        if(!empty ($oResearcherKeywordArray)){
          $strTags = $oProjectModel->getResearcherKeywordsInputHTML($oResearcherKeywordArray);
        }
      }

      $strProjectView = $oProject->getView();
      switch ($strProjectView) {
          case "MEMBERS":
              $iAccess = 4;
              break;
          case "USERS":
              $iAccess = 3;
              break;
          default:
              $iAccess = 0;
              break;
      }

      $iNees = $oProject->getNEES();

      $iEntityViews = $oProjectModel->getEntityPageViews(1, $oProject->getId());
      $iEntityDownloads = $oProjectModel->getEntityDownloads(1, $oProject->getId());
    }

    $oEntityType = EntityTypePeer::findByTableName(ProjectEditor::PROJECT_IMAGE);

    $this->assignRef( "iUsageTypeId", $oEntityType->getId() );
    $this->assignRef( "bEditProject", $bEditProject );
    $this->assignRef( "strPIs", $strPIs );
    $this->assignRef( "strAdministrator", $strAdministrator );
    $this->assignRef( "strTitle", $strTitle );
    $this->assignRef( "strShortTitle", $strShortTitle );
    $this->assignRef( "strStartDate", $strStartDate );
    $this->assignRef( "strEndDate", $strEndDate );
    $this->assignRef( "strUrl", $strUrl );
    $this->assignRef( "strDescription", $strDescription );
    $this->assignRef( "iAccess", $iAccess );
    $this->assignRef( "iNees", $iNees );
    $this->assignRef( "strTags", $strTags );
    $this->assignRef( "iProjectTypeId", $iProjectTypeId );

    $this->assignRef( "strOrganization", $strOrganization );
    $this->assignRef( "strOrganizationPicked", $strOrganizationPicked);

    $this->assignRef( "strSponsor", $strSponsor );
    $this->assignRef( "strSponsorPicked", $strSponsorPicked);

    $this->assignRef( "strAward", $strAward );

    $this->assignRef( "strWebsite", $strWebsite );
    $this->assignRef( "strWebsitePicked", $strWebsitePicked);

    $this->assignRef( "strProjectImage", $strProjectImage );
    $this->assignRef( "strProjectImageCaption", $strProjectImageCaption );
    $this->assignRef( "bHasPhoto", $bHasPhoto );

    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    if($oProject){
      $_REQUEST[Search::SELECTED] = serialize($oProject);
      $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );

      JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"javascript:void(0)");
    }else{
      $strBlank = StringHelper::EMPTY_STRING;
      $this->assignRef( "mod_curationprogress", $strBlank );
    }

    if($bCanCurate){
      //$this->setLayout(ProjectEditor::CURATE_LAYOUT);
      //$this->getCurationInformation($oProjectModel, $oProject->getId(), $oProject->getCreatorId());
    }

    parent::display($tpl);
  }

  /**
   * Gets the list of organizations for the project
   * @return array of organization names
   */
  private function getOrganizations($p_oProject){
    return OrganizationPeer::findByProject($p_oProject->getId());
  }

  /**
   *
   * @param ProjectEditorModelProject $p_oModel
   */
  private function getCurationInformation($p_oModel, $p_iProjectId, $p_iCreatorId=0){
    $iObjectId = 0;
    $strBlank = StringHelper::EMPTY_STRING;
    $this->assignRef( "iObjectId", $iObjectId );
    $this->assignRef( "strCreationDate", $strBlank );
    $this->assignRef( "strCurationDate", $strBlank );
    $this->assignRef( "strCurationState", $strBlank );
    $this->assignRef( "strCurationStateArray", $p_oModel->getCurationStates() );
    $this->assignRef( "strConformanceLevel", $strBlank );
    $this->assignRef( "strConformanceLevelArray", $p_oModel->getConformanceLevels() );
    $this->assignRef( "strObjectStatus", $strBlank );
    $this->assignRef( "strObjectStatusArray", $p_oModel->getObjectStatus() );
    $this->assignRef( "strModifiedBy", $strBlank );
    $this->assignRef( "strModifiedDate", $strBlank );
    $this->assignRef( "strObjectType", $strBlank );
    $this->assignRef( "strCreatedBy", $strBlank );

    $oNCCuratedObjects = $p_oModel->getCuratedProject($p_iProjectId);
    if($oNCCuratedObjects){
      $iObjectId = $oNCCuratedObjects->getId();
      
      $oPerson = PersonPeer::find($p_iCreatorId);
      $strCreatedBy = ucfirst($oPerson->getLastName()).", ".ucfirst($oPerson->getFirstName())." (".$oPerson->getUserName().")";

      $strModifiedByUsername = $oNCCuratedObjects->getModifiedBy();
      $oModifiedByPerson = $p_oModel->getOracleUserByUsername($strModifiedByUsername);
      $strModifiedBy = ucfirst($oModifiedByPerson->getLastName()).", ".ucfirst($oModifiedByPerson->getFirstName())." (".$oModifiedByPerson->getUserName().")";

      $this->assignRef( "iObjectId", $iObjectId );
      $this->assignRef( "strCreatedBy", $strCreatedBy );
      $this->assignRef( "strCreationDate", $oNCCuratedObjects->getCreatedDate('%m/%d/%Y') );
      $this->assignRef( "strCurationDate", $oNCCuratedObjects->getInitialCurationDate('%m/%d/%Y') );
      $this->assignRef( "strCurationState", $oNCCuratedObjects->getCurationState() );
      $this->assignRef( "strModifiedBy", $strModifiedBy );
      $this->assignRef( "strModifiedDate", $oNCCuratedObjects->getModifiedDate('%m/%d/%Y') );
      $this->assignRef( "strObjectType", $oNCCuratedObjects->getObjectType() );
      $this->assignRef( "strConformanceLevel", $oNCCuratedObjects->getConformanceLevel() );
      $this->assignRef( "strObjectStatus", $oNCCuratedObjects->getObjectStatus() );
    }
  }
  
}
?>
