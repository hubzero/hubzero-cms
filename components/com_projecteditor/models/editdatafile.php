<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';

class ProjectEditorModelEditDataFile extends ProjectEditorModelBase{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  public function getDataFileById($p_iDataFileId){
    return DataFilePeer::retrieveByPK($p_iDataFileId);
  }

  public function getDataFileLinkById($p_iDataFileId){
    return DataFileLinkPeer::retrieveByPK($p_iDataFileId);
  }

  public function findOpeningTools(){
    return DataFilePeer::findOpeningTools();
  }
  
}

?>