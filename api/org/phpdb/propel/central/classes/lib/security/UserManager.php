<?php
include_once "lib/data/Person.php";
include_once "lib/data/Project.php";


/* ****************
The user manager singleton will provide global access to data in the Person table.
It will utilize the Person domain class. It also serves many gridauth interface
functions, before we can eliminate GridAuth completely from the code.

*******************/

class UserManager {

  private static $instance;
  private $adminStatus;
  private $userName;
  private $userFirstName;
  private $userId;
  private $gaSqlLink;

  private function __construct() {
    if ( !empty($_SESSION[__CLASS__]['sessionActive']) ) {
      $this->restoreState();
    }
    else {
      $this->resetState();
    }
  }

  function __destruct() {
    $this->saveState();
  }


  /**
   * Get an instance UserManager object
   *
   * @return UserManager
   */
  public static function getInstance() {
    if (empty(self::$instance))
    self::$instance = new UserManager();

    return self::$instance;
  }


  private function sessionGet($key) {
    return $_SESSION[__CLASS__][$key];
  }
  private function sessionSet($key, $value) {
    $_SESSION[__CLASS__][$key] = $value;
  }

  private function resetState() {
    $this->adminStatus = false;
    $this->userName = "";
    $this->userFirstName = "";
    $this->userId = 0;
  }

  private function restoreState() {
    $this->adminStatus = $this->sessionGet('adminStatus');
    $this->userName = $this->sessionGet('userName');
    $this->userFirstName = $this->sessionGet('userFirstName');
    $this->userId = $this->sessionGet('userId');
  }

  private function saveState() {
    $this->sessionSet('sessionActive', true);
    $this->sessionSet('adminStatus', $this->adminStatus);
    $this->sessionSet('userName', $this->userName);
    $this->sessionSet('userFirstName', $this->userFirstName);
    $this->sessionSet('userId', $this->userId);
  }


  /**
   * Is the current user is admin or not
   *
   * @return boolean value
   */
  public function isAdmin() {
    return $this->adminStatus;
  }


  /**
   * Sets the user who is currently logged in.
   *
   * @param String $userName
   */
  public function setUser($userName) {
    if ($userName == '') {
      $this->userName = "";
      $this->userFirstName = "";
      $this->userId = 0;
      return;
    }

    // Determine admin status.
    $person = PersonPeer::findByUserName($userName);
    if(is_null($person)) return false;

    $this->userName = $userName;
    $this->userFirstName = $person->getFirstName();
    $this->userId = $person->getId();
    $this->adminStatus = $person->getAdminStatus() == 1;
    $this->saveState();

    return true;
  }


  /**
   * Add a user to NEEScentral Person table. This must be done by an admin user only
   *
   * @param String $userName
   * @param String $firstName
   * @param String $lastName
   * @param String $phone
   * @param String $fax
   * @param String $eMail
   * @param String $category
   * @param String $address
   * @param String $comment
   * @param String $orgRoleId
   * @param String $orgId
   */
  public function addUser($userName, $firstName, $lastName, $phone, $fax, $eMail, $category, $address, $comment, $orgRoleId, $orgId) {
    if ($userName == '') {
      return null;
    }

    // Check for admin privileges
    if (!$this->isAdmin()) {
      throw new Exception("Error in UserManager::addUser - Not an admin.");
    }

    if(!$orgRoleId || !$orgId || $orgId <= 0) {
      throw new Exception("Error in UserManager::addUser - no organization or role selected.");
    }

    if (!$category) {
      $category = "Other";
    }

    $person = new Person($userName, $firstName, $lastName, $phone, $fax, $eMail, $category, $address, $comment);
    $person->save();

    // Each new user should be given PI permissions in the Default project.
    //$defaultRole = RolePeer::findByName("Principal Investigator");
    $defaultRole = RolePeer::findByNameEntityType("Other", DomainEntityType::ENTITY_TYPE_PROJECT);

    $demo_projid = 354;

    $defaultProject = ProjectPeer::retrieveByPK($demo_projid);

    $per = new PersonEntityRole($person->getId(), $demo_projid, DomainEntityType::ENTITY_TYPE_PROJECT, $defaultRole);
    $per->save();

    // Add entry to Authorization
    $auth = new Authorization($person->getId(), $demo_projid, DomainEntityType::ENTITY_TYPE_PROJECT, $defaultRole->getDefaultPermissions());
    $auth->save();

    $exps = $defaultProject->getExperiments();

    foreach($exps as $e) {
      $person->removeFromEntity($e);
      AuthorizationPeer::insertProjectAuthsForAllExperiments($demo_projid, $e->getId(),$person->getId());
      PersonEntityRolePeer::insertProjectPERforAllExperiments($demo_projid, $e->getId(),$person->getId());
    }

    // Add user's PersonEntityRole and Authorization for their role w/i the organization they've selected.
    // Throw an error if either or both have failed to be passed in.
    if($orgRoleId && $orgId && $orgId > 0) {

      // Fixed #Case 9129 on FogBugz

      $orgRole = RolePeer::find($orgRoleId);

      if (!$orgRole) {
        $orgRole = RolePeer::findByNameEntityType("Other", DomainEntityType::ENTITY_TYPE_ORGANIZATION);
      }


      $org = OrganizationPeer::find($orgId);

      if($org) {
        $person->addRoleForOrganization($orgRole, $org);
        $person->addAuthForOrganization($org, $orgRole->getDefaultPermissions());
      }
      else {
        throw new Exception("Error in UserManager::addUser - organization not found.");
      }
    }
    else {
      throw new Exception("Error in UserManager::addUser - no organization or role selected.");
    }
  }


