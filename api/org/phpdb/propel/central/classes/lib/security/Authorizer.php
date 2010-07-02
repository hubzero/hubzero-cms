<?php

require_once 'lib/data/Authorization.php';
require_once 'lib/data/Person.php';
require_once 'lib/util/DomainEntityType.php';
require_once 'lib/data/PersonEntityRole.php';
require_once 'lib/data/DataFile.php';
require_once 'lib/security/PermissionsView.php';

/** ****************************************************************************
 * @title
 *   Authorizer class (singleton)
 *
 * @author
 *   Greg Crawford
 *
 * @abstract
 *   Singleton class used globally for determining authorization.
 *
 * @description
 *    The Authorizer class is used globally to determine the authorization levels for the
 *  active user and the current entity (id and type) being currently accessed.  The
 *  Authorizer is built during the initial phases of the request so that the user id
 *  and entity id (projectid, experimentid, etc) is valid by the time we need to
 *  determine the authorization level for a given page.
 *
 ******************************************************************************/

class Authorizer {
  private static $instance;


  private $authorization;

  private $userName;
  private $uid;
  private $entityId;
  private $entityTypeId;

  /**
   * Construct function
   *
   */
  private function __construct() {

    if ( !empty($_SESSION[__CLASS__]['sessionActive']) ) {
      $this->restoreState();
    }
    else {
      $this->uid = 0;
      $this->entityId = 0;
      $this->entityTypeId = 0;
    }
  }


  /**
   * Destruct function ???
   *
   */
  function __destruct() {
    $this->saveState();
  }


  /**
   * Get the value of session by its key
   *
   * @param $key
   * @return $value
   */
  private function sessionGet($key) {
    return $_SESSION[__CLASS__][$key];
  }


  /**
   * Set the session by a pair ($key / $value)
   *
   * @param $key
   * @param $value
   */
  private function sessionSet($key, $value) {
    $_SESSION[__CLASS__][$key] = $value;
  }


  /**
   * Restore the current state
   *
   */
  private function restoreState() {
    $this->userName = $this->sessionGet('userName');
    $this->uid = $this->sessionGet('uid');
    $this->entityId = $this->sessionGet('entityId');
    $this->entityTypeId = $this->sessionGet('entityTypeId');
  }


  /**
   * Save the current state
   *
   */
  private function saveState() {
    $this->sessionSet('sessionActive', true);
    $this->sessionSet('userName', $this->userName);
    $this->sessionSet('uid', $this->uid);
    $this->sessionSet('entityId', $this->entityId);
    $this->sessionSet('entityTypeId', $this->entityTypeId);
  }


  /**
   * Get an instance Authorizer object
   *
   * @return Authorizer
   */
  public static function getInstance() {
    if (empty(self::$instance))
      self::$instance = new Authorizer();

    return self::$instance;
  }


  /**
   * Set the current logged in user by its username
   *
   * @param String $userName
   */
  public function setUser($userName) {

    if ($userName == '') {
      $this->userName = $userName;
      $this->uid = 0;
      return;
    }

    $person = PersonPeer::findByUserName($userName);

    if($person) {
      $this->userName = $userName;
      $this->uid = $person->getId();
    }
  }


  /**
   * Get the current Logged in userName
   *
   * @return String $userName
   */
  public function getUserName() {
    return $this->userName;
  }


  /**
   * Get the current Logged in userId
   *
   * @return int $userId
   */
  public function getUserId() {
    return $this->uid;
  }


  /**
   * Get the current Logged in user
   *
   * @return Person $person
   */
  public function getUser() {
    return PersonPeer::find($this->uid);
  }


  /**
   * Get the Entity Id of the current working entity
   *
   * @return int $entityId
   */
  public function getEntityId() {
    return $this->entityId;
  }


  /**
   * Get the Entity Type Id of the current working entity
   *
   * @return int $entityTypeId
   */
  public function getEntityTypeId() {
    return $this->entityTypeId;
  }


