<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once( 'api/org/nees/oracle/Project.php' );
require_once( 'api/org/nees/oracle/Curate.php' );
require_once( 'api/org/nees/html/CurateHtml.php' );
require_once( 'project.php' );

class CurateModelFinished extends CurateModelProject{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
  }
  
  /**
   * 
   *
   */
  public function getCuratedObjectAttribute($p_strColumn, $p_iObjectId){
  	$firephp = FirePHP::getInstance(true);
  	$firephp->log('curated object', $p_iObjectId);
  	return Curate::getCuratedObjectAttribute($p_strColumn, $p_iObjectId);
  }
  
}

?>