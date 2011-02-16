<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('project.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'api/org/nees/html/UserRequest.php';
require_once 'lib/data/ProjectOrganization.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/ResearcherKeyword.php';
require_once 'lib/data/Sponsor.php';
require_once 'lib/data/SponsorPeer.php';

class ProjectEditorModelConfirmProject extends ProjectEditorModelProject{
	

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
    return array($p_strName);
  }
  
  public function getSponsorList(){
  	
  }
  
  public function getWebsiteTupleValuesHTML($p_strFieldName){
    return UserRequest::getWebsiteTupleValuesHTML($p_strFieldName);
  }

  /**
   * Gets the HTML for ProjectOrganizations.
   * @param array $p_oOrganizationArray
   * @return string
   */
  public function getOrganizationsHTML($p_oOrganizationArray){
    $strReturn = "";

    /* @var $oOrganization Organization */
    foreach($p_oOrganizationArray as $iIndex=>$oOrganization){
      $strName = "";
      $iFacilityId = $oOrganization->getFacilityId();
      if($iFacilityId > 0){
        $strName = "<a href=\"index.php?option=com_sites&view=site&id=$iFacilityId\">".$oOrganization->getName()."</a>";
      }else{
        $strName = $oOrganization->getName();
      }
      $iId = $oOrganization->getId();
      $strComma = "";
      if($iIndex < sizeof($p_oOrganizationArray)-1){
        $strComma = ", ";
      }
      $strReturn .= <<< ENDHTML
                    <span>
                      $strName$strComma
                    </span>
                    <input type="hidden" name="organization[]" value="$iId"/>
ENDHTML;
    }

    return $strReturn;
  }

  /**
   * Gets the HTML for ProjectOrganizations.
   * @param array $p_oProjectOrganizationArray
   * @return string
   */
  public function getProjectOrganizationsHTML($p_oProjectOrganizationArray){
    $strReturn = "";

    //var_dump($p_oProjectOrganizationArray);

    /* @var $oProjectOrganization ProjectOrganization */
    foreach($p_oProjectOrganizationArray as $iIndex=>$oProjectOrganization){
      $strName = "";
      $iFacilityId = $oProjectOrganization->getOrganization()->getFacilityId();
      if($iFacilityId > 0){
        $strName = "<a href=\"index.php?option=com_sites&view=site&id=$iFacilityId\">".$oProjectOrganization->getOrganization()->getName()."</a>";
      }else{
        $strName = $oProjectOrganization->getOrganization()->getName();
      }
      $iId = $oProjectOrganization->getOrganization()->getId();
      $strComma = "";
      if($iIndex < sizeof($p_oProjectOrganizationArray)-1){
        $strComma = ", ";
      }
      $strReturn .= <<< ENDHTML
                    <span class="nobr">
                      $strName $strComma
                    </span>
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

  public function validateOrganizations($p_strOrganizationNameArray){
    //unset ($_SESSION[ProjectOrganizationPeer::TABLE_NAME]);
    unset ($_SESSION[OrganizationPeer::TABLE_NAME]);

    $oOrganizationArray = array();
    while (list ($key,$strOrganizationName) = @each ($p_strOrganizationNameArray)) {
      if(StringHelper::hasText($strOrganizationName)){
        $strOrganizationName = str_replace("&amp;", "&", $strOrganizationName);
        
        /* @var $oOrganization Organization */
        $oOrganization = $this->findOrganizationByName(trim($strOrganizationName));
        if($oOrganization){
          array_push($oOrganizationArray, $oOrganization);
        }else{
          throw new ValidationException("The organization '$strOrganizationName' is not found. As you type, we will suggest names.  Click on the desired organization.");
        }
      }
    }

    return $oOrganizationArray;
  }

  public function validateSponsors($p_strSponsorNameArray, $p_strAwardArray){
    unset ($_SESSION[ProjectGrantPeer::TABLE_NAME]);

    $iIndex = 0;
    $oSponsorArray = array();
    while (list ($key,$strSponsorName) = @each ($p_strSponsorNameArray)) {
      if(StringHelper::hasText($strSponsorName)){
        $strAward = null;
        if(isset($p_strAwardArray[$iIndex])){
          $strAward = $p_strAwardArray[$iIndex];
        }

        if($strSponsorName=="NSF" && $strAward=="Award Number"){
          //do nothing...
        }else{
          /* @var $oSponsor Sponsor */
          $oSponsor = SponsorPeer::findByName(trim($strSponsorName));
          array_push($oSponsorArray, $oSponsor); 
        }
      }
      ++$iIndex;
    }

    return $oSponsorArray;
  }

  
}

?>