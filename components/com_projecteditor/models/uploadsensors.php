<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );

require_once('sensors.php');
require_once 'lib/data/SensorTypePeer.php';

class ProjectEditorModelUploadSensors extends ProjectEditorModelSensors{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }

  function getSensorTypes(){
    return SensorTypePeer::findAll();
  }
  
}

?>