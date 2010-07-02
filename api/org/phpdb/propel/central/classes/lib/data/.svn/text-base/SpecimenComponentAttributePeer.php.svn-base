<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenComponentAttributePeer.php';

  // include object class
  include_once 'lib/data/SpecimenComponentAttribute.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECIMEN_COMPONENT_ATTRIBUTE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentAttributePeer extends BaseSpecimenComponentAttributePeer {

  /**
   * Find a SpecimenComponentAttribute object based on its ID
   *
   * @param int $id
   * @return SpecimenComponentAttribute
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all SpecimenComponentAttributes
   *
   * @return array <SpecimenComponentAttribute>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::DISPLAY_ORDER);
    return self::doSelect($c);
  }


  /**
   * Find a list of attributes by a component
   *
   * @param int $specCompId
   * @return array <SpecimenComponentAttribute>
   */
  public static function findBySpecimenComponent($specCompId) {
    $c = new Criteria();
    $c->add(self::SPEC_COMP_ID, $specCompId);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }


  /**
   * Find one match by its ID and its SpecimenComponent
   *
   * @param int $attrId
   * @param int $specCompId
   * @return SpecimenComponentAttribute
   */
  public static function findOneByIdAndSpecimenComponent($attrId, $specCompId) {
    $c = new Criteria();
    $c->add(self::ID, $attrId);
    $c->add(self::SPEC_COMP_ID, $specCompId);

    return self::doSelectOne($c);
  }

} // SpecimenComponentAttributePeer
