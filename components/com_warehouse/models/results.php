<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.model' );

require_once('base.php');


class WarehouseModelResults extends WarehouseModelBase{

  private $m_oResultsArray;
  
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
  }
  
  public function setResults($p_oResultArray){
  	$this->m_oResultsArray = $p_oResultArray;
  }
  
  public function getResults(){
  	return $this->m_oResultsArray;
  }
	
}

?>