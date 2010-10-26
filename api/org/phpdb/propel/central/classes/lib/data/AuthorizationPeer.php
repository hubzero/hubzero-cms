<?php

// include base peer class
require_once 'lib/data/om/BaseAuthorizationPeer.php';

// include object class
include_once 'lib/data/Authorization.php';

require_once 'lib/util/DomainEntityType.php';
require_once 'lib/security/Permissions.php';

/**
 * AuthorizationPeer
 *
 * Peer class for Authorization
 * Contains static methods to operate on the Authorization table
 *
 * @package    lib.data
 *
 */
class AuthorizationPeer extends BaseAuthorizationPeer {

  /**
   * Find an Authorization object based on its ID
   *
   * @param int $id
   * @return Authorization
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }



  /**
   * @desc Find array containing all Authorizations
   * @return array<Authorization>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }



  /**
   * @desc Find the only authorization given by an EntityID, EntityTypeID and Person ID
   *
   * @param int $person_id
   * @param int $entity_id
   * @param int $entity_type_id
   * @return Authorization Object
   */
  public static function findByPersonIdEntityidEntitytype($person_id, $entity_id, $entity_type_id) {
    $c = new Criteria();
    $c->add(self::PERSON_ID, $person_id);
    $c->add(self::ENTITY_ID, $entity_id);
    $c->add(self::ENTITY_TYPE_ID, $entity_type_id);
    return self::doSelectOne($c);
  }

  /**
   * @desc Find authorizations given by an EntityTypeID and Person ID
   *
   * @param int $person_id
   * @param int $entity_type_id
   * @return array <Authorization>
   */
  public static function findByPersonIdEntitytypeId($person_id, $entity_type_id) {
    $c = new Criteria();
    $c->add(self::PERSON_ID, $person_id);
    $c->add(self::ENTITY_TYPE_ID, $entity_type_id);
    return self::doSelect($c);
  }


  /**
   * @desc Find authorizations given by an EntityTypeID
   *
   * @param int $entity_type_id
   * @return array <Authorization>
   */
  public static function findByEntitytypeId($entity_type_id) {
    $c = new Criteria();
    $c->add(self::ENTITY_TYPE_ID, $entity_type_id);
    return self::doSelect($c);
  }



  /**
   * @desc Find the list of authorization given by Person ID
   *
   * @param int $person_id
   * @return Array <Authorization Object>
   */
  public static function findByPersonId($person_id) {
    $c = new Criteria();
    $c->add(self::PERSON_ID, $person_id);
    return self::doSelect($c);
  }



  /**
   * @desc Find the list of authorization given by Person ID
   * Wrap {@link AuthorizationPeer::findByPersonId()}
   *
   * @param int $person_id
   * @return Array <Authorization Object>
   */
  public static function findByUid($person_id) {
    return self::findByPersonId($person_id);
  }



  /**
   * @desc Find the list of Authorizations associates with a Project given by ProjID
   *
   * @param int $projid
   * @return Array <Authorization Object>
   */
  public static function findByProjectIDFullPermissions($projid) {
    $c = new Criteria();
    $c->add(self::ENTITY_ID, $projid);
    $c->add(self::ENTITY_TYPE_ID, DomainEntityType::ENTITY_TYPE_PROJECT);
    $c->add(self::PERMISSIONS, 'view,create,edit,delete,grant');
    return self::doSelect($c);
  }


  /**
   * @desc Find the array of authorizations given by ProjectID
   *
   * @param int $projid
   * @return Array of Authorization Objects
   */
  public static function findByProject($projid) {
    $c = new Criteria();
    $c->add(self::ENTITY_ID, $projid);
    $c->add(self::ENTITY_TYPE_ID, DomainEntityType::ENTITY_TYPE_PROJECT);
    return self::doSelect($c);
  }


  /**
   * @desc Find the only authorization given by ProjectID and PersonID
   *
   * @param int $person_id
   * @param int $projid
   * @return Authorization Object
   */
  public static function findByUIDProjectID($person_id, $projid) {
    return self::findByPersonIdEntityidEntitytype($person_id, $projid, DomainEntityType::ENTITY_TYPE_PROJECT);
  }


  /**
   * @desc Find the only authorization given by an Entity object and Person object
   *
   * @param Person $person
   * @param BaseObject $entity
   * @return Authorization Object
   */
  public static function findByUserEntity(Person $person, BaseObject $entity) {
    return self::findByPersonIdEntityidEntitytype($person->getId(), $entity->getId(), DomainEntityType::getEntityTypeId($entity));
  }


  /**
   * @desc Find the only authorization given by an EntityID, EntityTypeID and Person ID
   *
   * @param int $person_id
   * @param int $entity_id
   * @param int $entity_type_id
   * @return Authorization Object
   */
  public static function findByUIDEntity($person_id, $entity_id, $entity_type_id){
    return self::findByPersonIdEntityidEntitytype($person_id, $entity_id, $entity_type_id);
  }



