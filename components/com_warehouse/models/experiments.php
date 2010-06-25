<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');


class WarehouseModelExperiments extends WarehouseModelBase{

  private $m_oResultsArray;
  
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
  public function findByProject($p_oProjectId, $p_strOrderby = null) {
  	return ExperimentPeer::findByProject($p_oProjectId, $p_strOrderby);
  }	
  
  /**
   * 
   *
   */
  public function findByNameProject($p_strName, $p_oProjectId) {
  	return ExperimentPeer::findByNameProject($p_strName, $p_oProjectId);
  }
}

?>