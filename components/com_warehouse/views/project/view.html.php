<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');
ximport('xprofile');

require_once 'lib/security/Authorizer.php';
require_once 'lib/data/ProjectGrant.php';
require_once 'lib/data/ProjectGrantPeer.php';

class WarehouseViewProject extends JView{

  function display($tpl = null){
    $iProjectId = JRequest::getVar("id");
    $oProject = ProjectPeer::find($iProjectId);
    if(!$oProject){
       echo ComponentHtml::showError("Unable to process request.  Project ($iProjectId) not found.");
       return;
    }

    $strProjectCreated = "";
    if(isset($_REQUEST["created"])){
      $strGroupCn = str_replace("-",  "_",  $oProject->getName());
      $strGroupCn = strtolower(trim($strGroupCn));
      $strProjectCreated = "Your new project has a NEEShub group.  Click <a href='/collaborate/groups/$strGroupCn'>here</a> for more information.";
    }
    $this->assignRef("projectCreated", $strProjectCreated);

    $strDisplayDescription = $this->getDisplayDescription($oProject);
    $oProject->setDescription($strDisplayDescription);

    $_REQUEST[Search::SELECTED] = serialize($oProject);

    /* @var $oProjectModel WarehouseModelProject */
    $oProjectModel =& $this->getModel();
    $strPIs = $this->getPIandCoPIs($oProjectModel, $oProject, 1, array("Principal Investigator", "Co-PI"));
    $this->assignRef( "strPIandCoPIs", $strPIs );
    $this->assignRef( "strDates", $this->getDates($oProject) );
    $this->assignRef( "strFundingOrg", $this->getFunding($oProject) );

    $_REQUEST[OrganizationPeer::TABLE_NAME] = serialize($this->getOrganizations($oProject));
    $_REQUEST[ProjectHomepagePeer::URL] = serialize($this->getProjectLinks($oProject));

    //get the tabs to display on the page
    $strTabArray = $oProjectModel->getTabArray();
    $strTabViewArray = $oProjectModel->getTabViewArray();
    $strTabHtml = $oProjectModel->getTabs( "warehouse", $iProjectId, $strTabArray, $strTabViewArray, "project" );
    $this->assignRef( "strTabs", $strTabHtml );

    //initialize NEEScentral thumbnail to empty string
    $strProjectThumbnail = StringHelper::EMPTY_STRING;
    $this->assignRef("strProjectThumbnail", $strProjectThumbnail);

    /* @var $oProjectImageDataFile DataFile */
    $oProjectImageDataFile = $oProjectModel->getProjectImage($iProjectId);

    //temporarily store the datafile as a request for the plugin
    $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oProjectImageDataFile);

    if($oProjectImageDataFile){
      //scale the image if thumbnail and display don't exist.  return the picture for the view
      $oProjectImageDataFile = $oProjectModel->scaleImageByWidth($oProjectImageDataFile, true, false);

      //update the datafile for the view
      $_REQUEST[DataFilePeer::TABLE_NAME] = serialize($oProjectImageDataFile);
    }else{
      //if $oProjectImageDataFile is null, try the NEEScentral method.
      $strProjectThumbnail = $oProject->getProjectThumbnailHTML("thumb");
      $this->assignRef("strProjectThumbnail", $strProjectThumbnail);
    }

    $oFacilityOrganizationArray = $oProjectModel->findProjectFacility($iProjectId);
    $_REQUEST["oFacility"] = serialize($oFacilityOrganizationArray);

    //get the list of equipment for the project
    $oEquipmentArray = $oProjectModel->findProjectEquipment($iProjectId);
    $_REQUEST[EquipmentPeer::TABLE_NAME] = serialize($oEquipmentArray);

    $oUser =& JFactory::getUser();
    $oPublicationArray = $oProjectModel->findProjectPublications($oUser->id, $oProject->getName(), 3);
    $this->assignRef( "publications", $oPublicationArray );

    $iPublicationCount = $oProjectModel->findProjectPublicationCount($oUser->id, $oProject->getName(), 3);
    $this->assignRef( "publicationCount", $iPublicationCount );

    //find publications without authors
    if($iPublicationCount < 4){
      $oProjectHomepageArray = $oProjectModel->getProjectHomepages($iProjectId, ProjectHomepagePeer::CLASSKEY_PROJECTHOMEPAGEPUB);
      $_REQUEST[ProjectHomepagePeer::TABLE_NAME] = serialize($oProjectHomepageArray);
    }

    $oToolArray = $oProjectModel->findTools($oProject->getId());
    $this->assignRef( "tools", $oToolArray );

    //TODO: for new docs module
    $strCurrentPath = "/nees/home/".$oProject->getName().".groups";
    JRequest::setVar('path', $strCurrentPath);

