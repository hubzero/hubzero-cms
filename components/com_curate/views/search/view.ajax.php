<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once('api/org/nees/oracle/util/DbPagination.php');
require_once('components/com_curate/models/projects.php');

class CurateViewSearch extends JView{
	
  function display($tpl = null){
  	$sSearchBy = JRequest::getString('searchby');
  	$sSearchTerm = JRequest::getString('searchTerm');
  	
  	#if a page.index isn't passed, set the default index to 0
    $nCurrentPageIndex = JRequest::getVar('index', 0, 'int');
    if($nCurrentPageIndex===null){
      $nCurrentPageIndex=0;
    }
    
    #if a page.size isn't passed, set the default index to 25 rows
    $nDisplaySize = JRequest::getVar('limit', 25, 'int');
    
    $oModel =& $this->getModel();
    
    #find the rows to display
    $oProjectArray =  array();
    switch($sSearchBy){
      case "name":
    	$oProjectArray =  $this->getProjectsByName($oModel,$sSearchTerm,0,$nCurrentPageIndex,$nDisplaySize);
    	
    	#find the total rows for pagination
     	$nProjectCount = $oModel->getProjectsCountByName(0, $sSearchTerm);
    	break;
      case "keyword":
    	$oProjectArray =  $this->getProjectsByKeyword($oModel,$sSearchTerm,0,$nCurrentPageIndex,$nDisplaySize);
    	
    	#find the total rows for pagination
     	$nProjectCount = $oModel->getProjectsCountByDescription(0, $sSearchTerm);
    	break;
      case "title":
    	$oProjectArray =  $this->getProjectsByTitle($oModel,$sSearchTerm,0,$nCurrentPageIndex,$nDisplaySize);
    	
    	#find the total rows for pagination
     	$nProjectCount = $oModel->getProjectsCountByTitle(0, $sSearchTerm);
    	break;
    }
    $this->assignRef( 'projectArray', $oProjectArray );
    
    
    
    $oDbPagination = new DbPagination($nCurrentPageIndex, $nProjectCount, $nDisplaySize);
    $oDbPagination->computePageCount();
    //echo "pages=".$oDbPagination->getPageCount();
    
    $this->assignRef('projectsPagination', $oDbPagination->getPaginationFooter("/curate?task=search&format=ajax"));
    
    parent::display($tpl);
  	
  }//end display
  
  private function getProjectsByName($p_oModel,$p_sSearchTerm, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return $p_oModel->getProjectsByNameWithPagination($p_sSearchTerm, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  private function getProjectsByKeyword($p_oModel,$p_sSearchTerm, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return $p_oModel->getProjectsByDescriptionWithPagination($p_sSearchTerm, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  private function getProjectsByTitle($p_oModel,$p_sSearchTerm, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize){
  	return $p_oModel->getProjectsByTitleWithPagination($p_sSearchTerm, $p_nDeleted, $p_nCurrentIndex, $p_nDisplaySize);
  }
  
  
  
}//end class
?>
