<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenPeer.php';

  // include object class
  include_once 'lib/data/Specimen.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECIMEN' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenPeer extends BaseSpecimenPeer {

  /**
   * Find a Specimen object based on its ID
   *
   * @param int $id
   * @return Specimen
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all Specimens
   *
   * @return array <Specimen>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  /**
   * Find a unique Specimen for a project.
   *
   * @param int $projid
   * @return Specimen
   */
  public static function findByProject($projid) {

    $c = new Criteria();
    $c->add(self::PROJID, $projid);
    return self::doSelectOne($c);
  }
  
  /**
   * Find a unique Specimen for a project.
   *
   * @param int $projid
   * @return Specimen
   */
  public static function findByName($p_strName) {

    $c = new Criteria();
    $c->add(self::NAME, $p_strName);
    return self::doSelectOne($c);
  }


  /**
   * Find an array map for PROJID->SPECIMEN_ID.
   *
   * @return array(projid->specimenId)
   */
  public static function getProjectSpecimenMap() {
    $specimens = self::findAll();

    $map = array();
    foreach($specimens as $specimen) {
      $map[$specimen->getProjectId()] = $specimen->getId();
    }

    return $map;
  }


  /**
   * Suggest Specimen for a project.
   *
   * @param <String> $p_strName
   * @return Specimen
   */
  public static function suggestByName($p_strName) {

    $c = new Criteria();
    $c->add(self::NAME, $p_strName ."%", Criteria::LIKE);
    $c->setIgnoreCase(true);
    $c->addAscendingOrderByColumn(self::NAME);
    return self::doSelect($c);
  }
} // SpecimenPeer
