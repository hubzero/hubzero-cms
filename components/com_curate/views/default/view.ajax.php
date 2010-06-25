<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once('api/org/nees/oracle/util/DbPagination.php');
require_once('components/com_curate/models/projects.php');

class CurateViewDefault extends JView{
	
  function display($tpl = null){
  	$firephp = FirePHP::getInstance(true);
  	$firephp->log('CurateViewList(ajax)::display');
  	
  	$oModel =& $this->getModel();

    #if a page.index isn't passed, set the default index to 0
    $nCurrentPageIndex = JRequest::getVar('index', 0, 'int');
    
    #if a page.size isn't passed, set the default index to 25 rows
    $nDisplaySize = JRequest::getVar('limit', 25, 'int');
    
    #find the rows to display
	$oProjectArray = $oModel->getCuratedProjectsWithPagination(0, $nCurrentPageIndex, $nDisplaySize);
	$this->assignRef( 'projectArray', $oProjectArray );
    
    #find the total rows for pagination
    $nProjectCount = $oModel->getCuratedProjectsCount(0);
    
    $oDbPagination = new DbPagination($nCurrentPageIndex, $nProjectCount, $nDisplaySize);
    $oDbPagination->computePageCount();
    
    $this->assignRef('projectsPagination', $oDbPagination->getPaginationFooter("/curate"));
    
    parent::display($tpl);
  	
  }//end display
  
  
}//end class
?>
