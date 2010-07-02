<?php

/** ****************************************************************************
 * @title
 *   Permissions Class
 *
 * @author
 *   Greg Crawford
 *
 * @abstract
 *   Class encapsulating various Role permissions.
 *
 * @description
 *   The Permissions class encapsulates the set of permissions used in the User based
 *   authorization scheme.
 *
 *   Current permissions are:  view, create, edit, delete, and grant.
 *
 *   Authorization and Role domain objects use the Permissions class.
 *
 ******************************************************************************/

class Permissions {

  const PERMISSION_NONE   = '';
  const PERMISSION_VIEW   = 'view';
  const PERMISSION_CREATE = 'create';
  const PERMISSION_EDIT   = 'edit';
  const PERMISSION_DELETE = 'delete';
  const PERMISSION_GRANT  = 'grant';

  const PERMISSION_ALL = 'view,create,edit,delete,grant';

  private $permissionStr;

  public function __construct($str = self::PERMISSION_NONE) {
    $this->permissionStr = $str;
  }


  /**
   * Rewrite the string permission so that it does not have duplicate permissions
   * and make sure it is in order view->create->edit->delete->grant
   *
   * @param String $permission_string
   * @return Permissions
   */
  public static function fromString($permission_string) {
    $perms = explode(",", $permission_string);

    $strArr = array();
    if(in_array('view',   $perms)) $strArr[] = self::PERMISSION_VIEW;
    if(in_array('create', $perms)) $strArr[] = self::PERMISSION_CREATE;
    if(in_array('edit',   $perms)) $strArr[] = self::PERMISSION_EDIT;
    if(in_array('delete', $perms)) $strArr[] = self::PERMISSION_DELETE;
    if(in_array('grant',  $perms)) $strArr[] = self::PERMISSION_GRANT;

    return new Permissions(implode(",", $strArr));
  }


/**
 * get the String Permission exactly as it get from database.
 *
 * @return String
 */

  public function toString() {
    $perms = self::getPermissionArray();

    $strArr = array();
    if(in_array('view',   $perms)) $strArr[] = self::PERMISSION_VIEW;
    if(in_array('create', $perms)) $strArr[] = self::PERMISSION_CREATE;
    if(in_array('edit',   $perms)) $strArr[] = self::PERMISSION_EDIT;
    if(in_array('delete', $perms)) $strArr[] = self::PERMISSION_DELETE;
    if(in_array('grant',  $perms)) $strArr[] = self::PERMISSION_GRANT;

    return implode(",", $strArr);
  }



/**
 * The isPermissionSet method tests to see if the indicated permission is set
 * and returns true if so, false if not.
 *
 * Note that the passed parameter is best passed utilizing a defined constant
 * For example to test if the grant permission is set use:
 *
 * isPermissionSet(Permissions::PERMISSION_GRANT)
 *
 *
 * @param int $permission
 * @return boolean
 */

  public function isPermissionSet($permission) {
    $perms = self::getPermissionArray();
    return in_array($permission, $perms);
  }


  public function getPermissionArray() {
    return explode(",", $this->permissionStr);
  }

/**
 * The clearPermission method clears the indicated permission.
 *
 * Note that the passed parameter is best passed utilizing a defined constant
 * For example to clear the edit permission use:
 *
 * clearPermission(Permissions::PERMISSION_EDIT);
 *
 * @param int $permission
 */

  public function clearPermission($permission)
  {
    $perms = self::getPermissionArray();
    $new_permissionStr = "";

    foreach($perms as $perm) {
      if($perm != $permission) {
        $new_permissionStr .= $perm . ',';
      }
    }
    $new_permissionStr =rtrim($new_permissionStr, ',');

    $this->permissionStr = $new_permissionStr;
  }

/**
 * The setPermission method sets the indicated permission.
 *
 * Note that the passed parameter is best passed utilizing a defined constant
 * For example to set the create permission use:
 *
 * setPermission(Permissions::PERMISSION_CREATE);
 *
 * @param String $permission (Permissions::PERMISSION_VIEW ...)
 */

  public function setPermission($permission) {
    $perms = self::getPermissionArray();

    if (! in_array($permission, $perms)) {
      $this->permissionStr .= ',' . $permission;
    }
  }

/**
 * The setBitMap method sets the value for the entire permissions bit map.
 * Using this method will completely overwrite any existing permissions.
 *
 * Note that the passed parameter is best passed utilizing defined constants
 * For example to set the bit map with edit, create, and grant permissions use:
 *
 * setBitMap(Permissions::PERMISSION_EDIT | Permissions::PERMISSION_CREATE | Permissions::PERMISSION_GRANT)
 *
 * @param int $bitMap
 */

  public function setPermissionStr($str) {
    $this->permissionStr = $str;
  }

}

?>