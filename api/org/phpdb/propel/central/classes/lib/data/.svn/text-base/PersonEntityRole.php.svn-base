<?php

require_once 'lib/data/om/BasePersonEntityRole.php';
require_once 'lib/util/DomainEntityType.php';


/**
 * PersonEntityRole
 *
 * A {@link Person} may have multiple {@link Roles} on a given "Entity"
 * Where an entity is anything in the DB, but usually a Project, Experiment, Facility, etc.
 *
 *
 * @package    lib.data
 *
 * @uses Person
 * @uses EntityType
 * @uses Role
 *
 */
class PersonEntityRole extends BasePersonEntityRole {

  /**
   * Initializes internal state of PersonEntityRole object.
   */
  public function __construct(
    $personId = null,
    $entity_id = null,
    $entity_type_id = null,
    Role $role = null) {

    $this->setPersonId($personId);
    $this->setEntityId($entity_id);
    $this->setEntityTypeId($entity_type_id);
    $this->setRole($role);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/PersonEntityRole/{$this->getId()}";
  }


  /**
   * Get the BaseObject from this PersonEntityRole
   *
   * @return BaseObject $entity
   */
  public function getEntity() {
    return DomainEntityType::getDomainEntity($this->getEntityTypeId(), $this->getEntityId());
  }


  /**
   * Set the Entity for this PersonEntityRole
   *
   * @param BaseObject $entity
   */
  public function setEntity(BaseObject $entity) {
    if ($entity) {
      $this->setEntityId($entity->getId());
    }
    else {
      $this->setEntityId(null);
    }
  }


} // PersonEntityRole
?>
