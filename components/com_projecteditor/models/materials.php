<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('base.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'lib/data/MaterialPeer.php';
require_once 'lib/data/MaterialProperty.php';

class ProjectEditorModelMaterials extends ProjectEditorModelBase {

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

  public function findMaterialsByExperimentHTML0($p_oMaterialArray, $p_iProjectId, $p_iExperimentId, $p_iSelectedMaterialId=0, $p_bCanDelete=false){
        $strHTML = "<table class=\"editorInputSizeFull\"  align=\"center\">
  				  <tr>
  				    <thead>
  				      <th style=\"width:16px\"></th>
  				      <th>Name</th>
  				      <th>Type</th>
                                      <th>Manage</th>
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

          $strDeleteLink = "";
          if($p_bCanDelete){
            $strDeleteLink = "[<a href='/warehouse/projecteditor/delete?format=ajax&eid=$iMaterialId&etid=134' class='modal'>Delete</a>]";
          }

  	  if($iMaterialId==$p_iSelectedMaterialId){
  	    $strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="javascript:void(0);" onClick="getMootools('/warehouse/projecteditor/materialslist?projectId=$p_iProjectId&experimentId=$p_iExperimentId&format=ajax', 'experimentInfo');"><img border="0" width="16" height="16" alt="" title="Collapse" src="/components/com_warehouse/images/arrow_down.png" style="border: 0px none;"></a></td>
  	        <td>
  	          <a href="javascript:void(0);" onClick="getMootools('/warehouse/projecteditor/materialslist?projectId=$p_iProjectId&experimentId=$p_iExperimentId&format=ajax', 'experimentInfo');">$strMaterialName</a>
  	          <p>$strMaterialDesc</p>
  	          <p><b>Properties:</b></p>
  	          $strPropertiesHTML
  	          <p><b>Files:</b></p>
  	          $strFilesHTML
  	        </td>
                <td>$strMaterialType</td>
  	        <td>
                  [<a onclick="getMootools('/warehouse/projecteditor/editmaterial?format=ajax&amp;materialId=$iMaterialId', 'experimentInfo')" href="javascript:void(0);">Edit</a>]&nbsp;&nbsp;
                  $strDeleteLink
                </td>
  	      <tr>
ENDHTML;
  	  }else{
  	  	$strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="javascript:void(0);" onClick="getMootools('/warehouse/projecteditor/materialslist?projectId=$p_iProjectId&experimentId=$p_iExperimentId&materialId=$iMaterialId&format=ajax', 'experimentInfo');"><img border="0" width="16" height="16" alt="" title="Expand" src="/components/com_warehouse/images/arrow_right.png" style="border: 0px none;"></a></td>
  	        <td><a href="javascript:void(0);" onClick="getMootools('/warehouse/projecteditor/materialslist?projectId=$p_iProjectId&experimentId=$p_iExperimentId&materialId=$iMaterialId&format=ajax', 'experimentInfo');">$strMaterialName</a></td>
  	        <td>$strMaterialType</td>
                <td>
                  [<a href="javascript:void(0);" onClick="getMootools('/warehouse/projecteditor/editmaterial?format=ajax&materialId=$iMaterialId', 'experimentInfo')">Edit</a>]&nbsp;&nbsp;
                  $strDeleteLink
                </td>
  	      <tr>
ENDHTML;
  	  }
  	}

    $strHTML .= "</table>";

    return $strHTML;
  }

  public function findMaterialsByExperimentHTML($p_oMaterialArray, $p_iProjectId, $p_iExperimentId, $p_iSelectedMaterialId=0, $p_bCanDelete=false){
        $strHTML = "<table class=\"editorInputSizeFull\"  align=\"center\">
  				  <tr>
  				    <thead>
  				      <th style=\"width:16px\"></th>
  				      <th>Name</th>
  				      <th>Type</th>
                                      <th>Manage</th>
  				    </thead>
  				  <tr>";

  	foreach($p_oMaterialArray as $iMaterialIndex=>$oMaterial){
  	  $iMaterialId = $oMaterial->getId();
  	  $strMaterialName = $oMaterial->getName();
          $strMaterialDesc = nl2br($oMaterial->getDescription());
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

          $strDeleteLink = "";
          if($p_bCanDelete){
            $strDeleteLink = "[<a href='/warehouse/projecteditor/delete?format=ajax&eid=$iMaterialId&etid=134' class='modal'>Delete</a>]";
          }

  	  if($iMaterialId==$p_iSelectedMaterialId){
  	    $strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a style="border:0px;" href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/materials"><img border="0" width="16" height="16" alt="" title="Collapse" src="/components/com_warehouse/images/arrow_down.png" style="border: 0px none;"></a></td>
  	        <td>
  	          <a href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/materials">$strMaterialName</a>
  	          <p>$strMaterialDesc</p>
  	          <p><b>Properties:</b></p>
  	          $strPropertiesHTML
  	          <p><b>Files:</b></p>
  	          $strFilesHTML
  	        </td>
                <td>$strMaterialType</td>
  	        <td nowrap="">
                  [<a onclick="getMootools('/warehouse/projecteditor/editmaterial?format=ajax&amp;materialId=$iMaterialId', 'experimentInfo')" href="javascript:void(0);">Edit</a>]&nbsp;&nbsp;
                  $strDeleteLink
                </td>
  	      <tr>
ENDHTML;
  	  }else{
  	  	$strHTML .= <<< ENDHTML
	      <tr class="$strBgColor">
  	        <td><a href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/materials?materialId=$iMaterialId" style="border:0px;"><img border="0" width="16" height="16" alt="" title="Expand" src="/components/com_warehouse/images/arrow_right.png" style="border: 0px none;"></a></td>
  	        <td><a href="/warehouse/projecteditor/project/$p_iProjectId/experiment/$p_iExperimentId/materials?materialId=$iMaterialId">$strMaterialName</a></td>
  	        <td>$strMaterialType</td>
                <td nowrap="">
                  [<a href="javascript:void(0);" onClick="getMootools('/warehouse/projecteditor/editmaterial?format=ajax&materialId=$iMaterialId', 'experimentInfo')">Edit</a>]&nbsp;&nbsp;
                  $strDeleteLink
                </td>
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
          <td nowrap>$strPropertyName:</td>
          <td width="100%">$strPropertyValue $strUnitName $strUnitAbbreviation $strUnits</td>
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
   * Displays a table of properties for the given material object.
   * @param Material $p_oMaterial
   * @returns string
   */
  public function findMaterialPropertiesFormByMaterialHTML($p_oMaterial){
    $strPropertiesHTML = "<table id=\"materialProperties\" style=\"border:0px; margin-left:30px;\">";

    /* @var $oMaterialProperty MaterialProperty */
    foreach($p_oMaterial->getMaterialProperties() as $oMaterialProperty){
      $iPropertyId = $oMaterialProperty->getId();
      $iPropertyTypeId = $oMaterialProperty->getMaterialTypeProperty()->getId();
      $strPropertyName = $oMaterialProperty->getMaterialTypeProperty()->getName();
      $strPropertyValue = $oMaterialProperty->getValue();
      $strUnitName = "";
      

      /**
       * We are looking for the following (listed in order of preference):
       *   1. The assigned value for the measurement unit for this particular property of the material OR
       *   2. The default measurement unit for this kind of property in the experiment OR
       *   3. The default measurement unit for this kind of material property.
       * BUT we only need to do this if the material type property even has a measurement unit category.
       */
      $strUnitCombobox="";
      
      $prop = $oMaterialProperty->getMaterialTypeProperty();
      if( !is_null($prop->getUnitCategory()) ){
        $matUnit = NULL;
        if( !is_null($p_oMaterial) ) {
          $matProp = MaterialPropertyPeer::findByMaterialMaterialTypeProperty($p_oMaterial->getId(), $prop->getId());
          if(count($matProp) > 0) {
            $matUnit = $matProp[0]->getUnit();
          }
        }

        if( is_null($matUnit) ) {
          // No measurement unit assigned for them material property - get the experiment measurement unit.
          $expUnit = ExperimentMeasurementPeer::findByExperimentAndCategory($p_oExperiment->getId(), $prop->getUnitCategory()->getId());
          if($expUnit) {
            $matUnit = $expUnit->getDefaultUnit();
          }
        }

        if( is_null($matUnit) ) {
          // Still don't have it. Let's just get the default units for the Material Type Property's
          // MeasurementUnitCategory. Last chance!
          $matUnitCategory = $prop->getUnitCategory();
          if( !is_null($matUnitCategory) ) {
            $matUnit = MeasurementUnitPeer::findBaseUnitByCategory($matUnitCategory->getId());
          }
        }

        if( !is_null($matUnit) ) {
          $strUnitCombobox = $this->printUnitsBox($prop, $prop->getUnitCategory(), $matUnit);
        }
      }

      $strPropertiesHTML .= <<< ENDHTML
        <tr>
          <td nowrap width="1">$strPropertyName:</td>
          <td nowrap width="1"><input type="txt" name="property$iPropertyTypeId" value="$strPropertyValue"></td>
          <td>$strUnitCombobox</td>
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
   *
   * @param array <MaterialProperty> $p_oMaterialProperties
   * @param MaterialTypeProperty $p_oProp 
   */
  private function getPropertyData($p_oMaterialProperties, $p_oProp){
    /* @var $prop MaterialProperty */
    foreach($p_oMaterialProperties as $prop){
      if($prop->getMaterialTypeProperty()->getId()==$p_oProp->getId()){
        return array($prop->getValue(), $prop->getUnit());
      }
    }
    return null;
  }

  /**
   *
   * @param Experiment $p_oExperiment
   * @param Material $p_oMaterial
   * @return string
   */
  public function findMaterialPropertiesFormByMaterial($p_oExperiment, $p_oMaterial, $p_oMaterialTypeArray){
    $oMaterialType = $p_oMaterial->getMaterialType();
    $oMaterialProperties = $p_oMaterial->getMaterialProperties();
    $oMaterialPrototype = $p_oMaterial->getPrototype();
    $prototypeid = null;
    if($oMaterialPrototype){
      $prototypeid = $oMaterialPrototype->getId();
    }

    $strPropertiesHTML = "";
    foreach( $p_oMaterialTypeArray as $materialType ) {
      $matTypeId = $materialType->getId();
      $strPropertiesHTML .= "
      <div id=\"properties_$matTypeId\" style=\"display: none;\">
        <table border=\"0\">";

      $matTypeProperties = $materialType->getMaterialTypePropertys();

      foreach( $matTypeProperties as $prop ) {
        $propid = $prop->getId();
        list($value, $unit) = $this->getPropertyData($oMaterialProperties, $prop);

        $strPropertiesHTML .= "
        <tr>
          <td nowrap=\"nowrap\">".$prop->getName();


        if ( $prop->getRequired() ) {
          $strPropertiesHTML .= "<span class=\"orange\">*</span>";
        }//end required

        $strPropertiesHTML .= "
          </td>
          <td>";

        $doprint = 1;

        if( preg_match('/^Type of/', $prop->getName()) ) {
          //$matprotos = $command->getMaterialPrototypes($materialType);
          $matprotos = MaterialPeer::getLibraryMaterials($materialType);

          if( count($matprotos) > 0 ) {
            $doprint = 0;
            $strPropertiesHTML .= "
            <select id=\"prototype".$materialType->getId()."\" name=\"prototype".$materialType->getId()."\">";
            foreach($matprotos as $pt ) {
              if( $pt->getMaterialType()->getId() == $materialType->getId() ) {
              $strPropertiesHTML .= "<option value='".$pt->getId()."'";
                if($prototypeid==$pt->getId()){
                  $strPropertiesHTML .= " selected>";
                }
                $strPropertiesHTML .= $pt->getName()."</option>";
              }
            }
            $strPropertiesHTML .= "<option value=''";
            if($p_oMaterial && !$prototypeid) {
              $strPropertiesHTML .= 'selected';
            }
            $strPropertiesHTML .= '>Other</option>
            </select>';

            $strPropertiesHTML .= "
            <div id='typeother".$materialType->getId()."' style='display: ";
            if($p_oMaterial&&!$prototypeid){
             $strPropertiesHTML .= "'block'>";
            }else{
              $strPropertiesHTML .= "'none'>";
            }

            $strPropertiesHTML .= "
              Type Name <input type='text' id='property". $propid."' name='property". $propid."' value='".$value."' />
            </div>";
          }
        }

        if( $doprint ) {
          $strPropertiesHTML .= "
            <input type='text' id='property". $propid ."' name='property". $propid. "' value='". $value ."' />";
        }

        /**
         * We are looking for the following (listed in order of preference):
         *   1. The assigned value for the measurement unit for this particular property of the material OR
         *   2. The default measurement unit for this kind of property in the experiment OR
         *   3. The default measurement unit for this kind of material property.
         * BUT we only need to do this if the material type property even has a measurement unit category.
         */
      if( !is_null($prop->getUnitCategory()) ){
        $matUnit = NULL;
        if( !is_null($p_oMaterial) ) {
          $matProp = MaterialPropertyPeer::findByMaterialMaterialTypeProperty($p_oMaterial->getId(), $prop->getId());
          if(count($matProp) > 0) {
            $matUnit = $matProp[0]->getUnit();
          }
        }

        if( is_null($matUnit) ) {
          // No measurement unit assigned for them material property - get the experiment measurement unit.
          $expUnit = ExperimentMeasurementPeer::findByExperimentAndCategory($p_oExperiment->getId(), $prop->getUnitCategory()->getId());
          if($expUnit) {
            $matUnit = $expUnit->getDefaultUnit();
          }
        }

        if( is_null($matUnit) ) {
          // Still don't have it. Let's just get the default units for the Material Type Property's
          // MeasurementUnitCategory. Last chance!
          $matUnitCategory = $prop->getUnitCategory();
          if( !is_null($matUnitCategory) ) {
            $matUnit = MeasurementUnitPeer::findBaseUnitByCategory($matUnitCategory->getId());
          }
        }

        if( !is_null($matUnit) ) {
          $strPropertiesHTML .= $this->printUnitsBox($prop, $prop->getUnitCategory(), $matUnit);
        }
      }
          $strPropertiesHTML .= "
            <div id='property". $propid ."_err'>&nbsp;</div>
          </td>
        </tr>";
      }
    }
    $strPropertiesHTML .= "</table>";
    return $strPropertiesHTML;
  }

  private function printUnitsBox($prop, $category, $default=null) {
    $name = 'units' . $prop->getId();
    $units = $category->getUnits();
    if( !$units || !$units[0] ) {
      return false;
    }
    $strUnits = "<select id=\"$name\" name=\"$name\" size=\"1\">";
    $sel = '';
    $unitId = '';
    $unitAbbrev = '';

    if($default) {
      $unitId = $default->getId();
      $unitAbbrev = $default->getAbbreviation();
    }

    foreach( $units as $unit ) {

      if( $unitId && $unitId == $unit->getId() ) {
        $sel = 'selected';
      }

      // print name for time, abbreviation for everything else.
      if( $category->getName() == 'Time' ) {
        $strUnits .="<option value=\"{$unit->getId()}\" $sel>{$unit->getName()}</option>";
      } else {
        $strUnits .="<option value=\"{$unit->getId()}\" $sel>{$unit->getAbbreviation()}</option>";
      }
      $sel = '';
    }

    $strUnits .="</select>";

    return $strUnits;
  }

  /**
   * Displays a table of files for the given material object.
   * @param $p_oMaterial - current material object.
   * @returns html string
   */
  public function findMaterialFilesByExperimentHTML($p_oMaterial){
  	$strFilesHTML = "<table style=\"border-bottom:0px;border-top:0px;margin-left:30px;\">
  					  <tr><thead><th style='border-bottom:0px;padding-left:0px;'>Title or Name</th><th style='border-bottom:0px;'>Timestamp</th></thead></tr>";

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
  	      <td nowrap style="padding-left:0px;"><a href="$strFileLink" target="data_file">$strFileDisplay</a></td>
  	      <td>$strFileCreated</td>
  	    <tr>
ENDHTML;
  	}

  	if(sizeof($p_oMaterial->getFiles())==0){
  	  $strFilesHTML .= "<tr><td nowrap style=\"padding-left:0px;\">0 files listed.<td></tr>";
  	}
  	$strFilesHTML .= "</table>";

  	return $strFilesHTML;
  }
}
?>