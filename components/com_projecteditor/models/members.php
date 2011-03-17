<?php

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/PersonPeer.php';
require_once 'lib/data/Person.php';

class ProjectEditorModelMembers extends ProjectEditorModelBase{

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  /**
   *
   * @param string $p_strSearchTerm
   * @param int $p_iCount
   * @return array
   */
  public function suggestMembers($p_strSearchTerm, $p_iLimit){
    return PersonPeer::suggestMembers($p_strSearchTerm, $p_iLimit);
  }

  /**
   *
   * @param string $p_strSearchTerm
   * @param int $p_iCount
   * @return array
   */
  public function suggestMembersCount($p_strSearchTerm){
    return PersonPeer::suggestMembersCount($p_strSearchTerm);
  }

  /**
   *
   *
   */
  public function getMembersByProjectId($p_iProjectId){
    return PersonPeer::findMembersForEntity($p_iProjectId, 1);
  }

  public function findMembersForEntityWithPagination($p_iProjectId, $p_iEntityId=1, $p_iLowerLimit=0, $p_iUpperLimit=25){
    return PersonPeer::findMembersForEntityWithPagination($p_iProjectId, $p_iEntityId, $p_iLowerLimit, $p_iUpperLimit);
  }

  public function findMembersForEntityCount($p_iProjectId){
    return PersonPeer::findMembersForEntityCount($p_iProjectId, 1);
  }
}

?>