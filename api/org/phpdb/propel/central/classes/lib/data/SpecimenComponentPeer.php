<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenComponentPeer.php';

  // include object class
  include_once 'lib/data/SpecimenComponent.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECIMEN_COMPONENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentPeer extends BaseSpecimenComponentPeer {

  /**
   * Find a SpecimenComponent object based on its ID
   *
   * @param int $id
   * @return SpecimenComponent
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all SpecimenComponents
   *
   * @return array <SpecimenComponent>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }



  /**
   * File a list of SpecimenComponent by SpecimenId
   *
   * @param int $specimenId
   * @return array <SpecimenComponent>
   */
  public static function findBySpecimen($specimenId) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_ID, $specimenId);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

  /**
   * Get a list of SpecimenComponents within Project
   *
   * @param int $projid
   * @return array <SpecimenComponent>
   */
  public static function findByProject($projid) {
    $c = new Criteria();
    $c->addJoin(SpecimenPeer::ID, self::SPECIMEN_ID);
    $c->add(SpecimenPeer::PROJID, $projid);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

  /**
   * Get a list of SpecimenComponents within Experiment
   *
   * @param int $expid
   * @return array <SpecimenComponent>
   */
  public static function findByExperiment($expid) {
    include_once 'lib/data/SpecimenComponentExperiment.php';
    include_once 'lib/data/SpecimenComponent.php';
    include_once 'lib/data/Specimen.php';
    include_once 'lib/data/Experiment.php';
    include_once 'lib/data/Project.php';

    $c = new Criteria();
    $c->addJoin(ProjectPeer::PROJID, ExperimentPeer::PROJID);
    $c->addJoin(ExperimentPeer::EXPID, SpecimenComponentExperimentPeer::EXPID);
    $c->addJoin(SpecimenComponentExperimentPeer::SPECIMEN_COMPONENT_ID, self::ID);
    $c->addJoin(self::SPECIMEN_ID, SpecimenPeer::ID);

    $c->add(SpecimenComponentExperimentPeer::EXPID, $expid);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }

  /**
   * Get a list of SpecimenComponents within Experiment
   *
   * @param int $expid
   * @return array <SpecimenComponent>
   */
  public static function findByProjectNotExperiment($projid, $expid) {

    $c = new Criteria();
    $c->add(self::ID, self::ID . " IN (
              SELECT DISTINCT SC.ID
              FROM SPECIMEN_COMPONENT SC, SPECIMEN S
              WHERE SC.SPECIMEN_ID = S.ID AND S.PROJID = $projid)
              AND " . self::ID . " NOT IN (
              SELECT DISTINCT SC.ID
              FROM SPECIMEN_COMPONENT SC, SPECIMEN S, SPECCOMP_EXPERIMENT SCE, EXPERIMENT E, PROJECT P
              WHERE P.PROJID = E.PROJID AND E.EXPID = SCE.EXPID AND SCE.SPECIMEN_COMPONENT_ID = SC.ID AND SC.SPECIMEN_ID = S.ID AND SCE.EXPID = $expid)"
        , Criteria::CUSTOM);

    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }


  /**
   * File a specific SpecimenComponent by SpecimenId and its Id
   *
   * @param int $specimenId
   * @param int $specCompId
   * @return SpecimenComponent
   */
  public static function findOneByIdAndSpecimen($specimenId, $specCompId) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_ID, $specimenId);
    $c->add(self::ID, $specCompId);

    return self::doSelectOne($c);
  }

  /**
   * get a list of sub-components by a parentId
   *
   * @param int $specCompId
   * @return array <SpecimenComponent>
   */
  public static function findSubComponents($specCompId) {
    $c = new Criteria();
    $c->add(self::PARENT_SPEC_COMP_ID, $specCompId);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }


  /**
   * Get number of sub-component by a parentId
   *
   * @param int $specCompId
   * @return int
   */
  public static function countSubComponent($specCompId) {
    $c = new Criteria();
    $c->add(self::PARENT_SPEC_COMP_ID, $specCompId);

    return self::doCount($c);
  }


  /**
   * Get a list of SpecimenComponent that belong to a Specimen and
   * could be used as a list of parent candidates
   *
   * @param int $specimenId
   * @param SpecimenComponent $specComp
   * @return array <$id>
   */
  public static function getAncestorList( $specimenId, $specComp=null) {

    $specCompId = $specComp ? $specComp->getId() : -1;

    $childrenList = self::getChildrenList($specComp);
    $allComponents = self::findBySpecimen($specimenId);

    $subCompIds = array();

    foreach($childrenList as $child) {
      $subCompIds[] = $child->getId();
    }

    $ret = array();
    foreach($allComponents as $acomp) {
      if(($acomp->getId() != $specCompId) && !in_array($acomp->getId(), $subCompIds)) {
        $ret[$acomp->getId()] = $acomp->getName();
      }
    }

    return $ret;
  }


  /**
   * Get a list of SpecimenComponents that are sub-compoment deep level
   *
   * @param SpecimenComponent $specComp
   * @param array $list
   * @return array $list
   */
  public static function getChildrenList( $specComp="", $list="") {

    if(empty($list)) $list = array();

    if(is_null($specComp)) return $list;

    $specCompId = $specComp->getId();

    $children = self::findSubComponents($specCompId);

    foreach ($children as $child) {
      $list[] = $child;
      $list = self::getChildrenList($child, $list);
    }

    return $list;
  }


  function mapId(SpecimenComponent $specComp) {
      return $specComp->getId();
  }

} // SpecimenComponentPeer
