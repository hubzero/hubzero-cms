<?php

  // include base peer class
  require_once 'lib/data/om/BaseUserReqPeer.php';

  // include object class
  include_once 'lib/data/UserReq.php';


/**
 * Skeleton subclass for performing query and update operations on the 'USER_REQ' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class UserReqPeer extends BaseUserReqPeer {

  /**
   * Find an UserReq object based on its ID
   *
   * @param int $id
   * @return UserReq
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all UserReq in order
   *
   * @return array <UserReq>
   */
  public static function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);
  }


  public static function getEncryptPass($pass, $hexstring) {
    $sql = "select aes_encrypt(?, ?) as PASS from dual";

    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $stmt->setString(1, $pass);
    $stmt->setString(2, $hexstring);
    $rs = $stmt->executeQuery();
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    if($rs->next()) {
      return $rs->get('PASS');
    }
    return null;
  }



  public static function getDecryptPass($pass, $hexstring) {
    $sql = "select aes_decrypt(?, ?) as PASS from dual";
    $conn = Propel::getConnection();
    $stmt = $conn->prepareStatement($sql);
    $stmt->setString(1, $pass);
    $stmt->setString(2, $hexstring);
    $rs = $stmt->executeQuery();
    $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    if($rs->next()) {
      return $rs->get('PASS');
    }
    return null;
  }

} // UserReqPeer
