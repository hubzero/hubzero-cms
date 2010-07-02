<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenComponentMaterialPropertyPeer.php';

  // include object class
  include_once 'lib/data/SpecimenComponentMaterialProperty.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECCOMP_MATERIAL_PROPERTY' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentMaterialPropertyPeer extends BaseSpecimenComponentMaterialPropertyPeer {
  /**
   * Find a SpecimenComponentMaterialProperty object based on its ID
   *
   * @param int $id
   * @return SpecimenComponentMaterialProperty
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all MaterialProperties
   *
   * @return array <MaterialProperty>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  /**
   * Find all MaterialProperty given by Material ID
   *
   * @param int $material_id
   * @return array <SpecimenComponentMaterialProperty>
   */
  public static function findByMaterial($material_id) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_MATERIAL_ID, $material_id);
    $c->addAscendingOrderByColumn(self::MATERIAL_TYPE_PROPERTY_ID);

    return self::doSelect($c);
  }


  /**
   * Find all Properties by Specimen Component
   * select * from material_property where material_id in (select id from Material where expid = 1118)
   *
   * @param int $expid
   * @return array <MaterialProperty>
   */
  public static function findBySpecimenComponent($specCompId) {
    $c = new Criteria();
    $c->addJoin(self::SPECIMEN_COMPONENT_MATERIAL_ID, SpecimenComponentMaterialPeer::ID);
    $c->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $specCompId);

    return self::doSelect($c);
  }



  /**
   * Find all MaterialProperty given by Material ID and MaterialTypeProperty ID
   *
   * @param int $material_id
   * @param int $materialTypeProperty_id
   * @return array <MaterialProperty>
   */
  public static function findByMaterialMaterialTypeProperty($material_id, $materialTypeProperty_id) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_MATERIAL_ID, $material_id);
    $c->add(self::MATERIAL_TYPE_PROPERTY_ID, $materialTypeProperty_id);
    $c->addAscendingOrderByColumn(self::MATERIAL_TYPE_PROPERTY_ID);

    return self::doSelect($c);
  }

} // SpecimenComponentMaterialPropertyPeer
