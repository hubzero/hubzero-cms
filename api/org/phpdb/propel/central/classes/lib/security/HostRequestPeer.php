<?php
require_once 'lib/security/om/BaseHostRequestPeer.php';
include_once 'lib/security/HostRequest.php';

/**
 * HostRequestPeer
 *
 * Peer class for HostRequest
 * Contains static methods for manipulating the HostReq table
 *
 * @package    lib.security
 *
 */
class HostRequestPeer extends BaseHostRequestPeer {

  /**
   * Find a HostRequest object based on its ID
   *
   * @param int $id
   * @return HostRequest
   */
  public static function find($id, Connection $conn = null) {
    return self::retrieveByPK($id,$con);
  }


  /**
   * Find all HostRequests
   *
   * @param Criteria $c
   * @param Connection $conn
   * @return array<HostRequest>
   */
  public static function findAll(Criteria $c = null, Connection $conn = null) {
    return self::doSelect(is_null($c)?new Criteria():$c, $conn);
  }


} // HostRequestPeer
?>
