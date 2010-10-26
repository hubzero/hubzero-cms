<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );
jimport( 'joomla.application.component.view' );

require_once('editmember.php');
require_once 'lib/data/AuthorizationPeer.php';
require_once 'lib/data/Person.php';
require_once 'lib/data/Project.php';

class ProjectEditorModelDeleteMember extends ProjectEditorModelEditMember{
	
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