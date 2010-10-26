<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class WarehouseModelSearchFiles extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function findByTitle($p_strTitle, $p_iLowerLimit=1, $p_iUpperLimit=10){
    return DataFilePeer::findByTitle($p_strTitle, $p_iLowerLimit, $p_iUpperLimit);
  }

  public function findByTitleCount($p_strTitle){
    return DataFilePeer::findByTitleCount($p_strTitle);
  }

  public function findByName($p_strName, $p_iLowerLimit=1, $p_iUpperLimit=10){
    return DataFilePeer::findByName($p_strName, $p_iLowerLimit, $p_iUpperLimit);
  }

  public function findByNameCount($p_strName) {
    return DataFilePeer::findByNameCount($p_strName);
  }
  
}

?>