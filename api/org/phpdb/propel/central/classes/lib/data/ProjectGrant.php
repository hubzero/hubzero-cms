<?php

require_once 'lib/data/om/BaseProjectGrant.php';


/**
 * Skeleton subclass for representing a row from the 'PROJECT_GRANT' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ProjectGrant extends BaseProjectGrant {

  function __construct($p_strFundingOrg, $p_strAwardNumber, $p_strAwardUrl="", $p_oProject=null){
    $this->setFundingOrg($p_strFundingOrg);
    $this->setAwardNumber($p_strAwardNumber);
    $this->setAwardUrl($p_strAwardUrl);
    $this->setProject($p_oProject);
  }
  
} // ProjectGrant
