<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/util/SearchHelper.php';

class WarehouseViewResults extends JView{
	
  function display($tpl = null){
    //get the tabs to display on the page
    $oResultsModel =& $this->getModel();
    $strTabArray = $oResultsModel->getSearchResultsTabArray();
    $strTabViewArray = $oResultsModel->getSearchResultsTabViewArray();
    $strTabHtml = $oResultsModel->getTabs( "warehouse", 0, $strTabArray, $strTabViewArray, "results" );
    $this->assignRef( "strTabs", $strTabHtml );

    $strTreeTabArray = $oResultsModel->getTreeBrowserTabArray();
    $strTreeTabHtml = $oResultsModel->getTreeTab( "warehouse", 0, $strTreeTabArray, "projects" , false);
    $this->assignRef( "strTreeTabs", $strTreeTabHtml );

    /*
     * grab the nees pagination object.  joomla's
     * pagination object doesn't handle the proper uri.
     */
    $iDisplay = JRequest::getVar('limit', 25);
    $iIndex = JRequest::getVar('index', 0);
    $iResultsCount = JRequest::getVar('count');
    $iLowerLimit = JRequest::getVar('low', 0);
    $iUpperLimit = JRequest::getVar('high', 0);

    //echo "results lower=".$iLowerLimit."<br>";
    //echo "results upper=".$iUpperLimit."<br>";

    $oDbPagination = new DbPagination($iIndex, $iResultsCount, $iDisplay, $iLowerLimit, $iUpperLimit);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmResults", "project-list"));

    //$this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );
    $this->assignRef( "mod_warehousefilter", ComponentHtml::getModule("mod_warehousefilter") );

    JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
    JFactory::getApplication()->getPathway()->addItem("Results","javascript:void(0)");
    parent::display($tpl);
  }
}

?>