  /**
   * Return the username of the current logged-in user
   *
   * @return String $username
   */
  function getMyUserName() {
    return $this->userName;
  }


  /**
   * Return the First Name of the current logged-in user
   *
   * @return String $firstname
   */
  function getMyFirstName() {
    return $this->userFirstName;
  }



  /**
   * Return the UserId of the current logged-in user
   *
   * @return String $userId
   */
  function getMyUserId() {
    return $this->userId;
  }


  /**
   * Get current Login user as an Person Object
   *
   * @return Person $loginUser
   */
  function getMyLoginUser() {
    if(empty($this->userName)) return null;

    return PersonPeer::find($this->userId);
  }


  /**
   * Get the PersonEntityRoles for the specified user given by userName.
   *
   * @param string $userName
   * @return array of PersonEntityRole objects
   */

  public function getPersonEntityRoles($userName) {
    if ($userName == '') {
      return null;
    }

    $person = PersonPeer::findByUserName($userName);

    if (is_null($person)) {
      throw new Exception("Error in UserManager::getEntities - No such user.");
    }

    return PersonEntityRolePeer::findByUid($person->getId());

  }


  /**
   * get the list of PersonEntityRoles of the current user
   *
   * @return array of PersonEntityRole objects
   */
  public function getMyPersonEntityRoles() {

    return PersonEntityRolePeer::findByUid($this->userId);
  }



  /**
   * Gets the primary organization for the user. There should
   * only be one, right now. We are not currently supporting
   * multiple organization designations for Person objects.
   *
   * @param String $userId
   * @return Organization object
   */
  public function getPrimaryOrganization($userId) {
    if ( !is_numeric($userId) ) {
      return null;
    }

    $orgs = array();

    $perColl = PersonEntityRolePeer::findByPersonEntityType($userId, DomainEntityType::ENTITY_TYPE_ORGANIZATION);

    foreach($perColl as $per) {
      $entityId = $per->getEntityId();
      $organization = OrganizationPeer::find($entityId);

      // add this organization to the orgs array
      if(!is_null($organization)) {
        return $organization;
      }
    }
    return null; // otherwise, no organization - just return null;
  }


  /**
   * Get the primary Organization of the current user
   *
   * @return Organization object
   */
  public function getMyOrganization() {
    return $this->getPrimaryOrganization($this->userId);
  }


  /**
   * Indicate whether or not the current user belongs to the project specified by projectName.
   *
   * @param String $projectName
   * @return boolean
   */
  public function isProjectMember($projectName) {

    // Find project using projectName
    $project = ProjectPeer::find( $projectName );

    if (is_null($project)) {
      return false;
    }

    return $this->isMember($project);
  }


  /**
   * Indicate whether or not the current user is member of the specified entity.
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function isMember(BaseObject $entity) {
    if (! $this->userId) {
      return false;
    }

    $PERs = PersonEntityRolePeer::findByPersonEntityEntityType($this->userId, $entity->getId(), DomainEntityType::getEntityTypeId($entity));
    if (count($PERs) > 0) return true;

    // A special case, a superRole admin can be consider as a member
    return (self::hasSuperRole());
  }


  public function hasSuperRole() {
    $supper_per = PersonEntityRolePeer::findSuperPERsByPerson($this->userId);
    return is_null($supper_per) == false;
  }

  public function writeMemberCheck($p_iPersonId, $p_iEntityId, $p_iEntityTypeId){
    $myFile = "/www/neeshub/logs/datafile.log";
    $fh = fopen($myFile, 'a') or die("can't open file");
    $stringData = date("r"). " - UserManager::isMember - user=".$p_iPersonId.", entity=".$p_iEntityId.", type=".$p_iEntityTypeId."\n";
    fwrite($fh, $stringData);
    fclose($fh);
  }
}
?>
