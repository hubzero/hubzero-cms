<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/MaterialTypePropertyPeer.php';

class ProjectEditorModelMproperties extends ProjectEditorModelBase{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function findMaterialProperties(){
  	return MaterialPropertyPeer::findAll();
  }
  
  public function findByMaterialTypePropertyDisplayName($p_strDisplayName) {
  	return MaterialTypePropertyPeer::findByMaterialTypePropertyDisplayName($p_strDisplayName);
  }
  
 
}

?>