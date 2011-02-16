<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');


class WarehouseViewTestMe extends JView{
	
  function display($tpl = null){
    $dStartTime = $this->getComputeTime();
    $oProjectArray = ProjectPeer::searchTestWithProjectIds();
    $dEndTime = $this->getComputeTime();
    $dSeconds = $dEndTime - $dStartTime;
    $_REQUEST["time1"] = round($dSeconds, 2);
    $_REQUEST["search1"] = serialize($oProjectArray);

    $dStartTime = $this->getComputeTime();
    foreach($oProjectArray as $iProjectId){
      $oProject = ProjectPeer::find($iProjectId);
    }
    $dEndTime = $this->getComputeTime();
    $dSeconds = $dEndTime - $dStartTime;
    $_REQUEST["time2"] = round($dSeconds, 2);

    $dStartTime = $this->getComputeTime();
    $oProjectArray = ProjectPeer::searchTestWithProjects();
    $dEndTime = $this->getComputeTime();
    $dSeconds = $dEndTime - $dStartTime;
    $_REQUEST["time3"] = round($dSeconds, 2);
    $_REQUEST["search3"] = serialize($oProjectArray);
    parent::display($tpl);
  }

  function getComputeTime(){
    $mtime = microtime();
    $mtime = explode(' ', $mtime);
    $mtime = $mtime[1] + $mtime[0];
    return $mtime;
  }
  
}

?>