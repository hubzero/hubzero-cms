<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once( 'api/org/nees/oracle/Project.php' );

class CurateModelDefault extends CurateModelProjects{
	

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