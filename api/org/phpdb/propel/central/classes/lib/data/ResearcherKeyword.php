<?php

require_once 'lib/data/om/BaseResearcherKeyword.php';


/**
 * Skeleton subclass for representing a row from the 'RESEARCHER_KEYWORD' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ResearcherKeyword extends BaseResearcherKeyword {

  /**
   *
   * @param int $p_iEntityId
   * @param int $p_iEntityTypeId
   * @param string $p_strTerm
   * @param string $p_strDate
   * @param string $p_strCreatedByUsername
   */
  function  __construct($p_strTerm, $p_strDate, $p_strCreatedByUsername, $p_iEntityId=0, $p_iEntityTypeId=0) {
    $this->setEntityId($p_iEntityId);
    $this->setEntityTypeId($p_iEntityTypeId);
    $this->setKeywordTerm($p_strTerm);
    $this->setCreatedDate($p_strDate);
    $this->setCreatedBy($p_strCreatedByUsername);
  }

} // ResearcherKeyword
