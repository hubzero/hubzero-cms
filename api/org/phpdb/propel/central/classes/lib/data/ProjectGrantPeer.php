<?php

  // include base peer class
  require_once 'lib/data/om/BaseProjectGrantPeer.php';

  // include object class
  include_once 'lib/data/ProjectGrant.php';


/**
 * Skeleton subclass for performing query and update operations on the 'PROJECT_GRANT' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ProjectGrantPeer extends BaseProjectGrantPeer {

  public static function findByProjectId($p_iProjectId){
    $oCriteria = new Criteria();
    $oCriteria->add(self::PROJID, $p_iProjectId);
    return self::doSelect($oCriteria);
  }

  public static function deleteByProject($p_iProjectId, $p_oConnection=null){
    $strQuery = "delete from project_grant
                 where projid=?";

    if(!$p_oConnection){
      $oConnection = Propel::getConnection();
    }else{
      $oConnection = $p_oConnection;
    }
    
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
    $oStatement->executeUpdate();
  }
  

} // ProjectGrantPeer
