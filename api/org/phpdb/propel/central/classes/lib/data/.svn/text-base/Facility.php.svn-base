<?php

require_once 'lib/data/Organization.php';
require_once 'lib/data/om/BaseOrganization.php';
require_once 'lib/data/ExperimentFacility.php';

/**
 * Facility
 *
 * This is a two-purposed class: on the one hand Experimentalists
 *   use facilities to do experiments
 *   easily tap into information...
 *   should be able to populate channel list from Facility Equipment Inventory
 * On the other hand, Facility managers use this to maintain inventory and
 *   status information
 *
 * @package    lib.data
 * @uses Organization
 * @uses OrganizationPeer
 * @uses Experiment
 * @uses Project
 */
class Facility extends Organization {

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Facility/{$this->getId()}";
  }


  /**
   * Constructs a new Facility class, setting the org_type_id column to OrganizationPeer::CLASSKEY_1.
   */
  function __construct( $name = "",             // Defined in Organization
                        $description = "",      // Defined in Organization
                        $url = "",              // Defined in Organization
                        $sensorManifest = null, //  Defined in Organization
                        $shortName = "",
                        $sysadmin = "",
                        $sysadminEmail = "",
                        $sysadminUser = "",
                        $siteOpUser = "",
                        $flexTPS_URL = "",
                        $pop_URL = "",
                        $nawiStatus = "",
                        $timezone = "GMT",
                        $nawiAdminUsers = "",
                        $image_URL = "",
                        $sitename = "",
                        $department = "",
                        $laboratory = "",
                        $nsfAcknowledgement = "",
                        $nsfAward_URL = "")
  {
    parent::__construct($name,
                        $description,
                        $url,
                        $sensorManifest);

    $this->setOrganizationTypeId(OrganizationPeer::CLASSKEY_FACILITY);
    $this->setShortName($shortName);
    $this->setSysadmin($sysadmin);
    $this->setSysadminEmail($sysadminEmail);
    $this->setSysadminUser($sysadminUser);
    $this->setSiteOpUser($siteOpUser);
    $this->setFlexTpsUrl($flexTPS_URL);
    $this->setPopUrl($pop_URL);
    $this->setNawiStatus($nawiStatus);
    $this->setTimezone($timezone);
    $this->setNawiAdminUsers($nawiAdminUsers);
    $this->setImageUrl($image_URL);
    $this->setSiteName($sitename);
    $this->setDepartment($department);
    $this->setLaboratory($laboratory);
    $this->setNsfAcknowledgement($nsfAcknowledgement);
    $this->setNsfAwardUrl($nsfAward_URL);
  }


  // Override from DomainObject to indicate that Facilities should always be publicly searchable & visible.
  function isVisibleToCurrentUser() {
    return true;
  }


  public function hasExperiment(Experiment $exp) {
    $efs = ExperimentFacilityPeer::findByExperimentFacility($exp->getId(), $this->getId());
    return count($efs) > 0;
  }


  public function addExperiment(Experiment $exp) {
    if (is_null($this->getId())) {
      $this->save();
    }
    if (is_null($exp->getId())) {
      $exp->save();
    }

    if (!$this->hasExperiment($exp)) {
      $ef = new ExperimentFacility($exp,$this);
      $ef->save();
      $this->addExperimentFacility($ef);

      return $ef;
    }
  }


  public function removeExperiment(Experiment $exp) {
    $ef = ExperimentFacilityPeer::findByExperimentFacility($exp->getId(), $this->getId());
    if( $ef ) $ef->delete();
  }


  /**
   * Print out information of facility
   *
   * @return string facility information
   */
  public function toString() {
    return
    "Facility ID: "    . $this->getFacilityId() .
    ", Name: "         . $this->getName() .
    ", ShortName: "    . $this->getShortName();
  }

  function changeSiteContactPerson(Person $current_contact, Person $new_contact){
    // Get 'Site Contact' Role
    $role = RolePeer::findByNameEntityType("Site Contact", DomainEntityType::ENTITY_TYPE_FACILITY);

    $criteria = new Criteria();
    $criteria->add(PersonEntityRolePeer::ENTITY_TYPE_ID, 20);
    $criteria->add(PersonEntityRolePeer::ENTITY_ID, $this->getId());
    $criteria->add(PersonEntityRolePeer::PERSON_ID, $current_contact->getId());
    $criteria->add(PersonEntityRolePeer::ROLE_ID, $role->getId());

    $pers = $current_contact->getPersonEntityRoles($criteria);
    foreach($pers as $per) {
      $per->setPerson($new_contact);
      $per->save();
    }

    //if previous contact person has no role now, add other role for him/her
    $criteria = new Criteria();
    $criteria->add(PersonEntityRolePeer::ENTITY_ID, $this->getId());
    $criteria->add(PersonEntityRolePeer::PERSON_ID, $current_contact->getId());

    if (count($current_contact->getPersonEntityRolesJoinEntityType($criteria))==0) {
      $otherRole = RolePeer::findByNameEntityType("Other Academic Personnel", DomainEntityType::ENTITY_TYPE_FACILITY);
      $current_contact->addRoleForEntity($otherRole, $this);
    }

  }

  /**
   * Overwrite function getImageUrl from parent class
   * @return String ImgURL
   */
  function getImageUrl() {
    return "/images/facility_" . strtolower($this->getShortName()) . ".jpg";
  }

  /**
   * @deprecated
   *
   * @return boolean value
   */
  public function isPublished() {
    return TRUE;
  }


  /**
   * Each Facility is associated with a directory on disk.
   * This function returns the path of that directory for
   * this Facility.
   *
   */
  public function getPathname() {
    return '/nees/home/facility.groups/' . $this->getShortName();
  }
} // Facility
?>
