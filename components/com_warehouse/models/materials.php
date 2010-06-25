<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once('lib/data/MaterialPropertyPeer.php');

class WarehouseModelMaterials extends WarehouseModelBase{
	
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
  public function findMaterialsByExperiment($p_iExpid){
  	return MaterialPropertyPeer::findByExperiment($p_iExpid);
  }
  
  
  
  /**
   * 
   *
   */
  public function getTrialById($p_iTrialId){
  	return TrialPeer::retrieveByPK($p_iTrialId);
  }
}

?>