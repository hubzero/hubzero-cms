<?php

  // include base peer class
  require_once 'lib/security/om/BasePermissionsViewPeer.php';

  // include object class
  include_once 'lib/security/PermissionsView.php';


/**
 * Skeleton subclass for performing query and update operations on the 'AUTHORIZER' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.security
 */
class PermissionsViewPeer extends BasePermissionsViewPeer {

  /**
   * Find a PermissionsView object for a Person on an Entity
   *
   * @param int $pid
   * @param int $entityId
   * @param int $entityTypeId
   * @return PermissionsView
   */
  public static function findByPersonIdEntityIdEntityTypeId($pid, $entityId, $entityTypeId) {
    $c = new Criteria();
    $c->add(self::PERSON_ID, $pid);
    $c->add(self::ENTITY_TYPE_ID, $entityTypeId);
    $c->add(self::ENTITY_ID, $entityId);

    return self::doSelectOne($c);
  }


  /**
   * Check if a user has a SuperRole or not
   *
   * @param int $pid
   * @param String $action
   * @return boolean value
   */
  public static function haveSuperRole($pid, $action) {
    $c = new Criteria();
    $c->add(self::PERSON_ID, $pid);
    $c->add(self::IS_SUPER_ROLE, 1);

    if($action == "View")       $c->add(self::CAN_VIEW,   1);
    elseif($action == "Create") $c->add(self::CAN_CREATE, 1);
    elseif($action == "Edit")   $c->add(self::CAN_EDIT,   1);
    elseif($action == "Delete") $c->add(self::CAN_DELETE, 1);
    elseif($action == "Grant")  $c->add(self::CAN_GRANT,  1);
    else return false;

    return self::doCount($c) > 0;
  }


  /**
   * Check if a Person can have the permission on a specific entity
   *
   * @param int $pid: The user ID
   * @param int $entityId: The Entity ID (projid or expid)
   * @param int $entityTypeId: The type of entity (Project or Experiment)
   * @param String $action: the action to check: View, Edit, Delete, Create, Grant
   * @return boolean value
   */
  public static function canDo($pid, $entityId, $entityTypeId, $action) {
    $sql =
      "SELECT MAX(CAN_" . $action . ") AS CANDO FROM (
                SELECT CAN_" . $action . " FROM PERMISSION WHERE PERSON_ID = " . $pid . " AND ENTITY_TYPE_ID = " . $entityTypeId . " AND ENTITY_ID = " . $entityId . "
                UNION
                SELECT CAN_" . $action . " FROM PERMISSION WHERE PERSON_ID = " . $pid . " AND IS_SUPER_ROLE = 1)";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $rs = $stmt->executeQuery();

    if($rs->next()) {
      return $rs->get("CANDO") == 1 ;
    }
    return false;
  }
} // PermissionsViewPeer
