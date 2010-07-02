<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenComponentExperimentPeer.php';

  // include object class
  include_once 'lib/data/SpecimenComponentExperiment.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECCOMP_EXPERIMENT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentExperimentPeer extends BaseSpecimenComponentExperimentPeer {
  /**
   * Find a SpecimenComponentExperiment object based on its ID
   *
   * @param int $id
   * @return SpecimenComponentExperiment
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all SpecimenComponentExperiments
   *
   * @return array <SpecimenComponentExperiment>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  /**
   * Find all SpecimenComponentExperiments that link to a specific specimenComponent
   *
   * @param int $specimenComponentId
   * @return array <SpecimenComponentExperiment>
   */
  public static function findBySpecimenComponent($specimenComponentId) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_ID, $specimenComponentId);

    return self::doSelect($c);
  }


  /**
   * Find all SpecimenComponentExperiments that link to an experiment
   *
   * @param int $expid
   * @return array <SpecimenComponentExperiment>
   */
  public static function findByExperiment($expid) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);

    return self::doSelect($c);
  }


  /**
   * Find one SpecimenComponentExperiment that link to an experiment and a specimenComponent
   *
   * @param int $specimenComponentId
   * @param int $expid
   * @return SpecimenComponentExperiment
   */
  public static function findOneByComponentAndExperiment($specimenComponentId, $expid) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_ID, $specimenComponentId);
    $c->add(self::EXPID, $expid);

    return self::doSelectOne($c);
  }




} // SpecimenComponentExperimentPeer
