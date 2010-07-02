<?php

  // include base peer class
  require_once 'lib/data/curation/om/BaseNCCuratedObjectsPeer.php';

  // include object class
  include_once 'lib/data/curation/NCCuratedObjects.php';


/**
 * Skeleton subclass for performing query and update operations on the 'CURATED_OBJECTS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data.curation
 */
class NCCuratedObjectsPeer extends BaseNCCuratedObjectsPeer {

  /**
   * Check if a Project is curated or not
   * @param String $projectName
   * @return boolean
   */
  public static function isCompleteCuratedProject($projectName) {

    $c = new Criteria();
    $c->add(self::NAME, $projectName);
    $c->add(self::OBJECT_TYPE, "Project");
    $c->add(self::CURATION_STATE, "Complete");
    return self::doCount($c) > 0;
  }

  /**
   * Find the CurateObject of type Project by project Name
   *
   * @param String $projectName
   * @return NCCuratedObjects
   */
  public static function findByProjectName($projectName) {

    $c = new Criteria();
    $c->add(self::NAME, $projectName);
    $c->add(self::OBJECT_TYPE, "Project");
    return self::doSelectOne($c);
  }


  /**
   * Find the CurateObject of type Project by project ID
   *
   * @param int $projid
   * @return NCCuratedObjects
   */
  public static function findByProjectId($projid) {

    $c = new Criteria();
    $c->add(self::LINK, "/Project/" . $projid);
    $c->add(self::OBJECT_TYPE, "Project");
    return self::doSelectOne($c);
  }

  /**
   * Find the CurateObject of type Experiment by $expid
   *
   * @param int $expid
   * @return NCCuratedObjects
   */
  public static function findByExperimentId($expid) {

    $c = new Criteria();
    $c->add(self::LINK, "/Project/%/Experiment/" . $expid, Criteria::LIKE);
    $c->add(self::OBJECT_TYPE, "Experiment");

    return self::doSelectOne($c);
  }


  /**
   * Get the array map with the projectId of the complete curated projects
   *
   * @return array $map
   */
  public static function getCuratedProjectsMap() {
    $c = new Criteria();
    $c->add(self::OBJECT_TYPE, "Project");
    $c->add(self::CURATION_STATE, "Complete");
    $objects = self::doSelect($c);

    $map = array();

    foreach($objects as $obj) {
      $projid = str_replace("/Project/", "", $obj->getLink());
      $map[] = (int)($projid);
    }

    return $map;
  }


  /**
   * Get a list of projids or expids that in curation mode ("Complete", "Incomplete", "Current")
   *
   * @param String $object_type ("Project" or "Experiment")
   * @param String $state ("Complete" or "Incomplete" or "Current")
   * @return array <ids>
   */
  public static function getCuratedObjectsByStatusMap($object_type, $state) {

    $map = array();
    if($object_type != "Project" && $object_type != "Experiment") return $map;

    $c = new Criteria();
    $c->add(self::OBJECT_TYPE, $object_type);
    $c->add(self::CURATION_STATE, $state);
    $objects = self::doSelect($c);

    if($object_type == "Project") {
      foreach($objects as $obj) {
        $pname = $obj->getName();
        $tokens = explode("-", $pname);

        if(isset($tokens[2])) {
          $projid = (int)($tokens[2]);
          $map[] = (int)($projid);
        }
      }
    }
    elseif($object_type == "Experiment") {
      foreach($objects as $obj) {
        $arr = explode("/", $obj->getLink());

        if(count($arr) == 5) {
          $map[] = (int)($arr[4]);
        }
      }
    }
    return $map;
  }



  /**
   * Get the array map with the experiment Id of the complete curated experiments
   *
   * @return array $map
   */
  public static function getCuratedExperimentsMap() {
    $c = new Criteria();
    $c->add(self::OBJECT_TYPE, "Experiment");
    $c->add(self::CURATION_STATE, "Complete");
    $objects = self::doSelect($c);

    $map = array();

    foreach($objects as $obj) {
      $arr = explode("/", $obj->getLink());

      if(count($arr) == 5) {
        $map[] = (int)($arr[4]);
      }
    }

    return $map;
  }



} // NCCuratedObjectsPeer
