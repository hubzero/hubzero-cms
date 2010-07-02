<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenComponentMaterialPeer.php';

  // include object class
  include_once 'lib/data/SpecimenComponentMaterial.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECIMEN_COMPONENT_MATERIAL' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentMaterialPeer extends BaseSpecimenComponentMaterialPeer {

  /**
   * Find a SpecimenComponentMaterial object based on its ID
   *
   * @param int $id
   * @return SpecimenComponentMaterial
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all SpecimenComponentMaterials
   *
   * @return array <SpecimenComponentMaterial>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }



  /**
   * Find all SpecimenComponentMaterials by its SpecimenComponent
   * @param $specCompId
   *
   * @return array <SpecimenComponentMaterial>
   */
  public static function findByComponent($specCompId) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_ID, $specCompId);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }



  /**
   * Find a specific SpecimenComponentMaterial by its Id and SpecimenComponentId
   * @param $id
   * @param $specCompId
   *
   * @return SpecimenComponentMaterial
   */
  public static function findOneByIdAndSpecimenComponent($id, $specCompId) {
    $c = new Criteria();
    $c->add(self::ID, $id);
    $c->add(self::SPECIMEN_COMPONENT_ID, $specCompId);

    return self::doSelectOne($c);
  }


  /**
   * Find all SpecimenComponentMaterials that not belongs to any Experiment (Library Materials) and given by Type
   *
   * @param Object $type
   * @return array <SpecimenComponentMaterial>
   */
  public static function getLibraryMaterials($type = null) {
    if( $type ) {
      // Figure out whether they passed in a MaterialType object, or a string.
      $string = $type;
      if( get_class($type) ) {
        $string = $type->getSystemName();
      }
      return self::findAllLibraryMaterialsByType($string);
    } else {
      return self::findAllLibraryMaterials();
    }
  }




  /**
   * Find all SpecimenComponentMaterials that not belongs to any Experiment (Library Materials)
   *
   * @return array <SpecimenComponentMaterial>
   */
  public static function findAllLibraryMaterials() {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_ID, null, Criteria::ISNULL);

    return self::doSelect($c);
  }



  /**
   * Find all Materials that not belongs to any Experiment (Library Materials) and given by System Name
   *
   * @param String $system_name
   * @return array <Material>
   */
  public static function findAllLibraryMaterialsByType($system_name) {

    include_once 'lib/data/MaterialType.php';

    $c = new Criteria();
    $c->addJoin(self::MATERIAL_TYPE_ID, MaterialTypePeer::ID, Criteria::INNER_JOIN);
    $c->add(self::SPECIMEN_COMPONENT_ID, null, Criteria::ISNULL);
    $c->add(MaterialTypePeer::SYSTEM_NAME, $system_name);

    return self::doSelect($c);
  }


} // SpecimenComponentMaterialPeer
