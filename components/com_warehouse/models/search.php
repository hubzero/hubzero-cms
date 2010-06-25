<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');


class WarehouseModelSearch extends WarehouseModelBase{
	
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
	
}

?>