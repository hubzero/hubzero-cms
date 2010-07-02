<?php
/**
 * @title
 *   Domain Entity Type Class
 *
 * @package lib.util
 *
 * @author
 *   Greg Crawford
 *
 * @abstract
 *   Class providing constants that define the various Domain Entity Types used
 *   througout NEEScentral code.
 *
 * @description
 *   In order to create a unique key for accessing Authorizations we need to combine
 *   the Entity Type (i.e. project, experiment, trial, repetition, etc) with the
 *   id of those types in their corresponding database tables.  For example, we can use
 *   the id of a project from the Project table along with the ENTITY_TYPE_PROJECT constant
 *   to allow a select from the Authorizations table.  The reason for requiring both the
 *   id and the entity type is that the ids stored in the various entity tables is not
 *   unique:  i.e. a record in the Project table can have an id equal to 1 and a record
 *   in the Experiment table can have an id of 1.  Combining these ids with an EntityType
 *   allows the creation of a unique key.
 *
 */

class DomainEntityType {
  const ENTITY_TYPE_UNKNOWN         = 0;
  const ENTITY_TYPE_PROJECT         = 1;
  const ENTITY_TYPE_ORGANIZATION    = 2;
  const ENTITY_TYPE_EXPERIMENT      = 3;
  const ENTITY_TYPE_TRIAL           = 4;
  const ENTITY_TYPE_REPETITION      = 5;
  const ENTITY_TYPE_FACILITY        = 20;
  const ENTITY_TYPE_TSUNAMI_PROJECT = 40;
  const ENTITY_TYPE_PERSON          = 141;


  /**
   * get the EntityTypeId from an Entity, special case if the entity is a
   *
   * @param BaseObject $obj
   * @return int EntityTypeID
   */
  static function getEntityTypeId(BaseObject $obj) {
    include_once "lib/data/Organization.php";

    // Kevin, some thing weird here, so I have to hack this code
    $class_name = get_class($obj);

    if($class_name == "Facility") {
      if($obj->getOrganizationTypeId() == OrganizationPeer::CLASSKEY_ORGANIZATION) {
        return self::ENTITY_TYPE_ORGANIZATION;
      }
    }

    return self::getClassEntityType($class_name);
  }

  /**
   * Given a class name (e.g., "StructuredProject") return
   * the corresponding entity_type_id
   *
   * @param String $classname
   * @return int EntityTypeID
   */
  static function getClassEntityType($classname) {

    switch ($classname) {
      case "Project":
      case "StructuredProject":
      case "UnstructuredProject":
      case "SuperProject":
      case "HybridProject":
        return self::ENTITY_TYPE_PROJECT;

      case "TsunamiProject":
        return self::ENTITY_TYPE_TSUNAMI_PROJECT;

      case "Organization":
        return self::ENTITY_TYPE_ORGANIZATION;

      case "Experiment":
      case "UnstructuredExperiment":
      case "StructuredExperiment":
      case "Simulation":
        return self::ENTITY_TYPE_EXPERIMENT;

      case "Facility":
        return self::ENTITY_TYPE_FACILITY;

      case "Repetition":
        return self::ENTITY_TYPE_REPETITION;

      case "Trial":
        return self::ENTITY_TYPE_TRIAL;

      case "Person":
        return self::ENTITY_TYPE_PERSON;

      default:
        return self::ENTITY_TYPE_UNKNOWN;
    }
  }

  /**
   * given a type code id (e.g., 20) and a primary key (e.g., 32)
   * return the associated object (e.g., Facility # 32)
   *
   * @param int $typecode : EntityType id
   * @param int $id: EntityId
   * @return Entity Object
   */
  static function getDomainEntity($typecode, $id) {
    if (!$typecode || !$id) {
      return null;
    }

    switch ($typecode) {
      case self::ENTITY_TYPE_EXPERIMENT:
        return ExperimentPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_FACILITY:
        return FacilityPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_ORGANIZATION:
        return OrganizationPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_PROJECT:
        return ProjectPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_REPETITION:
        return RepetitionPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_TRIAL:
        return TrialPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_TSUNAMI_PROJECT:
        return TsunamiProjectPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_PERSON:
        return PersonPeer::retrieveByPK($id);

      case self::ENTITY_TYPE_UNKNOWN:
        throw new Exception("Can not load domain entity of unknown type out of database");
    }

  }

/**
 * Get the list of parent EntityTypeId
 *
 * @param BaseObject $entity
 * @return array <EntityTypeId>
 */
  static function getParentEntityTypes(BaseObject $entity) {

    $types = array();
    $reflectionClass = new ReflectionClass(get_class($entity));

    while ($reflectionClass != null) {
      $entity_type_id = self::getClassEntityType($reflectionClass->getName());

      if ($entity_type_id != self::ENTITY_TYPE_UNKNOWN) {
        if( ! in_array($entity_type_id, $types)) {
          $types[] = $entity_type_id;
        }
      }
      $reflectionClass = $reflectionClass->getParentClass();
    }

    return $types;
  }

} // DomainEntityType
?>
