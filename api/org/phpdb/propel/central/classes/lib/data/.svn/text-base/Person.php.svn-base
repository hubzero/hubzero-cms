<?php

require_once 'lib/data/om/BasePerson.php';


/**
 * @title
 *   Person domain class
 *
 * @author
 *   Greg Crawford
 *
 * @abstract
 *   Domain class corresponding to the Person table in the NEESCentral database.
 *
 * @description
 *
 * Note that this extends BaseObject and thus inherits the functionality of the
 * BaseObject class.
 *
 * @package    lib.data
 */
class Person extends BasePerson {

  /**
   * Initializes internal state of Person object.
   */
  public function __construct($userName = '',
                              $firstName = '',
                              $lastName = '',
                              $phone = '',
                              $fax = '',
                              $eMail = '',
                              $category = '',
                              $address = '',
                              $comment = '',
                              $adminStatus = 0)
  {
    $this->setUserName($userName);
    $this->setFirstName($firstName);
    $this->setLastName($lastName);
    $this->setPhone($phone);
    $this->setFax($fax);
    $this->setEMail($eMail);
    $this->setCategory($category);
    $this->setAddress($address);
    $this->setComment($comment);
    $this->setAdminStatus($adminStatus);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Person/{$this->getId()}";
  }

  public function toString() {
    return "Person ID: "    . $this->getId() .
      ", Username: "   . $this->getUserName() .
      ", First name: " . $this->getFirstName() .
      ", Last name: "  . $this->getLastName() .
      ", Email: "      . $this->getEMail();
  }


  /**
   * Get the array of Role for this Person on an Entity
   *
   * @param BaseObject $entity
   * @return array <Role>
   */
  public function getRolesForEntity(BaseObject $entity) {
    $roles = array();
    $pers = PersonEntityRolePeer::findByPersonEntity($this, $entity);
    foreach ($pers as $per) {
      $roles[] = $per->getRole();
    }

    return $roles;
  }

  /**
   * Get the array of Authorization for this Person on an Entity
   *
   * @param BaseObject $entity
   * @return array <Authorization>
   */
  public function getAuthorizationsForEntity(BaseObject $entity) {
    return AuthorizationPeer::findByUserEntity($this, $entity);
  }


  /**
   * Remove completely a Person from an Entity by removing the Roles and the Authorizations
   *
   * @param BaseObject $entity
   */
  public function removeFromEntity(BaseObject $entity) {
    $this->deleteRolesForEntity($entity);
    $this->deleteAuthorizationsForEntity($entity);
  }



  /**
   * Remove completely a Person from an Organization by removing the Roles and the Authorizations
   *
   * @param Organization $org
   */
  public function removeOrganization(Organization $org) {
    $this->deleteRolesForOrganization($org);
    $this->deleteAuthorizationsForOrganization($org);
  }



  /**
   * Remove all Roles of this Person from an Organization
   *
   * @param Organization $org
   */
  private function deleteRolesForOrganization(Organization $org) {
    $pers = PersonEntityRolePeer::findByPersonEntityEntityType($this->getId(), $org->getId(), DomainEntityType::ENTITY_TYPE_ORGANIZATION);

    foreach ($pers as $per) {
      $per->delete();
    }
  }


  /**
   * Remove the Authorization of this Person from an Organization
   *
   * @param Organization $org
   */
  private function deleteAuthorizationsForOrganization(Organization $org) {
    $auth = AuthorizationPeer::findByPersonIdEntityidEntitytype($this->getId(), $org->getId(), DomainEntityType::ENTITY_TYPE_ORGANIZATION);
    if($auth)  $auth->delete();
  }



  /**
   * Check if this Person is the last person that has full permission on an Entity
   * Mostly, this function is usefull to prevent removing the last person that has full permission
   * from a Project or Experiment, so that no one has Grant permission
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function isLastPersonAuthorizedOnEntity(BaseObject $entity) {
    return (1 == AuthorizationPeer::countFullyAuthorizedPeople($entity));
  }


  /**
   * Remove the Authorization of this Person from an Entity
   *
   * @param BaseObject $entity
   */
  private function deleteAuthorizationsForEntity(BaseObject $entity) {
    $auth = $this->getAuthorizationsForEntity($entity);
    if($auth)  $auth->delete();
  }


  /**
   * Remove a Role of this Person from an Entity
   *
   * @param Role $role
   * @param BaseObject $entity
   */
  public function deleteRoleForEntity(Role $role, BaseObject $entity) {
    $per = PersonEntityRolePeer::findByPersonEntityRole($this->getId(), $entity->getId(), DomainEntityType::getEntityTypeId($entity), $role->getId());
    if($per) $per->delete();
  }


  /**
   * Remove all Roles of this Person from an Entity
   *
   * @param BaseObject $entity
   */
  private function deleteRolesForEntity(BaseObject $entity) {
    $pers = PersonEntityRolePeer::findByPersonEntity($this, $entity);

    foreach ($pers as $per) {
      $per->delete();
    }
  }


  /**
   * Adding a Role for this Person to an Entity
   *
   * @param Role $role
   * @param BaseObject $entity
   * @return boolean value, true if succeed, false if failed
   */
  public function addRoleForEntity(Role $role, BaseObject $entity) {
    try {
      $per = new PersonEntityRole($this->getId(), $entity->getId(), DomainEntityType::getEntityTypeId($entity), $role);
      $per->save();
      return true;
    }
    catch (Exception $e) {
      return false;
    }
  }




