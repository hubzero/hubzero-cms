<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once( 'api/org/nees/oracle/Project.php' );
require_once( 'api/org/nees/oracle/Curate.php' );

class CurateModelProjects extends JModel{
	

  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
  }
  
  /**
   * Get projects by curation status.
   *
   */
  function getProjectsByCurationStatus($p_nDeleted, $p_sCurationStatus){
  	return Project::getProjectsByCurationStatus($p_nDeleted, $p_sCurationStatus);
  }
  
  /**
   * Get a subset of projects by curation status.
   *
   */
  function getProjectsByCurationStatusWithPagination($p_nDeleted, $p_sCurationStatus, $p_nCurrentIndex, $p_nDisplaySize){
  	return Project::getProjectsByCurationStatusWithPagination($p_nDeleted, $p_sCurationStatus, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  /**
   * 
   *
   */
  function getProjectsCountByCurationStatus($p_nDeleted, $p_sCurationStatus){
  	return Project::getProjectsCountByCurationStatus($p_nDeleted, $p_sCurationStatus);
  }
  
  /**
   * 
   *
   */
  function getCuratedProjectsCount($p_nDeleted){
  	return Curate::getProjectsCount($p_nDeleted);
  }
  
  /**
   * Get a subset of projects by curation status.
   *
   */
  function getCuratedProjectsWithPagination($p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return Curate::getProjectsWithPagination($p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  /**
   * 
   *
   */
  public function getProjectsByNameWithPagination($p_sName, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return Project::getProjectsByNameWithPagination($p_sName, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  /**
   * 
   *
   */
  public static function getProjectsCountByName($p_nDeleted, $p_sName){
  	return Project::getProjectsCountByName($p_nDeleted, $p_sName);
  }
  
  /**
   * 
   *
   */
  public function getProjectsByTitleWithPagination($p_sTitle, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return Project::getProjectsByTitleWithPagination($p_sTitle, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  /**
   * 
   *
   */
  public static function getProjectsCountByTitle($p_nDeleted, $p_sTitle){
  	return Project::getProjectsCountByTitle($p_nDeleted, $p_sTitle);
  }
  
  /**
   * 
   *
   */
  public function getProjectsByDescriptionWithPagination($p_sDescription, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return Project::getProjectsByDescriptionWithPagination($p_sDescription, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  /**
   * 
   *
   */
  public static function getProjectsCountByDescription($p_nDeleted, $p_sDescription){
  	return Project::getProjectsCountByDescription($p_nDeleted, $p_sDescription);
  }
  
}

?>