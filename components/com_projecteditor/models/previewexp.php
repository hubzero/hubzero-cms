<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('experiment.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'api/org/nees/html/UserRequest.php';
require_once 'lib/data/ExperimentFacility.php';
require_once 'lib/data/ExperimentEquipment.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'api/org/nees/exceptions/ValidationException.php';

class ProjectEditorModelPreviewExp extends ProjectEditorModelExperiment{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }
  
  public function findSpecimenByName($p_strName){
    return SpecimenPeer::findSpecimenByName($p_strName);
  }
  
  public function findSpecimenByProject($p_iProjectId){
    return SpecimenPeer::findByProject($p_iProjectId);
  }

  
  /**
   * Initialize the recursive setFacilityHelper method.
   * @param $p_oExperiment
   * @param $p_strFacilityName
   * @param $p_strFacilityArray
   * @returns Experiment
   */
  public function setFacility($p_oExperiment, $p_strFacilityName, $p_strFacilityArray=null, $p_iFacilityIndex=0){
    if(sizeof($_SESSION["facility"]) > 0 && strlen($_REQUEST["facility"]) == 0){
      $strFacilityArray = $_SESSION["facility"];
      $strFacilityName = $strFacilityArray[0];
      $iFacilityIndex=1;
      return $this->setFacilityHelper($p_oExperiment, $strFacilityName, $_SESSION["facility"], $iFacilityIndex);
    }else{
      return $this->setFacilityHelper($p_oExperiment, $p_strFacilityName, $p_strFacilityArray, $p_iFacilityIndex);
    }
  }

  /**
   * Recurrively adds ExperimentFacility objects to an Experiment.
   * @param $p_oExperiment
   * @param $p_strFacilityName
   * @param $p_strFacilityArray
   * @returns Experiment
   *
   */
  public function setFacilityHelper($p_oExperiment, $p_strFacilityName, $p_strFacilityArray=null, $p_iFacilityIndex=0){
    if(isset($p_strFacilityName)){
      if(strlen($p_strFacilityName) > 0){
        $oOrganization = $this->findOrganizationByName($p_strFacilityName);
        if(!$oOrganization){
          throw new ValidationException($p_strFacilityName. " is not a valid organization.");
        }

        if($oOrganization->getFacilityId() != 0){
          $oExperimentFacility = new ExperimentFacility();
          $oExperimentFacility->setOrganization($oOrganization);

          $p_oExperiment->addExperimentFacility($oExperimentFacility);

          if($p_strFacilityArray != null){
            ++$p_iFacilityIndex;
            if( $p_iFacilityIndex < sizeof($p_strFacilityArray) ){
              $this->setFacilityHelper($p_oExperiment, $p_strFacilityArray[$p_iFacilityIndex], $p_strFacilityArray, $p_iFacilityIndex);
            }
          }
        }else{
          throw new ValidationException($p_strFacilityName. " is not a NEES facility.");
        }
      }
    }

    return $p_oExperiment;
  }

  /**
   * Sets a collection of Equipment objects.
   * @param <integer> $p_iEquipmentIdArray
   * @return <ExperimentEquipment> array
   */
  public function setEquipmentArray($p_iEquipmentIdArray){
    $oReturn = array();
    while (list ($key,$iEquipmentId) = @each ($p_iEquipmentIdArray)) {
      $oEquipment = EquipmentPeer::find($iEquipmentId);
      array_push($oReturn, $oEquipment);
    }
    return $oReturn;
  }

  /**
   * Sets the specimen type
   * @param <Project> $p_oProject
   * @param <String> $p_strSpecimenName
   * @return Specimen
   */
  public function setSpecimenType($p_oProject, $p_strSpecimenName){
    $oSpecimen = null;
    if(strlen($p_strSpecimenName) > 0){
      $oSpecimen = $this->findSpecimenByProject($p_oProject->getId());
      if(!$oSpecimen){
        $oSpecimen = new Specimen();
        $oSpecimen->setName($p_strSpecimenName);
        $oSpecimen->setTitle($p_strSpecimenName);
        $oSpecimen->setProject($p_oProject);
      }
    }
    return $oSpecimen;
  }

  /**
   * Sets the materials for the Experiment
   * @param <type> $p_strMaterialName
   * @param <type> $p_strMaterialType
   * @param <type> $p_strMaterialDesc
   * @param <type> $p_oMaterialFile
   * @param <type> $p_oMaterialArray
   */
  public function setMaterial($p_oExperiment, $p_strMaterialName, $p_strMaterialType,
                               $p_strMaterialDesc, $p_oMaterialFile, $p_oMaterialArray){

    if( strlen($p_strMaterialType) > 0 ){
      if( strlen($p_strMaterialType) ==0 ){
        throw new Exception("Material type not specified for ".$p_strMaterialName);
      }

      $oMaterial = new Material();
      $oMaterial->setName($strMaterialName);
      $oMaterial->setDescription($strMaterialDesc);

      $oMaterialType = null;

      //get the material type
      $oMaterialType = $this->findMaterialTypeByDisplayName($strMaterialType);
      if(!$oMaterialType){
        $oMaterialType = new MaterialType();
        $oMaterialType->setDisplayName($strMaterialType);

        $strSystemName = strtolower($strMaterialType);
        $strSystemName = str_replace(" ", "_", $strSystemName);
        $oMaterialType->setSystemName($strSystemName);
      }
      $oMaterial->setMaterialType($oMaterialType);

      if($p_oMaterialArray==null){
        $p_oMaterialArray = array();
        array_push($p_oMaterialArray, $oMaterial);
      }
    }



  }

  public function setMaterialMeasurementUnits($p_oExperiment, $p_oMaterialArray,
                                               $p_oReturnMaterialArray=array(),
                                               $p_iMaterialIndex=0){

    if( $p_iMaterialIndex < sizeof($p_oMaterialArray) ){
      //get the current Material object
      $oMaterial = $p_oMaterialArray[$p_iMaterialIndex];

      //set the field names from the form
      $strPropertyTypeName = "materialPropertyTypeName".$p_iMaterialIndex."[]";
      $strPropertyTypeValue = "materialPropertyTypeValue".$p_iMaterialIndex."[]";
      $strPropertyTypeUnit = "materialPropertyTypeUnit".$p_iMaterialIndex."[]";

      //check for the name
      $strPropertyTypeNameArray = null;
      if( isset($_REQUEST[$strPropertyTypeName]) ){
        $strPropertyTypeNameArray = $_REQUEST[$strPropertyTypeName];
      }

      //if we have a name, we should also have a value
      $strPropertyTypeValueArray = null;
      if( $strPropertyTypeNameArray && isset($_REQUEST[$strPropertyTypeValue]) ){
        $strPropertyTypeValueArray = $_REQUEST[$strPropertyTypeValue];
      }else{
        throw new Exception("Material property value missing.");
      }

      //if we have a value, we should also have units
      $strPropertyTypeUnitArray = null;
      if( $strPropertyTypeValueArray && isset($_REQUEST[$strPropertyTypeUnit]) ){
        $strPropertyTypeUnitArray = $_REQUEST[$strPropertyTypeUnit];
      }else{
        throw new Exception("Material property units missing.");
      }

      //collect the properties
      $iIndex = 0;
      while($iIndex < 10){
        $strMaterialPropertyTypeName = $strPropertyTypeNameArray[$iIndex];
        $strMaterialPropertyTypeValue = $strPropertyTypeValueArray[$iIndex];
        $strMaterialPropertyUnitName = $strPropertyTypeUnitArray[$iIndex];

        if(strlen($strMaterialPropertyTypeName) > 0 &&
           strlen($strMaterialPropertyTypeValue) > 0 &&
           strlen($strMaterialPropertyUnitName) > 0){

          $oMaterialType = $oMaterial->getMaterialType();

          $oMaterialTypeProperty = new MaterialTypeProperty($oMaterialType,
                                                            $strMaterialPropertyTypeName, $datatype="", $strMaterialPropertyUnitName, $required, $options, $unitCategory);
        }
        ++$iIndex;
      }
    }
    return $p_oReturnMaterialArray;
  }

  public function getExperimentFacilitiesHTML($p_oExperimentFacilityArray){
    $strHTML = "";

    foreach($p_oExperimentFacilityArray as $iKey => $oExperimentFacility){
      $iFacilityId = $oExperimentFacility->getOrganization()->getFacilityId();
      $strFacilityName = $oExperimentFacility->getOrganization()->getName();
      $strHTML .= <<< ENDHTML
                    <span class="nobr"><a href="/sites/?view=site&id=$iFacilityId" target="newexp">$strFacilityName</a></span>
ENDHTML;
      if($iKey < sizeof($p_oExperimentFacilityArray)-1){
        $strHTML .= ", ";
      }
    }

    return $strHTML;
  }

  public function getHubAccessSettings($p_strOracleViewSettings){
    $strReturn = "";
    switch($p_strOracleViewSettings){
      case "PUBLIC":
        $strReturn = "public";
      break;
      case "MEMBERS":
        $strReturn = "protected";
      break;
      default:
        $strReturn = "private";
    }
    return $strReturn;
  }
  
}

?>