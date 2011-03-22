<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once('lib/data/ProjectPeer.php');


class WarehouseModelFeatured extends WarehouseModelBase{

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  /**
   * 
   *
   */
  public function getFundingOrgs(){
  	return ProjectPeer::getFundingOrgs();
  }

  public function getEnhancedProjects(){
    return ProjectPeer::getEnhancedProjects();
  }
	
}

?>