    //removed tree from display as of NEEScore meeting on 4/8/10
    $this->assignRef( "mod_curationprogress", ComponentHtml::getModule("mod_curationprogress") );
    //$this->assignRef( "mod_warehousedocs", ComponentHtml::getModule("mod_warehousedocs") );
    $this->assignRef( "mod_warehousetags", ComponentHtml::getModule("mod_warehousetags") );

    /* @var $oHubUser JUser */
    $oHubUser = $oProjectModel->getCurrentUser();
    $this->assignRef( "strUsername", $oHubUser->username );

    $iPhotoFileCount = $oProjectModel->findDataFileByMimeTypeCount($iProjectId, 0);
    $this->assignRef( "photoCount", $iPhotoFileCount );

    $iDocumentFileCount = count($oProjectModel->findDataFileByDirPath($oProject->getPathname()."/Documentation"));
    $this->assignRef( "iDocumentCount", $iDocumentFileCount );

    $iAnalysisFileCount = count($oProjectModel->findDataFileByDirPath($oProject->getPathname()."/Analysis"));
    $this->assignRef( "iAnalysisCount", $iAnalysisFileCount );

    // update and get the page views
    $iEntityViews = $oProjectModel->getPageViews(1, $oProject->getId());
    $this->assignRef("iEntityActivityLogViews", $iEntityViews);

    // update and get the page views
    $iEntityDownloads = $oProjectModel->getEntityDownloads(1, $oProject->getId());
    $this->assignRef("iEntityActivityLogDownloads", $iEntityDownloads);

    $bSearch = false;
    if(isset($_SESSION[Search::KEYWORDS])){
      if(StringHelper::hasText($_SESSION[Search::KEYWORDS])){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::FUNDING_TYPE])){
      if(StringHelper::hasText($_SESSION[Search::FUNDING_TYPE])){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::MEMBER])){
      if(StringHelper::hasText($_SESSION[Search::MEMBER])){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::NEES_SITE])){
      if($_SESSION[Search::NEES_SITE]){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::PROJECT_TYPE])){
      if($_SESSION[Search::PROJECT_TYPE]){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::PROJECT_IDS])){
      if(StringHelper::hasText($_SESSION[Search::PROJECT_IDS])){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::AWARDS])){
      if(StringHelper::hasText($_SESSION[Search::AWARDS])){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::MATERIAL_TYPES])){
      if(StringHelper::hasText(Search::MATERIAL_TYPES)){
        $bSearch = true;
      }
    }
    if(isset($_SESSION[Search::PROJECT_YEAR])){
      if($_SESSION[Search::PROJECT_YEAR]){
        $bSearch = true;
      }
    }
    //if(isset($_SESSION[Search::START_DATE]))$bSearch = true;
    //if(isset($_SESSION[Search::END_DATE]))$bSearch = true;

    //set the breadcrumbs
    JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
    if($bSearch){
      JFactory::getApplication()->getPathway()->addItem("Results","/warehouse/find?keywords=".$_SESSION[Search::KEYWORDS]
                                                                                            . "&funding=".$_SESSION[Search::FUNDING_TYPE]
                                                                                            . "&member=".$_SESSION[Search::MEMBER]
                                                                                            . "&neesSite=".$_SESSION[Search::NEES_SITE]
                                                                                            . "&projectType=".$_SESSION[Search::PROJECT_TYPE]
                                                                                            . "&projid=".$_SESSION[Search::PROJECT_IDS]
                                                                                            . "&award=".$_SESSION[Search::AWARDS]
                                                                                            . "&materialType=".$_SESSION[Search::MATERIAL_TYPES]
                                                                                            . "&projectYear=".$_SESSION[Search::PROJECT_YEAR]);
//                                                                                            . "&startdate=".$_SESSION[Search::START_DATE]
//                                                                                            . "&startdate=".$_SESSION[Search::END_DATE]);
    }
    JFactory::getApplication()->getPathway()->addItem($oProject->getName(),"javascript:void(0)");


