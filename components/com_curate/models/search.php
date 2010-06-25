<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once( 'api/org/nees/oracle/Curate.php' );

class CurateModelSearch extends CurateModelProjects{
	

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
  public function getProjectsByNameWithPagination($p_sName, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return Curate::getProjectsByNameWithPagination($p_sName, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  /**
   * 
   *
   */
  public static function getProjectsCountByName($p_nDeleted, $p_sName){
  	return Curate::getProjectsCountByName($p_nDeleted, $p_sName);
  }
  
}

?>