<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class ProjectEditorModelEditRole extends ProjectEditorModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  /**
   * Get the Authorization for a person given the entity and its type.
   * @param int $p_iPersonId
   * @param int $p_iEntityId
   * @param int $p_iEntityTypeId
   * @return Authorization
   */
  public function findByPersonIdEntityidEntitytype($p_iPersonId, $p_iEntityId, $p_iEntityTypeId) {
    return AuthorizationPeer::findByPersonIdEntityidEntitytype($p_iPersonId, $p_iEntityId, $p_iEntityTypeId);
  }

  /**
   * Get the Role using an identifier.
   * @param int $p_iRoleId
   * @return Role
   */
  public function findRoleById($p_iRoleId){
    return RolePeer::find($p_iRoleId);
  }

  /**
   *
   * @param int $personId
   * @param int $entityId
   * @param int $entityTypeId
   * @return array <Role>
   */
  public function getRolesByPersonEntity($personId, $entityId, $entityTypeId) {
    return RolePeer::getRolesByPersonEntity($personId, $entityId, $entityTypeId);
  }
}

?>