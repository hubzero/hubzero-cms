<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('base.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'lib/data/MaterialPeer.php';

class ProjectEditorModelSetup extends ProjectEditorModelBase {

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct() {
        parent::__construct();
    }

    public function getProjectOwner() {
        $oUser = & JFactory::getUser();
        return $oUser;
    }

    public function suggestFacilities($p_strName) {
        return OrganizationPeer::suggestFacilities($p_strName);
    }

    public function suggestSpecimen($p_strName) {
        return SpecimenPeer::suggestByName($p_strName);
    }

    public function findMaterialTypeByDisplayName($p_strName) {
        return MaterialTypePeer::findByDisplayName($p_strName);
    }

    /**
     * get available material types
     * @return array <MaterialType>
     */
    public function findMaterialTypes() {
        return MaterialTypePeer::findAll();
    }

    public function suggestMaterialType($p_strName) {
        return MaterialTypePeer::suggestDisplayName($p_strName);
    }

    public function getFilmstripHTML($p_strPhotoArray) {
        $strHTML = "<div class=\"sscontainer\">
		          <div id=\"showcase\">
		            <div id=\"showcase-prev\" class=\"\"></div>
		            <div id=\"showcase-window\">
		              <div class=\"showcase-pane\" style=\"left: 0px;\">";

        foreach ($p_strPhotoArray as $strPhoto) {
            $strHTML .= <<< ENDHTML
		            	<a title="RWN, drift levels of 0.2%" href="/components/com_warehouse/images/prototype/film_strip/RWN-Original/5-RWN.jpg" rel="lightbox[filmstrip]">
		              	  <img class="thumbima" alt="thumbnail1" src="/components/com_warehouse/images/prototype/film_strip/RWN-h75/5-RWN.jpg">
		            	</a>
ENDHTML;
        }
        $strHTML .= "      </div>
		              </div>
		            <div id=\"showcase-next\" class=\"\"></div>
		          </div>
		        </div>";

        return $strHTML;
    }

    public function createAuthorization($p_iCreatorId, $p_iProjectId) {
        $perms = new Permissions(Permissions::PERMISSION_ALL);

        $auth = new Authorization($p_iCreatorId, $p_iProjectId, DomainEntityType::ENTITY_TYPE_PROJECT, $perms);
        $auth->save();

        return $auth;
    }

    public function createPersonEntityRole($p_iCreatorId, $p_iProjectId, $p_iRoleId) {
        $oRole = RolePeer::find($p_iRoleId);

        $oPersonEntityRole = new PersonEntityRole($p_iCreatorId, $p_iProjectId, DomainEntityType::ENTITY_TYPE_PROJECT, $oRole);
        $oPersonEntityRole->save();

        return $oPersonEntityRole;
    }

    /**
     * Creates ProjectOrganization objects for the current project.
     * @param $p_oProject - The current project
     * @param $p_strOrgTextField - The organization name in the text input field
     * @param $p_strOrgArray - The name of organizations added with the (+) button
     */
    public function createProjectOrganizations($p_oProject, $p_strOrgTextField, $p_strOrgArray) {
        //nothing to do if both are empty
        if (strlen($p_strOrgTextField) === 0 && empty($p_strOrgArray)) {
            return;
        }

        //insert the text field
        if (strlen($p_strOrgTextField) > 0) {
            if (!$this->hasProjectOrganization($p_oProject, $p_strOrgTextField)) {
                $oOrganization = OrganizationPeer::findByName($p_strOrgTextField);
                $oProjectOrganization = new ProjectOrganization($p_oProject, $oOrganization);
                $oProjectOrganization->save();
            }
        }

        //insert the array
        if (!empty($p_strOrgArray)) {
            while (list ($key, $strOrgName) = @each($p_strOrgArray)) {
                if (!$this->hasProjectOrganization($p_oProject, $strOrgName)) {
                    $oOrganization = OrganizationPeer::findByName($strOrgName);
                    $oProjectOrganization = new ProjectOrganization($p_oProject, $oOrganization);
                    $oProjectOrganization->save();
                }
            }
        }
    }

    /**
     * Checks to see if a project already has a ProjectOrganization
     * @return true/false
     */
    private function hasProjectOrganization($p_oProject, $p_strNewOrgName) {
        $bReturn = false;

        $oExistingProjectOrganizationArray = OrganizationPeer::findByProject($p_oProject->getId());
        foreach ($oExistingProjectOrganizationArray as $oThisProjectOrganization) {
            if ($oThisProjectOrganization->getName() == $p_strNewOrgName) {
                $bReturn = true;
            }
        }
        return $bReturn;
    }

    public function createProjectEquipment() {

    }

    public function createProjectLinks($p_oProject, $p_iProjectHomePageTypeId, $p_strCaption, $p_strDescription, $p_strUrl, $p_strWebsiteArray=null) {
        if (strlen($p_strUrl) > 0) {
            $oProjectHomepage = new ProjectHomepage();
            $oProjectHomepage->setProjectHomepageTypeId($p_iProjectHomePageTypeId);
            $oProjectHomepage->setCaption($p_strCaption);
            $oProjectHomepage->setDescription($p_strDescription);


            if (preg_match('/neeshub.org/warehouse/project/[id]\b/', $p_strUrl)) {
                $p_strUrl = "https://neeshub.org/warehouse/project/" . $p_oProject->getId();
            }
            $oProjectHomepage->setUrl($p_strUrl);
            $oProjectHomepage->save();
        }

        if ($p_strWebsiteArray != null) {
            foreach ($p_strWebsiteArray as $strWebsiteUrl) {
                $strWebsiteArray = explode("^*", $strWebsiteUrl);
                $strText = $strWebsiteArray[0];
                $strUrl = $strWebsiteArray[1];

                $oProjectHomepage = new ProjectHomepage();
                $oProjectHomepage->setProjectHomepageTypeId($p_iProjectHomePageTypeId);
                $oProjectHomepage->setCaption($strText);
                $oProjectHomepage->setDescription($strText);

                if (preg_match('/neeshub.org/warehouse/project/[id]\b/', $strUrl)) {
                    $strUrl = "https://neeshub.org/warehouse/project/" . $p_oProject->getId();
                }
                $oProjectHomepage->setUrl($strUrl);
                $oProjectHomepage->save();
            }
        }
    }

  public function validateMaterial($p_strCategory, $p_strText){
    if(!StringHelper::hasText($p_strText)){
      throw new ValidationException($p_strCategory." is required.");
    }

    if ($p_strText=="Material name"){
      throw new ValidationException($p_strCategory." is required.");
    }
    return $p_strText;
  }

  public function validateMaterialType($p_strCategory, $p_iMaterialTypeId){
    if(!StringHelper::hasText($p_iMaterialTypeId)){
      throw new ValidationException($p_strCategory." is required.");
    }

    $oMaterialType = MaterialTypePeer::find($p_iMaterialTypeId);
    if(!$oMaterialType){
      throw new ValidationException($p_strCategory." is invalid.");
    }
    return $oMaterialType;
  }

  public function validateMaterialDescription($p_strCategory, $p_strText){
    if(!StringHelper::hasText($p_strText)){
      throw new ValidationException($p_strCategory." is required.");
    }

    if ($p_strText=="Material description"){
      throw new ValidationException($p_strCategory." is required.");
    }
    return $p_strText;
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
   * @param Experiment $p_oExperiment
   * @return array <Material> 
   */
  public function findMaterialsByExperiment($p_oExperiment){
    return MaterialPeer::findByExperiment($p_oExperiment->getId());
  }

  /**
   * Return an html table of materials for the current experiment.
   * @param $p_oMaterialArray - array of materials
   * @param $p_iProjectId - project identifier
   * @param $p_iExperimentId - experiment identifier
   * @param $p_iSelectedMaterialId - a selected material
   * @returns html string
   */
  public function findMaterialsByExperimentHTML($p_oMaterialArray, $p_iProjectId, $p_iExperimentId, $p_iSelectedMaterialId=0){
  	$strHTML = "<table style=\"width:70%\">
  				  <tr>
  				    <thead>
  				      <th style=\"width:16px\"></th>
  				      <th>Name</th>
  				      <th>Type</th>
  				    </thead>
  				  <tr>";

  	foreach($p_oMaterialArray as $iMaterialIndex=>$oMaterial){
  	  $iMaterialId = $oMaterial->getId();
  	  $strMaterialName = $oMaterial->getName();
  	  $strMaterialDesc = $oMaterial->getDescription();
  	  $strMaterialType = $oMaterial->getMaterialType()->getName();

  	  //get the properties table
  	  $strPropertiesHTML = $this->findMaterialPropertiesByExperimentHTML($oMaterial);

  	  //get the files table
  	  $strFilesHTML = $this->findMaterialFilesByExperimentHTML($oMaterial);

  	  //set the background color
  	  $strBgColor = "odd";
  	  if($iMaterialIndex %2 == 0){
  	    $strBgColor = "even";
  	  }

  	  if($iMaterialId==$p_iSelectedMaterialId){
  	    $strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId?format=ajax', 'experimentInfo');"><img border="0" width="16" height="16" alt="" title="Collapse" src="/components/com_warehouse/images/arrow_down.png" style="border: 0px none;"></a></td>
  	        <td>
  	          <a href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId?format=ajax', 'experimentInfo');">$strMaterialName</a>
  	          <p>$strMaterialDesc</p>
  	          <p><b>Properties:</b></p>
  	          $strPropertiesHTML
  	          <p><b>Files:</b></p>
  	          $strFilesHTML
  	        </td>
  	        <td>$strMaterialType</td>
  	      <tr>
ENDHTML;
  	  }else{
  	  	$strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId/detail/$iMaterialId?format=ajax', 'experimentInfo');"><img border="0" width="16" height="16" alt="" title="Expand" src="/components/com_warehouse/images/arrow_right.png" style="border: 0px none;"></a></td>
  	        <td><a href="javascript:void(0);" onClick="getMootools('/warehouse/materials/project/$p_iProjectId/experiment/$p_iExperimentId/detail/$iMaterialId?format=ajax', 'experimentInfo');">$strMaterialName</a></td>
  	        <td>$strMaterialType</td>
  	      <tr>
ENDHTML;
  	  }
  	}

    $strHTML .= "</table>";

    return $strHTML;
  }

  /**
   * Displays a table of properties for the given material object.
   * @param $p_oMaterial - current material object.
   * @returns html string
   */
  public function findMaterialPropertiesByExperimentHTML($p_oMaterial){
  	$strPropertiesHTML = "<table style=\"border-bottom:0px;border-top:0px;margin-left:30px;\">";

  	foreach($p_oMaterial->getMaterialProperties() as $oMaterialProperty){
  	  $strPropertyName = $oMaterialProperty->getMaterialTypeProperty()->getName();
  	  $strPropertyValue = $oMaterialProperty->getValue();
  	  $strUnitName = "";
  	  $strUnitAbbreviation = "";
  	  $strUnits = "";

  	  if( $oMaterialProperty->getUnit() ) {
        if( $oMaterialProperty->getUnit()->getCategory()->getName() == 'Time' ) {
          $strUnitName = $oMaterialProperty->getUnit()->getName();
        } else {
          $strUnitAbbreviation = $oMaterialProperty->getUnit()->getAbbreviation();
        }
      } else {
        $strUnits = $oMaterialProperty->getMaterialTypeProperty()->getUnits();
      }

  	  $strPropertiesHTML .= <<< ENDHTML
	    <tr>
  	      <td nowrap width="1">$strPropertyName:</td>
  	      <td>$strPropertyValue $strUnitName $strUnitAbbreviation $strUnits</td>
  	    <tr>
ENDHTML;
  	}

  	if(sizeof($p_oMaterial->getMaterialProperties())==0){
  	  $strPropertiesHTML .= "<tr><td>No properties listed.<td></tr>";
  	}
  	$strPropertiesHTML .= "</table>";

  	return $strPropertiesHTML;
  }

  /**
   * Displays a table of files for the given material object.
   * @param $p_oMaterial - current material object.
   * @returns html string
   */
  public function findMaterialFilesByExperimentHTML($p_oMaterial){
  	$strFilesHTML = "<table style=\"border-bottom:0px;border-top:0px;margin-left:30px;\">
  					  <tr><thead><th style='border-bottom:0px;'>Title or Name</th><th style='border-bottom:0px;'>Timestamp</th></thead></tr>";

  	foreach($p_oMaterial->getFiles() as $oMaterialFile){
  	  $oFileCreated = $oMaterialFile->getDataFile()->getCreated();
  	  $strFileCreated = strftime("%b %d, %Y - %H:%M", strtotime($oFileCreated));
  	  $strFileName = $oMaterialFile->getDataFile()->getName();
  	  $strFilePath = $oMaterialFile->getDataFile()->getPath();
  	  $strFileTitle = $oMaterialFile->getDataFile()->getTitle();
  	  $strFileDisplay = (strlen($strFileTitle) != 0) ? $strFileTitle : $strFileName;
  	  $strFileLink = $oMaterialFile->getDataFile()->getUrl();  //file to view...

  	  $strFilesHTML .= <<< ENDHTML
	    <tr>
  	      <td nowrap><a href="$strFileLink" target="data_file">$strFileDisplay</a></td>
  	      <td>$strFileCreated</td>
  	    <tr>
ENDHTML;
  	}

  	if(sizeof($p_oMaterial->getFiles())==0){
  	  $strFilesHTML .= "<tr><td>No files listed.<td></tr>";
  	}
  	$strFilesHTML .= "</table>";

  	return $strFilesHTML;
  }
}
?>