<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'lib/data/ExperimentFacility.php';
require_once 'lib/data/EquipmentPeer.php';
require_once 'lib/data/Equipment.php';
require_once 'lib/data/ExperimentEquipment.php';
require_once 'lib/data/ExperimentEquipmentPeer.php';
require_once 'lib/data/ExperimentDomain.php';
require_once 'lib/data/ExperimentDomainPeer.php';
require_once 'lib/data/OrganizationPeer.php';

class ProjectEditorModelExperiment extends ProjectEditorModelBase{


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

  public function suggestSpecimen($p_strName) {
    return SpecimenPeer::suggestByName($p_strName);
  }


  public function createAuthorization($p_iCreatorId, $p_iProjectId){
    $perms = new Permissions( Permissions::PERMISSION_ALL );

    $auth  = new Authorization($p_iCreatorId, $p_iProjectId,  DomainEntityType::ENTITY_TYPE_PROJECT, $perms );
    $auth->save();

    return $auth;
  }

  public function createPersonEntityRole($p_iCreatorId, $p_iProjectId, $p_iRoleId){
    $oRole = RolePeer::find($p_iRoleId);

    $oPersonEntityRole = new PersonEntityRole($p_iCreatorId, $p_iProjectId,  DomainEntityType::ENTITY_TYPE_PROJECT, $oRole);
    $oPersonEntityRole->save();

    return $oPersonEntityRole;
  }

  /**
   * Find a list of all ExperimentDomain
   *
   * @return array <ExperimentDomain>
   */
  public function getExperimentDomains(){
    return ExperimentDomainPeer::findAll();
  }

  /**
   *
   * @param int $p_iExperimentDomainId
   * @return ExperimentDomain
   */
  public function getExperimentDomainById($p_iExperimentDomainId){
    return ExperimentDomainPeer::retrieveByPK($p_iExperimentDomainId);
  }

  /**
   *
   * @param Experiment $p_oExperiment
   * @param array $p_oFacilityArray
   */
  public function addFacilities($p_oExperiment, $p_oFacilityArray, $p_oConnection=null){
    FacilityPeer::deleteByExperimentId($p_oExperiment->getId(), $p_oConnection=null);

    foreach($p_oFacilityArray as $oFacility){
      $p_oExperiment->addFacility($oFacility);
    }//end loop

    return $p_oExperiment;
  }

  /**
   *
   * @param array $p_strFacilityNameArray
   * @return array
   */
  public function validateFacilitiesByName($p_strFacilityNameArray){
    $oFacilityArray = array();
    while (list ($key,$strFacilityName) = @each ($p_strFacilityNameArray)) {
      if(StringHelper::hasText($strFacilityName)){
        $oFacility = $this->findFacilityByName($strFacilityName);
        if(!$oFacility){
          throw new ValidationException("'$strFacilityName' is not a NEES facility. As you type, we will suggest names.  Click on the desired facility.");
        }

        array_push($oFacilityArray, $oFacility);
      }//if hasText
    }//end loop

    return $oFacilityArray;
  }

  /**
   * Initialize the recursive setFacilityHelper method.
   * @param $p_oExperiment
   * @param $p_strFacilityName
   * @param $p_strFacilityArray
   * @returns Experiment
   */
  /*
  public function addFacilities($p_oExperiment, $p_strFacilityName, $p_strFacilityArray=null, $p_iFacilityIndex=0){
    if(sizeof($_SESSION["facility"]) > 0 && strlen($_REQUEST["facility"]) == 0){
      $strFacilityArray = $_SESSION["facility"];
      $strFacilityName = $strFacilityArray[0];
      $iFacilityIndex=1;
      return $this->addFacilitiesHelper($p_oExperiment, $strFacilityName, $_SESSION["facility"], $iFacilityIndex);
    }else{
      return $this->addFacilitiesHelper($p_oExperiment, $p_strFacilityName, $p_strFacilityArray, $p_iFacilityIndex);
    }
  }
  */

  /**
   * Recurrively adds ExperimentFacility objects to an Experiment.
   * @param Experiment $p_oExperiment
   * @param string $p_strFacilityName
   * @param array $p_strFacilityArray
   * @returns Experiment
   *
   */
  /*
  public function addFacilitiesHelper($p_oExperiment, $p_strFacilityName, $p_strFacilityArray=null, $p_iFacilityIndex=0){
    if(isset($p_strFacilityName)){
      if(strlen($p_strFacilityName) > 0){
        $oFacility = $this->findFacilityByName($p_strFacilityName);
        if(!$oFacility){
          throw new ValidationException($p_strFacilityName. " is not a valid facility.");
        }

        $p_oExperiment->addFacility($oFacility);
        if($p_strFacilityArray != null){
          ++$p_iFacilityIndex;
          if( $p_iFacilityIndex < sizeof($p_strFacilityArray) ){
            $p_oExperiment = $this->setFacilityHelper($p_oExperiment, $p_strFacilityArray[$p_iFacilityIndex], $p_strFacilityArray, $p_iFacilityIndex);
          }
        }
      }
    }

    return $p_oExperiment;
  }
  */

  /**
   * Sets a collection of Equipment objects.
   * @param <integer> $p_iEquipmentIdArray
   * @return array <Equipment>
   */
//  public function setEquipmentArray($p_iEquipmentIdArray){
//    $oReturn = array();
//    while (list ($key,$iEquipmentId) = @each ($p_iEquipmentIdArray)) {
//      $oEquipment = EquipmentPeer::find($iEquipmentId);
//      array_push($oReturn, $oEquipment);
//    }
//    return $oReturn;
//  }

  /**
   *
   * @param Experiment $p_oExperiment
   * @param array <int> $p_iEquipmentIdArray
   * @return array <ExperimentEquipment>
   */
  public function addEquipment($p_oExperiment, $p_iEquipmentIdArray, $p_oConnection=null){
    $oReturn = array();

    ExperimentEquipmentPeer::deleteByExperientId($p_oExperiment->getId(), $p_oConnection);

    while (list ($key,$oValue) = @each ($p_iEquipmentIdArray)) {
      /* @var $oEquipment Equipment */
      $oEquipment = null;
      if(is_numeric($oValue)){
        $oEquipment = EquipmentPeer::find($oValue);
      }else{
        $strValueArray = explode(":::", $oValue);

        //query didn't like the escaped apostrophe - 20101119
        $strEquipmentName = str_replace("\'", "'", $strValueArray[0]);

        $iEquipmentModel = $strValueArray[1];
        $iEquipmentOrgId = $strValueArray[2];

        $oEquipment = EquipmentPeer::findByNameModelIdOrgId($strEquipmentName, $iEquipmentModel, $iEquipmentOrgId);
      }

      $oExperimentEquipment = new ExperimentEquipment($p_oExperiment, $oEquipment);
      $oExperimentEquipment->save();

      array_push($oReturn, $oExperimentEquipment);
    }

    return $oReturn;
  }

  public function findFacilityByExperiment($p_iExperimentId) {
    return OrganizationPeer::findExperimentFacility($p_iExperimentId);
  }

  public function findEquipmentByExperimentId($p_iExperimentId){
    return ExperimentEquipmentPeer::findByExperiment($p_iExperimentId);
  }

  public function deleteSpecimenByProject($p_iProjectId, $p_oConnection){
    SpecimenPeer::deleteSpecimenByProject($p_iProjectId, $p_oConnection);
  }

}

?>