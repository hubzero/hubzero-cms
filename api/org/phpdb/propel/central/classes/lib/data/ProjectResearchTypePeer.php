<?php

  // include base peer class
  require_once 'lib/data/om/BaseProjectResearchTypePeer.php';

  // include object class
  include_once 'lib/data/ProjectResearchType.php';


/**
 * Skeleton subclass for performing query and update operations on the 'PROJECT_RESEARCH_TYPE' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ProjectResearchTypePeer extends BaseProjectResearchTypePeer {

  /**
   * Find a ProjectResearchType object based on its ID
   *
   * @param int $id
   * @return ProjectResearchType
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all ProjectResearchType
   *
   * @return array <ProjectResearchType>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

} // ProjectResearchTypePeer
