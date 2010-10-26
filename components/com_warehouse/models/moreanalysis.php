<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('moredocs.php');
require_once 'lib/data/DataFilePeer.php';

class WarehouseModelMoreAnalysis extends WarehouseModelMoreDocs{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

}

?>