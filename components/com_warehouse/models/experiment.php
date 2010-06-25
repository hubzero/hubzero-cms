<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once('lib/data/LocationPlanPeer.php');

class WarehouseModelExperiment extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
	
  public function findTrialsByExperiment($p_iExperimentId, $p_strSortBy){
  	return TrialPeer::findByExperiment($p_iExperimentId, $p_strSortBy);
  }
  
  public function findRepetitionsByExperiment($p_iExperimentId, $p_strSortBy){
  	return TrialPeer::findByExperiment($p_iExperimentId, $p_strSortBy);
  }
  
  public function findRepititionsByMembers($p_iMembersId){
  	return RepetitionPeer::findByMembers($p_iMembersId);
  }
  
  public function findRepititionsByTrial($p_iTrialId){
  	return RepetitionPeer::findByTrial($p_iTrialId);
  }
  
  /**
   * Search for data file links with a non-zero rep_id using the specified exp_id.
   * @param $p_iExperimentId
   * @return array of DataFileLink objects
   */
  public function findRepetitionDataFileLinksByExperiment($p_iExperimentId){
  	return DataFileLinkPeer::findRepetitionDataFileLinksByExperiment($p_iExperimentId);
  }
  
  public function findFacilityByExperiment($p_iExperimentId) {
  	return OrganizationPeer::findExperimentFacility($p_iExperimentId);
  }
  
  public function findLocationPlansByExperiment($p_iExperimentId) {
  	return LocationPlanPeer::findAllByExperiment($p_iExperimentId);
  }
  
  public function findMaterialsByExperiment($p_iExperimentId){
  	require_once 'lib/data/MaterialPeer.php';
  	return MaterialPeer::findByExperiment($p_iExperimentId);
  }
  
  /**
   * @return Specimen object
   *
   */
  public function findSpecimenByProject($p_iProjectId){
  	return SpecimenPeer::findByProject($p_iProjectId);
  }
  
  public function findDataFileByTool($p_strTool, $p_iProjectId, $p_iExperimentId, $p_iTrialId=0, $p_iRepetitionId=0){
  	return DataFilePeer::findDataFileByTool($p_strTool, $p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);
  }
}

?>