<?php

  // include base peer class
  require_once 'lib/data/curation/om/BaseNCCuratedNCIDCrossRefPeer.php';

  // include object class
  include_once 'lib/data/curation/NCCuratedNCIDCrossRef.php';


/**
 * Skeleton subclass for performing query and update operations on the 'CURATEDNCIDCROSS_REF' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data.curation
 */
class NCCuratedNCIDCrossRefPeer extends BaseNCCuratedNCIDCrossRefPeer {

  /**
   * Find a NCCuratedNCIDCrossRef object based on its ID
   *
   * @param int $id
   * @return NCCuratedNCIDCrossRef
   */
  function find($id) {
    return self::retrieveByPK($id);
  }


  /**
   * Find all NCCuratedNCIDCrossRef
   *
   * @return array <NCCuratedNCIDCrossRef>
   */
  function findAll() {
    $c = new Criteria();
    $c->addAscendingOrderByColumn(self::ID);
    return self::doSelect($c);

  }


  /**
   * Find One by ID
   *
   * @param int $id
   * @return NCCuratedNCIDCrossRef
   */
  function findById($id) {
    return $this->find($id);

  }


  /**
   * Find allNCCuratedNCIDCrossRef by Central Object ID and table Source
   *
   * @param int $NEEScentralObjectID
   * @param String $NEEScentralTableSource
   * @return array <NCCuratedNCIDCrossRef>
   */
  function findByCentralIdAndTableSource($NEEScentralObjectID, $NEEScentralTableSource) {
    $c = new Criteria();
    $c->add(self::NEESCENTRAL_OBJECTID, $NEEScentralObjectID);
    $c->add(self::NEESCENTRAL_TABLE_SOURCE, $NEEScentralTableSource);
    return self::doSelect($c);

  }

} // NCCuratedNCIDCrossRefPeer