//    $this->setLayout("unstructured");
//    echo $this->getLayout();

    parent::display($tpl);
  }//end display

  /**
   * Gets the list of PI and Co-PIs.
   * @return comma seperated string of names
   */
  private function getPIandCoPIs($p_oProjectModel, $p_oProject, $p_iEntityId, $p_strRoleArray){
    $oPersonResultSet = PersonPeer::findMembersByRoleForEntity($p_oProject->getId(), $p_iEntityId, $p_strRoleArray);
    $strPIs = "";
    while($oPersonResultSet->next()) {
//	  print "Last login: " . $rs->getTimestamp(2, "m/d/y H:i:s");

      $strFirstName = ucfirst($oPersonResultSet->getString('FIRST_NAME'));
      $strLastName = ucfirst($oPersonResultSet->getString('LAST_NAME'));
      $strUsername = $oPersonResultSet->getString('USER_NAME');

      //use the oracle username to find the hub (mysql) user.
      $oHubUser = $p_oProjectModel->getMysqlUserByUsername($strUsername);

      //see if user exists
      if($oHubUser){
        $profile = new XProfile();
        $profile->load( $oHubUser->id );

        //if the user's profile is public, provide link
        if($profile->get('public') == 1){
          $strPIs .= <<< ENDHTML
          <a href="/members/$oHubUser->id">$strFirstName $strLastName</a>,
ENDHTML;
        }else{
          //the user's profile isn't public.  list name
          $strPIs .= $strFirstName ." ". $strLastName .", ";
        }
      }else{
            //user hasn't registered in hub.
            $strPIs .= $strFirstName ." ". $strLastName .", ";
      }
    }

    //remove the last comma
    $iIndexLastComma = strrpos($strPIs, ",");
    $strPIs = substr($strPIs, 0, $iIndexLastComma);
    return $strPIs;
  }

  /**
   * Gets the project dates
   * @return start and end date
   */
  private function getDates($p_oProject){
  	//if no start date, return empty string
    $strDates = trim($p_oProject->getStartDate());
    if(strlen($strDates) == 0){
      return $strDates;
    }

    //if we have start but no end date, enter Present
    if(strlen($p_oProject->getEndDate())>0){
      $strDates = strftime("%B %d, %Y", strtotime($strDates)) . " - ". strftime("%B %d, %Y", strtotime($p_oProject->getEndDate()));
      //$strDates = $strDates . " to ". $p_oProject->getEndDate();
    }else{
      //$strDates = $strDates . " to Present";
      $strDates = strftime("%B %d, %Y", strtotime($strDates)) . " to Present";
    }
    return $strDates;
  }

  /**
   * Gets the list of organizations for the project
   * @return array of organization names
   */
  private function getOrganizations($p_oProject){
  	$oOrganizationArray = OrganizationPeer::findByProject($p_oProject->getId());
  	if(empty($oOrganizationArray)){
  	  return array();
  	}
  	return $oOrganizationArray;
  }

  /**
   * Gets the list of organizations for the project
   * @return array of organization names
   */
  private function getFacility($p_oProject){
  	return OrganizationPeer::findProjectFacility($p_oProject->getId());
  }

  /**
   *
   *
   */
  private function getProjectLinks($p_oProject){
  	return ProjectHomepagePeer::findProjectURLsByProjectId($p_oProject->getId());
  }

  /**
   * This is in progress, so we will take a 2 step approach.
   * a) If the project has an award number, go to the project_grant table.
   * b) If not (a), get the information from the project.  We won't provide link.
   *
   * @param Project $p_oProject
   * @return string
   */
  private function getFunding($p_oProject){
      $strFundingOrg = StringHelper::EMPTY_STRING;
      $oProjectGrantArray = ProjectGrantPeer::findByProjectId($p_oProject->getId());
      foreach($oProjectGrantArray as $iIndex=>$oProjectGrant){
        /* @var $oProjectGrant ProjectGrant */
        $strSponsor = $oProjectGrant->getFundingOrg();
        $strAwardNumber = $oProjectGrant->getAwardNumber();
        $strUrl = $oProjectGrant->getAwardUrl();

        $strFundingOrg .= $strSponsor . " - " .$strAwardNumber;
        if($strSponsor=="NSF" && StringHelper::hasText($strUrl)){
          $strFundingOrg .= " (<a href='".$strUrl."'>view</a>)";
        }

        if($iIndex < sizeof($oProjectGrantArray)-1){
          $strFundingOrg .= "<br>";
        }
      }

      if(strlen($strFundingOrg) === 0){
        $strFundingOrg = $p_oProject->getFundorg();
  	if(strlen($p_oProject->getFundorgProjId())>0){
  	  $strFundingOrg = $strFundingOrg . " - " .$p_oProject->getFundorgProjId();
//  	  if($p_oProject->getFundorg()=="NSF"){
//  	  	$strFundingOrg .= " (<a href='http://www.nsf.gov/awardsearch/showAward.do?AwardNumber=".$p_oProject->getFundorgProjId()."'>view</a>)";
//  	  }
  	}
      }
      return $strFundingOrg;
  }

  private function getDisplayDescription($p_oProject){
    $oDescriptionClob = nl2br($p_oProject->getDescription());
    $strReturnDescription = "";
    if(strlen($oDescriptionClob) > 300){
      $strShortDescription = StringHelper::neat_trim($oDescriptionClob, 250);
      $strReturnDescription = <<< ENDHTML
              <div id="ProjectShortDescription">
                $strShortDescription (<a href="javascript:void(0);" onClick="document.getElementById('ProjectLongDescription').style.display='';document.getElementById('ProjectShortDescription').style.display='none';">more</a>)
              </div>
              <div id="ProjectLongDescription" style="display:none">
                $oDescriptionClob (<a href="javascript:void(0);" onClick="document.getElementById('ProjectLongDescription').style.display='none';document.getElementById('ProjectShortDescription').style.display='';">hide</a>)
              </div>
ENDHTML;
    }else{
      $strReturnDescription = $oDescriptionClob;
    }
    return $strReturnDescription;
  }

}

?>