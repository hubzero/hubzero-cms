<?php

// include base peer class
require_once 'lib/data/om/BaseCoordinateSpaceDataFilePeer.php';

// include object class
include_once 'lib/data/CoordinateSpaceDataFile.php';

require_once 'lib/data/CoordinateSpace.php';

/**
 * CoordinateSpaceDataFilePeer
 *
 * peer class for CoordinateSpaceDataFile: contains static methods
 * for managing ehte CoordinateSpaceDataFile table
 *
 * @package    lib.data
 */
class CoordinateSpaceDataFilePeer extends BaseCoordinateSpaceDataFilePeer {

  /**
   * Find a CoordinateSpaceDataFile object based on its ID
   *
   * @param int $id
   * @return CoordinateSpaceDataFile
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * find all CoordinateSpaceDataFiles
   *
   * @return array<CoordinateSpaceDataFile>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find CoordinateSpaceDataFiles for given CoordinateSpace
   *
   * @param int $coordinateSpaceId
   * @return array<CoordinateSpaceDataFile>
   *
   * was:return new Finder($finderName, "SELECT * FROM {$this->getTable()} WHERE coordinateSpaceId=?");
   */
  public static function findByCoordinateSpace($coordinateSpaceId) {
    $c = new Criteria();
    $c->add(self::COORDINATE_SPACE_ID, $coordinateSpaceId);
    return self::doSelect($c);
  }


  /**
   * Find CoordinateSpaceDataFiles by a given Experiment
   *
   * @param int $expid
   * @return array <CoordinateSpaceDataFile>
   */
  public static function findByExperiment($expid) {


    $c = new Criteria();
    $c->addJoin(self::COORDINATE_SPACE_ID, CoordinateSpacePeer::ID);
    $c->add(CoordinateSpacePeer::EXPID, $expid);

    return self::doSelect($c);
  }



} // CoordinateSpaceDataFilePeer
?>
