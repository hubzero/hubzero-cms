<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class WarehouseModelDataFiles extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  /**
   * Look inside the data_file_link table by trial-id and 
   * rep-id equal to 0.  If we get results, these files 
   * are on the trial level.  
   * 
   * @param $p_iMembersId
   */
  public function findDataByTrial($p_iTrialId){
  	return DataFilePeer::getDataFilesByTrial($p_iTrialId);
  }
  
  /**
   * Look inside the data_file_link table by trial-id and 
   * rep-id equal to 0.  If we get results, these files 
   * are on the trial level.  
   * 
   * @param $p_iMembersId
   */
  public function getDataFilesByTrialIdAndPath($p_iTrialId, $p_strPathEndsWith){
  	return DataFilePeer::getDataFilesByTrialIdAndPath($p_iTrialId, $p_strPathEndsWith);
  }
  
  /**
   * Look inside the data_file_link table where rep-id 
   * equals the given parameter.  If we get results, 
   * the data files are on the repetition level.
   * 
   * @param $p_iRepetitionId
   */
  public function findDataByRepetition($p_iRepetitionId){
  	return DataFilePeer::getDataFilesByRepetition($p_iRepetitionId);
  }
  
  public function findByDirectory($p_strPath){
  	return DataFilePeer::findByDirectory($p_strPath);
  }

}

?>