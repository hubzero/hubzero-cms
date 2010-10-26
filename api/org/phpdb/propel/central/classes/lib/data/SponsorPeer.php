<?php

  // include base peer class
  require_once 'lib/data/om/BaseSponsorPeer.php';

  // include object class
  include_once 'lib/data/Sponsor.php';


/**
 * Skeleton subclass for performing query and update operations on the 'SPONSOR' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SponsorPeer extends BaseSponsorPeer {

  public static function suggestSponsors($p_strName, $p_iLimit){
    $p_strName = strtoupper($p_strName);
    $p_strName = "'$p_strName%'";

    $strQuery = "SELECT *
                 FROM (
                   SELECT SPONSOR.ID, row_number()
                   OVER (ORDER BY SPONSOR.DISPLAY_NAME) as rn
                   FROM SPONSOR
                   WHERE upper(SPONSOR.SYSTEM_NAME) like $p_strName
                      OR upper(SPONSOR.DISPLAY_NAME) like $p_strName
                 )
                 WHERE rn <= $p_iLimit";

    $oConnection = Propel::getConnection();
    $oStatement = $oConnection->createStatement();
    $oResultsSet = $oStatement->executeQuery($strQuery, ResultSet::FETCHMODE_ASSOC);

    $iSponsorIdArray = array();
    while($oResultsSet->next()){
      $iSponsorId = $oResultsSet->getInt('ID');
      array_push($iSponsorIdArray, $iSponsorId);
    }
    return self::retrieveByPKs($iSponsorIdArray);
  }

  /**
   * Find Sponsor that has a given name
   *
   * @param string $name
   * @return Sponsor
   */
  public static function findByName($name) {

    $c = new Criteria();
    $c->add(self::DISPLAY_NAME, $name);
    $c->setIgnoreCase(true);

    return self::doSelectOne($c);
  }

} // SponsorPeer
