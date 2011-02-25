<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('search.php');
require_once 'lib/data/FacilityPeer.php';


class WarehouseModelAdvancedSearch extends WarehouseModelSearch{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }

  public function getNeesFacilities(){
    return FacilityPeer::findAll();
  }
	
}

?>