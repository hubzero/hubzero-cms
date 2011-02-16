<?php

  // include base peer class
  require_once 'lib/data/om/BaseProjectHomepagePeer.php';

  // include object class
  include_once 'lib/data/ProjectHomepage.php';

  /*
   * Added lookup peer because BaseProjectHomepagePeer didn't include and complained.
   * Adding include to Base*Peer could lead to it getting overwritten by another propel-gen.
   */
  include_once 'lib/data/ProjectHomepageTypeLookupPeer.php';  


/**
 * Skeleton subclass for performing query and update operations on the 'PROJECT_HOMEPAGE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ProjectHomepagePeer extends BaseProjectHomepagePeer {

  /**
   * Find a ProjectHomepage object based on its ID
   *
   * @param int $id
   * @return ProjectHomepage
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all ProjectHomepages
   *
   * @return array <ProjectHomepage>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }

  /**
   * Find all ProjectHomepages by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findByProjectId($projid) {
    $c = new Criteria();
    $c->add(self::PROJECT_ID, $projid);
    $c->addAscendingOrderByColumn(self::PROJECT_HOMEPAGE_TYPE_ID);
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }

  /**
   * Find all ProjectHomepages by a project id
   *
   * @param int $projid
   * @param int $p_iDataFileId
   * @return ProjectHomepage
   */
  public static function findByProjectIdAndDataFileId($projid, $p_iDataFileId) {
    $c = new Criteria();
    $c->addJoin(self::DATA_FILE_ID, DataFilePeer::ID);
    $c->add(self::PROJECT_ID, $projid);
    $c->add(self::DATA_FILE_ID, $p_iDataFileId);
    return self::doSelectOne($c);
  }


  /**
   * Find all ProjectHomepages by a project id
   *
   * @param int $projid
   * @param int $projHomepageTypeid
   * @return array <ProjectHomepage>
   */
  public static function findByProjectIdAndFileTypeId($projid, $typeId) {
    $c = new Criteria();
    $c->addJoin(self::DATA_FILE_ID, DataFilePeer::ID);
    $c->add(self::PROJECT_ID, $projid);
    $c->add(self::PROJECT_HOMEPAGE_TYPE_ID, $typeId);
    $c->addAscendingOrderByColumn(self::ID);

    return self::doSelectJoinAll($c);
  }


  /**
   * Find all ProjectHomepage Images by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findProjectImagesByProjectId($projid) {
    return self::findByProjectIdAndFileTypeId($projid, self::CLASSKEY_PROJECTHOMEPAGEIMAGE);
  }

  /**
   * Find all ProjectHomepage Videos by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findProjectVideosByProjectId($projid) {
    return self::findByProjectIdAndFileTypeId($projid, self::CLASSKEY_PROJECTHOMEPAGEVIDEO);
  }

  /**
   * Find all ProjectHomepage Docs by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findProjectDocsByProjectId($projid) {
    return self::findByProjectIdAndFileTypeId($projid, self::CLASSKEY_PROJECTHOMEPAGEDOC);
  }

  /**
   * Find all ProjectHomepage URLs by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findProjectURLsByProjectId($projid) {
    $c = new Criteria();
    $c->add(self::PROJECT_ID, $projid);
    $c->add(self::PROJECT_HOMEPAGE_TYPE_ID, self::CLASSKEY_PROJECTHOMEPAGEURL);
    return self::doSelect($c);
  }

  /**
   * Find all ProjectHomepage Publications by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findProjectPublicationsByProjectId($projid) {
    $c = new Criteria();
    $c->add(self::PROJECT_ID, $projid);
    $c->add(self::PROJECT_HOMEPAGE_TYPE_ID, self::CLASSKEY_PROJECTHOMEPAGEPUB);
    return self::doSelect($c);
  }
  
  /**
   * Find all ProjectHomepage URLs by a project id
   *
   * @param int $projid
   * @return array <ProjectHomepage>
   */
  public static function findProjectURLs($projid) {
    $c = new Criteria();
    $c->add(self::PROJECT_ID, $projid);
    return self::doSelect($c);
  }

  public static function deleteByProject($p_iProjectId, $p_oConnection=null){
    $strQuery = "delete from project_homepage
                 where project_id=?";

    if(!$p_oConnection){
      $oConnection = Propel::getConnection();
    }else{
      $oConnection = $p_oConnection;
    }
    
    $oStatement = $oConnection->prepareStatement($strQuery);
    $oStatement->setInt(1, $p_iProjectId);
    $oStatement->executeUpdate();
  }

} // ProjectHomepagePeer