  /**
   * Adding a Role for this Person to an Organization
   * Please note: I cannot use the function addRoleForEntity
   * because, any Facility Organization object will be return as ENTITY_TYPE_Facility
   *
   * @param Role $role
   * @param Organization $org
   * @return boolean value, true if succeed, false if failed
   */
  public function addRoleForOrganization(Role $role, Organization $org) {
    try {
      $per = new PersonEntityRole($this->getId(), $org->getId(), DomainEntityType::ENTITY_TYPE_ORGANIZATION, $role);
      $per->save();
      return true;
    }
    catch (Exception $e) {
      return false;
    }
  }


  /**
   * Adding a new Authorization (Permission) for this Person to an Organization
   * Cannot use function addAuthForEntity because there is a bug if an Organization is
   * Facility then the DomainEntityType will return ENTITY_TYPE_FACILITY not ENTITY_TYPE_ORGANIZATION
   *
   * @param Organization $org
   * @param Permission $entity
   */
  public function addAuthForOrganization(Organization $org, Permissions $perms=null) {
    if(is_null($perms)) $perms = new Permissions(Permissions::PERMISSION_NONE);

    try {
      $auth = new Authorization($this->getId(), $org->getId(), DomainEntityType::ENTITY_TYPE_ORGANIZATION, $perms);
      $auth->save();
    }
    catch (Exception $e) {
      return false;
    }
  }


  /**
   * Adding a new Authorization (Permission) for this Person to an Entity
   *
   * @param BaseObject $entity
   * @param Permission $entity
   */
  public function addAuthForEntity(BaseObject $entity, Permissions $perms=null) {
    if(is_null($perms)) $perms = new Permissions(Permissions::PERMISSION_NONE);

    $auth = new Authorization($this->getId(), $entity->getId(), DomainEntityType::getEntityTypeId($entity), $perms);
    $auth->save();
  }


  /**
   * Get the Permissions for this Person on an Entity
   *
   * @param BaseObject $entity
   * @return Permission
   */
  public function getPermissionsForEntity(BaseObject $entity) {
    $authorization = $this->getAuthorizationsForEntity($entity);

    if (!$authorization) {
      $roles = $this->getRolesForEntity($entity);
      $permsStr = '';
      foreach ($roles as $role) {
        $permsStr .= $role->getDefaultPermissions()->toString() . ',';
      }
      // This is urgly but it work any way, because the string $perms can be like this: 'view,edit,view,view,edit,delete,grant...'
      return Permissions::fromString(rtrim($permsStr, ','));
    }
    else {
      return $authorization->getPermissions();
    }
  }


  /**
   * Grant the FULL permissions for this Person to an Entity and assign the Admin Role ('Principal Investigator' === ID: 1) of this entity
   *
   * @param BaseObject $entity
   */
  public function grantAllOnEntity(BaseObject $entity) {
    $this->addAuthForEntity($entity, new Permissions(Permissions::PERMISSION_ALL));
    $this->addRoleForEntity(RolePeer::find(1), $entity);
  }


  /**
   * Get the Thumnail Photo uploaded
   *
   * @return String $html imgage tag (<img...>)
   */
  function getPicture(){

    $default_thumbnail = "<img src=\"/images/default_user.jpg\" width='150' height='143' title=\"This member does not have any uploaded thumbnail.\"  alt=''/>";

    $thumbnail_array = $this->getPersonThumbnail();

    $thumbnail = null;

    if (is_array($thumbnail_array)) {
      $path = $thumbnail_array['path'];
      $name = $thumbnail_array['name'];
      $url = $thumbnail_array['url'];

      if(file_exists("$path/$name")) {
        list($width, $height, $type, $attr) = getimagesize(rawurldecode("$path/$name"));
        if($width && $height) {
          return "<img src=\"/thumbnails.php?name=" . urlencode($name) . "&path=" . urlencode($path) . "&thumb_size=150\"  alt=''/>";
        }
      }
    }

    return $default_thumbnail;
  }


  /**
   * Get the Member thumbnail that uploaded to Central by owner
   *
   * @return Array of thumbnail information (name, path, url)
   */
  function getPersonThumbnail() {
    $thumbnail = DataFilePeer::findThumbnailDataFile($this->getId(), DomainEntityType::ENTITY_TYPE_PERSON);

    if($thumbnail){
      $name = $thumbnail->getName();
      $path= $thumbnail->getPath();
      $url = $thumbnail->get_url();

      $pathinfo = pathinfo($name);

      if(in_array(strtolower($pathinfo['extension']), array('png', 'jpg', 'jpeg', 'gif'))) {
        return array('name' => $name, 'path' => $path, 'url' => $url);
      }
    }
    return null;
  }


  /**
   * Each Person is associated with a directory on disk.
   * This function returns the path of that directory for
   * this Person.
   *
   * @return String
   */
  public function getPathname() {
    return '/nees/home/member.groups/' . $this->getUserName();
  }


  /**
   * Check if this person is an Admin or not
   *
   * @return boolean
   */
  public function isAdmin() {
    return $this->getAdminStatus() == 1;
  }
} // Person
?>
