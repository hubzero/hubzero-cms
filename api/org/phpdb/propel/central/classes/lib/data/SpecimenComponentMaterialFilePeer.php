<?php

  // include base peer class
  require_once 'lib/data/om/BaseSpecimenComponentMaterialFilePeer.php';

  // include object class
  include_once 'lib/data/SpecimenComponentMaterialFile.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPECCOMP_MATERIAL_DATA_FILE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SpecimenComponentMaterialFilePeer extends BaseSpecimenComponentMaterialFilePeer {
  /**
   * Find a SpecimenComponentMaterialFile object based on its ID
   *
   * @param int $id
   * @return SpecimenComponentMaterialFile
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find a SpecimenComponentMaterialFile object based on its ID
   *
   * @param int $id
   * @param int $materialId
   * @return SpecimenComponentMaterialFile
   */
  public static function findOneByIdAndMaterial($id, $materialId) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_MATERIAL_ID, $materialId);
    $c->add(self::ID, $id);

    return self::doSelectOne($c);
  }


  /**
   * Find all SpecimenComponentMaterialFiles
   *
   * @return array <SpecimenComponentMaterialFile>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  /**
   * File all SpecimenComponentMaterialFiles that link to a material
   *
   * @param int $materialId
   * @return array <SpecimenComponentMaterialFile>
   */
  public static function findByMaterial($materialId) {
    $c = new Criteria();
    $c->add(self::SPECIMEN_COMPONENT_MATERIAL_ID, $materialId);

    return self::doSelect($c);
  }


  /**
   * Find SpecimenComponentMaterialFiles by a given SpecimenComponent
   *
   * @param int $specCompId
   * @return array <SpecimenComponentMaterialFile>
   */
  public static function findBySpecimenComponent($specCompId) {

    $c = new Criteria();
    $c->addJoin(self::SPECIMEN_COMPONENT_MATERIAL_ID, SpecimenComponentMaterialPeer::ID);
    $c->add(SpecimenComponentMaterialPeer::SPECIMEN_COMPONENT_ID, $specCompId);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelect($c);
  }
} // SpecimenComponentMaterialFilePeer
