<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class WarehouseModelMore extends WarehouseModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function findProjectPhotoDataFiles($p_iProjectId, $p_iLowerLimit=0, $p_iUpperLimit = 24){
  	return DataFilePeer::findProjectPhotoDataFiles($p_iProjectId, $p_iLowerLimit, $p_iUpperLimit);
  }
  
  public function findProjectPhotoDataFilesCount($p_iProjectId){
  	return DataFilePeer::findProjectPhotoDataFilesCount($p_iProjectId);
  }

}

?>