  /**
   * Set the entity we are working on
   *
   * @param BaseObject $entity
   */
  public function setEntity(BaseObject $entity = null) {
    if (!$entity) {
      $this->entityId = $this->entityTypeId = $this->authorization = null;
      return;
    }

    $entityId = $entity->getId();
    $entityTypeId = DomainEntityType::getEntityTypeId($entity);

    if ($entityId > 0) {
      $this->entityId = $entityId;
      $this->entityTypeId = $entityTypeId;
      $this->authorization = AuthorizationPeer::findByUIDEntity($this->uid, $entityId, $entityTypeId);
    }
  }


  /**
   * Get the current working entity
   *
   * @return BaseObject $entity
   */
  public function getEntity() {
    return DomainEntityType::getDomainEntity($this->entityTypeId, $this->entityId);
  }


  /**
   * Set the Project to the current working entity
   *
   * @param Project $p
   */
  public function setProject(Project $p) {
    if ($p) {
      $this->setEntity($p);
    }
  }


  /**
   * Determine if the logged-in user has VIEW permission on an entity
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function canView($entity = null) {
    return $this->canDo($entity, 'View');
  }


  /**
   * Determine if the logged-in user has CREATE permission on an entity
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function canCreate($entity = null) {
    return $this->canDo($entity, 'Create');
  }


  /**
   * Determine if the logged-in user has EDIT permission on an entity
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function canEdit($entity = null) {
    return $this->canDo($entity, 'Edit');
  }


  /**
   * Determine if the logged-in user has DELETE permission on an entity
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function canDelete($entity = null) {
    return $this->canDo($entity, 'Delete');
  }


  /**
   * Determine if the logged-in user has GRANT permission on an entity
   *
   * @param BaseObject $entity
   * @return boolean
   */
  public function canGrant($entity = null) {
    return $this->canDo($entity, 'Grant');
  }


  /**
   * Determine if the logged-in user is allowed
   * to perform some action on some entity
   *
   * @param BaseObject $entity
   * @param string $action
   * @return boolean
   */
  private function canDo($entity, $action) {
    $ret = false;
    $function = 'can' . $action;
    if( $entity ) {
      $published = $entity->isPublished();
      if ($published && $action == "View"){
        return true;
      }
      return PermissionsViewPeer::canDo($this->uid, $entity->getId(), DomainEntityType::getEntityTypeId($entity), $action);
    }
    else {
      if ( $this->authorization ) return $this->authorization->$function();
      else return false;
    }
  }


  /**
   * Determine if a NEESuser is allowed
   * to perform some action on some entity
   *
   * @param BaseObject $entity
   * @param string $action
   * @param int $userid
   * @return boolean
   */
  public function personCanDo($entity, $action, $userid) {
    $ret = false;
    $function = 'can' . $action;
    if( $entity ) {
      $published = $entity->isPublished();
      if ($published && $action == "View"){
        return true;
      }
      return PermissionsViewPeer::canDo($userid, $entity->getId(), DomainEntityType::getEntityTypeId($entity), $action);
    }
    return $ret;
  }


  /**
   * Check if the current Logged in user has a role on the working Entity
   *
   * @return boolean value
   */
  public function hasRole() {
    $results = PersonEntityRolePeer::findByPersonEntityEntityType($this->uid, $this->entityId, $this->entityTypeId);

    if (count($results) > 0) {
      return true;
    }

    $supper_per = PersonEntityRolePeer::findSuperPERsByPerson($this->uid);
    if ($supper_per) return true;

    return false;
  }


  /**
   * Check if the current user has curator Role or not
   *
   * @return boolean value true if succeeds 0 if fails
   */
  public function canCurate() {
    return $this->hasCuratorRole();
  }


  /**
   * Check if the current user has curator Role or not
   *
   * @return boolean value true if succeeds 0 if fails
   */
  private function hasCuratorRole (){
    $supper_per = PersonEntityRolePeer::findSuperPERsByPerson($this->uid);
    if($supper_per){
       $roleName  = $supper_per->getRole()->getName();
       if(preg_match("/Curator/i", $roleName)) return true;
    }
    return false;
  }

}
?>