<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('confirmproject.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'api/org/nees/html/UserRequest.php';
require_once 'lib/data/ProjectOrganization.php';
require_once 'lib/data/Organization.php';
require_once 'lib/data/ResearcherKeyword.php';
require_once 'lib/data/Sponsor.php';
require_once 'lib/data/SponsorPeer.php';

class ProjectEditorModelCurateConfirmProject extends ProjectEditorModelConfirmProject{
	

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