<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
require_once 'lib/data/EquipmentPeer.php';
require_once 'lib/data/ProjectPeer.php';
require_once 'lib/data/Project.php';
require_once 'lib/data/Person.php';

class ProjectEditorViewPreview extends JView{
	
  function display($tpl = null){
    /*
     * 1. Get the current model
     */
    /* @var $oPreviewModel ProjectEditorModelPreview */
    $oPreviewModel =& $this->getModel();

    /*
     * 2. Initialize some variables to creation mode
     */
    $iProjectId = 0;
    $iProjectType = JRequest::getVar("type");
    $bEditProject = false;
    $iEntityViews = 0;
    $iEntityDownloads = 0;
    $iAccess = JRequest::getInt("access");
    $iNees = JRequest::getInt("nees");
    $strOwner = JRequest::getVar("owner");
    $strAdmin = JRequest::getVar("itperson");
    $strTagsRaw = JRequest::getVar("tags");
    
    
    /*
     * 3. Create the tabs for the view
     */
    $strTabArray = $oPreviewModel->getTabArray();
    $strTabViewArray = $oPreviewModel->getTabViewArray();
    $strOption = "warehouse/projecteditor/project/$iProjectId";
    $strTabHtml = $oPreviewModel->getTabs( $strOption, "", $strTabArray, $strTabViewArray, "" );
    if(!$iProjectId){
      /*
       * We're working with a new project.  Don't allow
       * users to click around until they save.
       */
      $strTabArray = $oPreviewModel->getCreateProjectTabArray();
      $strTabViewArray = $oPreviewModel->getCreateProjectTabViewArray();
      $strOption = "";
      $strTabHtml = $oPreviewModel->getOnClickTabs( $strTabArray, $strTabViewArray, "" );
    }
    $this->assignRef( "strTabs", $strTabHtml );

    $this->assignRef( "oUser", $oPreviewModel->getCurrentUser() );
    
    /*
     * 4. Check to see if we're actually editing a project
     */
    /* @var $oProject Project */
    $oProject = unserialize($_SESSION[ProjectPeer::TABLE_NAME]);
    if($oProject->getId() > 0){
      $iProjectId = $oProject->getId();

      $strPIs = $oPreviewModel->getMembersByRole($oPreviewModel, $oProject, 1, array("Principal Investigator", "Co-PI"));
      $strAdministrator = $oPreviewModel->getMembersByRole($oPreviewModel, $oProject, 1, array("IT Administrator"));

      $iEntityViews = $oPreviewModel->getEntityPageViews(1, $oProject->getId());
      $iEntityDownloads = $oPreviewModel->getEntityDownloads(1, $oProject->getId());
      $bEditProject = true;
    }else{
      $strOwnerUsername = $_SESSION[ProjectEditor::PROJECT_OWNER_USERNAME];
      $strAdminUsername = $_SESSION[ProjectEditor::PROJECT_ADMIN_USERNAME];

      /*@var $oOwnerPerson Person */
      $oOwnerPerson = $oPreviewModel->getOracleUserByUsername($strOwnerUsername);
      $strPIs = ucfirst($oOwnerPerson->getFirstName())." ".ucfirst($oOwnerPerson->getLastName());

      /*@var $oAdminPerson Person */
      $oAdminPerson = $oPreviewModel->getOracleUserByUsername($strAdminUsername);
      $strAdministrator = ucfirst($oAdminPerson->getFirstName())." ".ucfirst($oAdminPerson->getLastName());
    }
    $_SESSION[ProjectEditor::ACTIVE_PROJECT] = $iProjectId;

    $this->assignRef( "bEditProject", $bEditProject );
    $this->assignRef("iEntityActivityLogViews", $iEntityViews);
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);
    $this->assignRef( "iProjectType", $iProjectType );

    /*
     * 5. Setup the project metadata
     */
    $this->assignRef( "strPIs", $strPIs );
    $this->assignRef( "strAdministrator", $strAdministrator );
    $this->assignRef( "strTitle", $oProject->getTitle() );
    $this->assignRef( "strShortTitle", $oProject->getNickname() );
    $this->assignRef( "strDescription", $oProject->getDescription() );

