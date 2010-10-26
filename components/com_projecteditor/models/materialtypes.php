<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');
require_once 'lib/data/MaterialTypePeer.php';

class ProjectEditorModelMaterialTypes extends ProjectEditorModelBase{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
    parent::__construct();
  }
  
  public function find($p_iMaterialTypeId){
    return MaterialTypePeer::find($p_iMaterialTypeId);
  }
  
 
}

?>