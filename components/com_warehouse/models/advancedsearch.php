<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/FacilityPeer.php';


class WarehouseModelAdvancedSearch extends WarehouseModelBase{
	
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