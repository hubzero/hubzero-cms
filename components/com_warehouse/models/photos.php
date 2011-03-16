<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

//require_once('base.php');
require_once('experiment.php');

class WarehouseModelPhotos extends WarehouseModelExperiment{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  /*
  public function findDataFileByMimeType($p_iProjectId, $p_iExperimentId, $p_iTrialId=0, $p_iRepetitionId=0, $p_iLowerLimit=0, $p_iUpperLimit = 24){
    return DataFilePeer::findDataFileByMimeType($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId, $p_iLowerLimit, $p_iUpperLimit);	
  }
  */
  
  /*
  public function findDataFileByMimeTypeCount($p_iProjectId, $p_iExperimentId, $p_iTrialId=0, $p_iRepetitionId=0){
    return DataFilePeer::findDataFileByMimeTypeCount($p_iProjectId, $p_iExperimentId, $p_iTrialId, $p_iRepetitionId);	
  }
  */

}

?>