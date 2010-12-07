<?php

require_once 'lib/data/curation/om/BaseNCCuratedObjects.php';


/**
 * Skeleton subclass for representing a row from the 'CURATED_OBJECTS' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data.curation
 */
class NCCuratedObjects extends BaseNCCuratedObjects {

  /*
  public function __construct($p_strObjectType, $p_strTitle,
                              $p_strShortTitle, $p_strDescription,
                              $p_strObjectCreationDate, $p_strCurationState,
                              $p_strAccess, $p_strObjectStatus,
                              $p_strConformanceLevel, $p_strCreatedBy,
                              $p_strCreatedDate, $p_strModifiedBy,
                              $p_strModifiedDate){
    $this->setObjectType($p_strObjectType);
    $this->setTitle($p_strTitle);
    $this->setShortTitle($p_strShortTitle);
    $this->setDescription($p_strDescription);
    $this->setObjectCreationDate($p_strObjectCreationDate);
    $this->setInitialCurationDate($p_strObjectCreationDate);
    $this->setCurationState($p_strCurationState);
    $this->setObjectVisibility($p_strAccess);
    $this->setObjectStatus($p_strObjectStatus);
    $this->setConformanceLevel($p_strConformanceLevel);
    $this->setCreatedBy($p_strCreatedBy);
    $this->setCreatedDate($p_strCreatedDate);
    $this->setModifiedBy($p_strModifiedBy);
    $this->setModifiedDate($p_strModifiedDate);
  }
  */

  // Kevin, why didn't you set getId instead of getObjectId !!! It should be matched with all Object class !!!
  public function getId() {
    return $this->getObjectId();
  }
} // NCCuratedObjects
