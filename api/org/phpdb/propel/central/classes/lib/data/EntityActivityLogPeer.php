<?php

  // include base peer class
  require_once 'lib/data/om/BaseEntityActivityLogPeer.php';

  // include object class
  include_once 'lib/data/EntityActivityLog.php';


/**
 * Skeleton subclass for performing query and update operations on the 'ENTITY_ACTIVITY_LOG' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class EntityActivityLogPeer extends BaseEntityActivityLogPeer {

    /**
     * Returns the current EntityActivityLog.
     * @param int $p_iEntityTypeId
     * @param int $p_iEntityId
     * @return EntityActivityLog
     */
    public static function getEntityActivityLog($p_iEntityTypeId, $p_iEntityId){
      $oCriteria = new Criteria();
      $oCriteria->add(self::ENTITY_TYPE_ID, $p_iEntityTypeId);
      $oCriteria->add(self::ENTITY_ID, $p_iEntityId);
      return self::doSelectOne($oCriteria);
    }

    public static function updateViews($p_iEntityTypeId, $p_iEntityId){
      $strQuery = "UPDATE ENTITY_ACTIVITY_LOG
                   SET ENTITY_ACTIVITY_LOG.VIEW_COUNT = ENTITY_ACTIVITY_LOG.VIEW_COUNT+1
                   WHERE ENTITY_ACTIVITY_LOG.ENTITY_TYPE_ID=?
                     AND ENTITY_ACTIVITY_LOG.ENTITY_ID=?
                  ";

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iEntityTypeId);
      $oStatement->setInt(2, $p_iEntityId);
      $oStatement->executeUpdate();
    }

    public static function updateDownloads($p_iEntityTypeId, $p_iEntityId){
      $strQuery = "UPDATE ENTITY_ACTIVITY_LOG
                   SET ENTITY_ACTIVITY_LOG.DOWNLOAD_COUNT = ENTITY_ACTIVITY_LOG.DOWNLOAD_COUNT+1
                   WHERE ENTITY_ACTIVITY_LOG.ENTITY_TYPE_ID=?
                     AND ENTITY_ACTIVITY_LOG.ENTITY_ID=?
                  ";

      $oConnection = Propel::getConnection();
      $oStatement = $oConnection->prepareStatement($strQuery);
      $oStatement->setInt(1, $p_iEntityTypeId);
      $oStatement->setInt(2, $p_iEntityId);
      $oStatement->executeUpdate();
    }



} // EntityActivityLogPeer