    $strStartDate = $oProject->getStartDate();
    $strDates = strftime("%B %d, %Y", strtotime($strStartDate));
    $strEndDate = ($oProject->getEndDate()) ? $oProject->getEndDate() : "mm/dd/yyyy";
    if($strEndDate != "mm/dd/yyyy"){
      $strDates .= " - ".strftime("%B %d, %Y", strtotime($strEndDate));
    }else{
      $strDates .= " - Present";
    }
    $this->assignRef("strDates", $strDates);
    $this->assignRef("strStartDate", $strStartDate);
    $this->assignRef("strEndDate", $strEndDate);

    /*
     * 6. Setup the organization information
     */
    $oOrganizationArray = unserialize($_SESSION[OrganizationPeer::TABLE_NAME]);
    $strOrganizations = $oPreviewModel->getOrganizationsHTML($oOrganizationArray);
    $this->assignRef( "strOrganization", $strOrganizations );

    /*
     * 7. Setup the grant information
     */
    $_REQUEST["hasSponsor"] = true;
    $oSponsorArray = unserialize($_SESSION[ProjectGrantPeer::TABLE_NAME]);
    if(empty($oSponsorArray)){
      $_REQUEST["hasSponsor"] = false;
    }
    $strSponsor = $oPreviewModel->getSponsorsHTML($oSponsorArray);
    $this->assignRef( "strSponsor", $strSponsor );

    /*
     * 8. Setup the websites
     */
    $oWebsiteArray = unserialize($_SESSION[ProjectHomepagePeer::TABLE_NAME]);
    $strWebsites = $oPreviewModel->getWebsiteHTML($oWebsiteArray);
    $this->assignRef( "strWebsite", $strWebsites );

    /*
     * 9. Setup any tags provided
     */
    $oResearcherKeywordArray = unserialize($_SESSION[ResearcherKeywordPeer::TABLE_NAME]);
    $strTags = $oPreviewModel->getResearcherKeywordsHTML($oResearcherKeywordArray);
    $this->assignRef( "strTags", $strTags );

    /*
     * 10. New image (if available)
     */
    $bHasPhoto = false;
    $strPhotoPath = ProjectEditor::DEFAULT_PROJECT_IMAGE;
    $strPhotoCaption = StringHelper::EMPTY_STRING;

    if(isset($_SESSION[ProjectEditor::PHOTO_NAME])){
      $oHubUser =& JFactory::getUser();

      $strPhotoName = $_SESSION[ProjectEditor::PHOTO_NAME];
      $strPhotoPath = ProjectEditor::PROJECT_UPLOAD_DIR_WEB."/".$oHubUser->username."/".$strPhotoName;

      $strPhotoCaption = $_SESSION[ProjectEditor::PHOTO_CAPTION];
      $bHasPhoto = true;
    }else{
      /* @var $oProjectImageDataFile DataFile */
      $oProjectImageDataFile = $oPreviewModel->getProjectImage($iProjectId);
      if($oProjectImageDataFile){
        $bHasPhoto = true;
        $strPhotoPath = $oProjectImageDataFile->getGeneratedPic("thumb", Files::GENERATED_PICS);
        $strPhotoCaption = $oProjectImageDataFile->getDescription();
      }
    }
    $this->assignRef( 'strProjectImage', $strPhotoPath);
    $this->assignRef( 'strProjectCaption', $strPhotoCaption);
    $this->assignRef( 'bHasPhoto', $bHasPhoto );
    $this->assignRef( 'iProjectId', $iProjectId );
    $this->assignRef( 'strITperson', $strAdministrator );
    $this->assignRef( 'iAccess', $iAccess );
    $this->assignRef( 'iNees', $iNees );
    $this->assignRef( 'strOwner', $strOwner );
    $this->assignRef( 'strAdmin', $strAdmin );
    $this->assignRef( 'strTagsRaw', $strTagsRaw );


    if($iProjectId){
      JFactory::getApplication()->getPathway()->addItem($oProject->getName(), "/warehouse/projecteditor/project/".$oProject->getId());
    }
    JFactory::getApplication()->getPathway()->addItem("Preview Project","javascript:void(0)");
    parent::display($tpl);
  }
}
?>
