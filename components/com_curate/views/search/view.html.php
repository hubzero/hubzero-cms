<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once('api/org/nees/oracle/util/DbPagination.php');

class CurateViewProjects extends JView{
	
  function display($tpl = null){
    $oModel =& $this->getModel();

    #if a page.index isn't passed, set the default index to 0
    $nCurrentPageIndex = JRequest::getVar('index', 0, 'int');
    
    #if a page.size isn't passed, set the default index to 25 rows
    $nDisplaySize = JRequest::getVar('limit', 25, 'int');
    
    #find the rows to display
	$oProjectArray = $oModel->getProjectsByCurationStatusWithPagination(0, "UNCURATED", $nCurrentPageIndex, $nDisplaySize);
    $this->assignRef( 'projectArray', $oProjectArray );
    
    #find the total rows for pagination
    $nProjectCount = $oModel->getProjectsCountByCurationStatus(0, "UNCURATED");
    
    $oDbPagination = new DbPagination($nCurrentPageIndex, $nProjectCount, $nDisplaySize);
    $oDbPagination->computePageCount();
    //echo "pages=".$oDbPagination->getPageCount();
    
    $this->assignRef( 'projectsPagination', $oDbPagination->getPaginationFooter("/curate"));
    
    parent::display($tpl);
  }
}
?>
