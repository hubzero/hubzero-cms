<?php

// include base peer class
require_once 'lib/data/om/BaseAcknowledgementPeer.php';

// include object class
include_once 'lib/data/Acknowledgement.php';


/**
 * AcknowledgementPeer
 *
 * Peer class for Acknowledgement
 * Contains static methods to operate on the Acknowledgement table
 *
 *
 * @package    lib.data
 *
 */
class AcknowledgementPeer extends BaseAcknowledgementPeer {

  /**
   * Find an Acknowledgement object based on its ID
   *
   * @param int $id
   * @return Acknowledgement
   */
  public static function find($id) {
    return self::retrieveByPK($id);
  }

  /**
   * Find all acknowledgements
   *
   * @return array <Acknowledgement>
   */
  public static function findAll() {
    return self::doSelect(new Criteria());
  }


  /**
   * Find acknowledgements associated with a project,
   * including those for each experiment in the project
   *
   * @param int Project Id
   * @return array <Acknowledgement>
   */
  public static function findByProject($projid) {
    // Previously
    //  return new Finder($finderName, "SELECT * FROM Acknowledgement WHERE projid=? ORDER BY ackid DESC");

    $c = new Criteria();
    $c->add(self::PROJID, $projid);
    $c->addDescendingOrderByColumn(self::ACKID);
    return self::doSelect($c);

  }

  /**
   * Find acknowledgements associated with an Experiment.
   *
   * @param int Experiment Id
   * @return Acknowledgement
   */
  public static function findByExperiment($expid) {
    $c = new Criteria();
    $c->add(self::EXPID, $expid);
    $c->addDescendingOrderByColumn(self::ACKID);
    return self::doSelectOne($c);
  }

  /**
   * Find acknowledgements associated with a Trial
   * including those for each experiment in the project
   *
   * @param int Trial Id
   * @return array of Acknowledgements
   *
   * currently unused
   */
  public static function findByTrial($trialid) {
    // Previously:
    // return new Finder($finderName, "SELECT * FROM Acknowledgement WHERE trialid=? ORDER BY ackid DESC");

    $c = new Criteria();
    $c->add(self::TRIALID, $trialid);
    $c->addDescendingOrderByColumn(self::ACKID);
    return self::doSelect($c);
  }

  /**
   * Find Acknowledgement associated with a project
   * that were entered on the project page
   * including those for each experiment in the project
   *
   * @param int Project Id
   * @return Acknowledgement
   */
  public static function findByProjectOnly($projid) {
    // previously
    //return new Finder($finderName, "SELECT * FROM Acknowledgement WHERE projid=? and expid is null ORDER BY ackid DESC");

    $c = new Criteria();
    $c->add(self::PROJID, $projid);
    $c->add(self::EXPID, null, Criteria::ISNULL);
    $c->addDescendingOrderByColumn(self::ACKID);
    return self::doSelectOne($c);

  }


  /**
   * Clone the Experiment Acknowledgement to another
   *
   * @param Experiment $old_exp
   * @param Experiment $new_exp
   * @return boolean value, true if successed, false if failed
   */
  public static function cloneExperimentAcknowledgement(Experiment $old_exp, Experiment $new_exp){

    if(is_null($old_exp) || is_null($new_exp)) return false;

    $sql = "INSERT INTO
              Acknowledgement (ACKID, TRIALID, PROJID, EXPID, SPONSOR, HOW_TO_CITE)
            SELECT
              Acknowledgement_SEQ.NEXTVAL,
              TRIALID,
              ?,
              ?,
              SPONSOR,
              HOW_TO_CITE
            FROM
              Acknowledgement
            WHERE
              EXPID = ?";

    try {
      $conn = Propel::getConnection(self::DATABASE_NAME);

      $stmt = $conn->prepareStatement($sql);
      $stmt->setInt(1, $new_exp->getProjectId());
      $stmt->setInt(2, $new_exp->getId());
      $stmt->setInt(3, $old_exp->getId());
      $stmt->executeUpdate();

      return true;
    }
    catch (Exeption $e) {
      return false;
    }
  }

} // AcknowledgementPeer
?>
