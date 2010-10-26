<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class WarehouseModelMembers extends WarehouseModelBase{
	
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

  public function createThumb($p_strPhoto){
    $image = explode('.',$p_strPhoto);
    $n = count($image);
    $image[$n-2] .= '_thumb';
    $end = array_pop($image);
    $image[] = $end;
    return implode('.',$image);
  }

  public function formatId($someid){
    while (strlen($someid) < 5) {
      $someid = 0 . "$someid";
    }
    return $someid;
  }
}

?>