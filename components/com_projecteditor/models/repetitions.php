<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');

class ProjectEditorModelRepetitions extends ProjectEditorModelBase{
	
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
  public function findRepititionsByTrial($p_iTrialId){
  	return RepetitionPeer::findByTrial($p_iTrialId);
  }
  
  /**
   * 
   *
   */
  public function getTrialById($p_iTrialId){
  	return TrialPeer::retrieveByPK($p_iTrialId);
  }
}

?>