  /**
   * @desc Count number of people that have fully authorized within an entity
   *
   * SELECT
   *        COUNT(DISTINCT person_id) AS n
   * FROM
   *        Authorization
   * WHERE
   *        entity_id=? AND
   *        entity_type_id=? AND
   *        permissions = 'view,create,edit,delete,grant'
   *
   * @param BaseObject $entity
   * @return int $count
   */
  public static function countFullyAuthorizedPeople(BaseObject $entity) {

    $c = new Criteria();
    $c->clearSelectColumns();
    $c->addSelectColumn(self::PERSON_ID);
    $c->add(self::ENTITY_ID, $entity->getId());
    $c->add(self::ENTITY_TYPE_ID, DomainEntityType::getEntityTypeId($entity));
    $c->add(self::PERMISSIONS, 'view,create,edit,delete,grant');

    return self::doCount($c);
  }


  public static function findLastPersonWithFullPermissions(BaseObject $entity) {

    $c = new Criteria();
    $c->clearSelectColumns();
    $c->addSelectColumn(self::PERSON_ID);
    $c->add(self::ENTITY_ID, $entity->getId());
    $c->add(self::ENTITY_TYPE_ID, DomainEntityType::getEntityTypeId($entity));
    $c->add(self::PERMISSIONS, 'view,create,edit,delete,grant');
    $c->setDistinct();

    if(self::doCount($c) != 1) return null;

    $rs = self::doSelectRS($c);

    if($rs->next()) {
      $person_id = $rs->get(1);
      return $person_id;
    }

    return null;
  }

  /**
   *
   * @param int $p_iPersonId
   * @param array $p_iEntityIdArray
   * @param int $p_iEntityTypeId
   * @return array
   */
  public static function findPersonMembership($p_iPersonId, $p_iEntityIdArray, $p_iEntityTypeId){
    $iReturnEntityIdArray = array();
    if(empty($p_iEntityIdArray)){
      return $iReturnEntityIdArray;
    }

    $strEntityIds = implode(",", $p_iEntityIdArray);

    $strQuery = "select distinct entity_id
                 from authorization a
                 where a.entity_id in ($strEntityIds)
                   and a.entity_type_id=?
                   and a.person_id=?";

    $oConnection = Propel::getConnection();
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iEntityTypeId);
    $oStatement->setInt(2, $p_iPersonId);
    $oResultSet = $oStatement->executeQuery(ResultSet::FETCHMODE_ASSOC);
    while ($oResultSet->next()) {
      $iEntityId = $oResultSet->getInt("ENTITY_ID");
      array_push($iReturnEntityIdArray, $iEntityId);
    }
    
