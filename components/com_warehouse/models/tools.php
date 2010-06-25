<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class WarehouseModelTools extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function findDataFileByTool($p_strTool, $p_iProjectId, $p_iExperimentId, $p_iTrialId=0, $p_iRepetitionId=0){
  	return DataFilePeer::findDataFileByTool($p_strTool, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
  
  public function findProjectAndExperimentByRepetition($p_iRepetitionId){
  	require_once 'lib/data/DataFileLinkPeer.php';
  	return DataFileLinkPeer::findProjectAndExperimentByRepetition($p_iRepetitionId);
  }
}

?>