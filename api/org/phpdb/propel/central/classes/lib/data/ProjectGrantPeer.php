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

} // ProjectGrantPeer