    return $iReturnEntityIdArray;
  }


  const INSERT_EXPERIMENT_AUTHS_SQL = "INSERT INTO authorization (person_id, entity_type_id, entity_id, permissions) SELECT person_id, 3 AS entity_type_id, ? AS entity_id, permissions FROM authorization WHERE entity_id=? AND entity_type_id=1 AND person_id<>?";

  /**
   * @desc Insert all experiments authorization for all other members in this project, given by a ProjID, ExpID, current PersonID
   *
   *    INSERT INTO Authorization (person_id, entity_type_id, entity_id, permissions)
   *    SELECT
   *          person_id,
   *          3 AS entity_type_id,
   *          ? AS entity_id,
   *          permissions
   *    FROM
   *          Authorization
   *    WHERE
   *          entity_id=? AND
   *          entity_type_id=1 AND
   *          person_id<>?
   *
   * @param int $projid
   * @param int $expid
   * @param int $person_id
   * @return boolean true or false
   */
  public static function insertExperimentAuthsForAllProjectMembers($projid, $expid, $person_id) {
    try {
      $conn = Propel::getConnection(self::DATABASE_NAME);

      $stmt = $conn->prepareStatement(self::INSERT_EXPERIMENT_AUTHS_SQL);
      $stmt->setInt(1, $expid);
      $stmt->setInt(2, $projid);
      $stmt->setInt(3, $person_id);
      $stmt->executeUpdate();

      return true;
    }
    catch (Exeption $e) {
      return false;
    }
  }



  const INSERT_PROJECT_AUTHS_SQL = "INSERT INTO authorization (id, person_id, entity_type_id, entity_id, permissions) SELECT AUTHORIZATION_SEQ.NEXTVAL, person_id, 3 AS entity_type_id, ? AS entity_id, permissions FROM authorization WHERE entity_id = ? AND entity_type_id = 1 AND person_id = ?";


  /**
   * @desc Insert all experiments authorization for the current user, given by a ProjID, ExpID, current PersonID
   *
   * INSERT INTO
   *    authorization (person_id, entity_type_id, entity_id, permissions)
   * SELECT
   *    person_id,
   *    3 AS entity_type_id,
   *    [?1] AS entity_id,
   *    permissions
   * FROM
   *    authorization
   * WHERE
   *    entity_id = [2]
   *    AND entity_type_id = 1
   *    AND person_id = [3]
   *
   * @param int $projid
   * @param int $expid
   * @param int $person_id
   * @return boolean true or false
   */
  public static function insertProjectAuthsForAllExperiments($projid, $expid,$person_id) {

    try {
      $conn = Propel::getConnection(self::DATABASE_NAME);

      $stmt = $conn->prepareStatement(self::INSERT_PROJECT_AUTHS_SQL);
      $stmt->setInt(1, $expid);
      $stmt->setInt(2, $projid);
      $stmt->setInt(3, $person_id);
      $stmt->executeUpdate();

      return true;
    }
    catch (Exeption $e) {
      return false;
    }
  }


  /**
   * Checking if a person can do ("view"|"edit"|"delete"|"create"|"grant")
   * in any facility
   *
   * @param int $person_id
   * @param String $can_do
   * @return int count
   */
  public static function CanDoInAnyFacility($person_id, $can_do) {
    $c = new Criteria();

    $c->add(self::PERMISSIONS, '%' . $can_do . '%', Criteria::LIKE);
    $c->add(self::PERSON_ID, $person_id);
    $c->add(self::ENTITY_TYPE_ID, DomainEntityType::ENTITY_TYPE_FACILITY);

    return self::doCount($c) > 0;
  }


  /**
   * Get the list of Permissions from a list of all people in an entity
   *
   * @param BaseObject $entity
   * @return Array<Permission_String>
   */
  public static function listPermissionsForAllPeopleInEntity(BaseObject $entity ) {

    $entity_id = $entity->getId();
    $entity_type_id = DomainEntityType::getEntityTypeId($entity);

    $sub_query =
        "SELECT DISTINCT
            P.ID
        FROM
            PERSON P,
            PERSON_ENTITY_ROLE PER
        WHERE
            P.ID = PER.PERSON_ID AND
            PER.ENTITY_ID = ? AND
            PER.ENTITY_TYPE_ID = ?";

    $sql =
        "SELECT
            PERSON_ID,
            PERMISSIONS
        FROM
            AUTHORIZATION
        WHERE
            PERSON_ID IN ( " . $sub_query . ") AND
            ENTITY_ID = ? AND
            ENTITY_TYPE_ID = ?";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $stmt->setInt(1, $entity_id);
    $stmt->setInt(2, $entity_type_id);
    $stmt->setInt(3, $entity_id);
    $stmt->setInt(4, $entity_type_id);

    $rs = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);

    $permissions = array();

    while($rs->next()) {
      $permissions[$rs->get('PERSON_ID')] = $rs->get('PERMISSIONS');
    }
    return $permissions;
  }

  /**
   * Clone the Experiment Authorization to another
   *
   * @param Experiment $old_exp
   * @param Experiment $new_exp
   * @return boolean value, true if successed, false if failed
   */
  public static function cloneExperimentAuthorization(Experiment $old_exp, Experiment $new_exp){

    if(is_null($old_exp) || is_null($new_exp)) return false;

    $sql = "INSERT INTO
              AUTHORIZATION (ID, PERSON_ID, ENTITY_TYPE_ID, ENTITY_ID, PERMISSIONS)
            SELECT
              AUTHORIZATION_SEQ.NEXTVAL,
              PERSON_ID,
              ENTITY_TYPE_ID,
              ?,
              PERMISSIONS
            FROM
              AUTHORIZATION
            WHERE
              ENTITY_ID = ? AND
              ENTITY_TYPE_ID = 3 AND
              PERSON_ID IN (SELECT PERSON_ID FROM AUTHORIZATION WHERE ENTITY_ID = ? AND ENTITY_TYPE_ID = 1)";

    try {
      $conn = Propel::getConnection(self::DATABASE_NAME);

      $stmt = $conn->prepareStatement($sql);
      $stmt->setInt(1, $new_exp->getId());
      $stmt->setInt(2, $old_exp->getId());
      $stmt->setInt(3, $new_exp->getProjectId());
      $stmt->executeUpdate();

      return true;
    }
    catch (Exeption $e) {
      return false;
    }
  }

  /**
   * Clone the Experiment Authorization to another
   *
   * @param Experiment $old_exp
   * @param Experiment $new_exp
   * @return boolean value, true if successed, false if failed
   */
  public static function cloneProjectAuthorization(Project $old_proj, Project $new_proj, $members){

    if(is_null($old_proj) || is_null($new_proj)) return false;
    if(!is_array($members) || count($members) == 0) return false;

    $membersStr = implode("," , $members);

    $sql = "INSERT INTO
              AUTHORIZATION (ID, PERSON_ID, ENTITY_TYPE_ID, ENTITY_ID, PERMISSIONS)
            SELECT
              AUTHORIZATION_SEQ.NEXTVAL,
              PERSON_ID,
              ENTITY_TYPE_ID,
              ?,
              PERMISSIONS
            FROM
              AUTHORIZATION
            WHERE
              ENTITY_ID = ? AND
              ENTITY_TYPE_ID = 1 AND
              PERSON_ID IN ( " . $membersStr . " )";

    try {
      $conn = Propel::getConnection(self::DATABASE_NAME);

      $stmt = $conn->prepareStatement($sql);
      $stmt->set(1, $new_proj->getId());
      $stmt->set(2, $old_proj->getId());
      $stmt->executeUpdate();

      return true;
    }
    catch (Exeption $e) {
      return false;
    }
  }

} // AuthorizationPeer
?>
