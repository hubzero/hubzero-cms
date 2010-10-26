<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('editdatafile.php');
require_once 'lib/data/DataFilePeer.php';
require_once 'lib/data/DataFile.php';

class ProjectEditorModelEditAnalysis extends ProjectEditorModelEditDataFile{
	
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