<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewResults extends JView{
	
  function display($tpl = null){
    //get the tabs to display on the page
    $oResultsModel =& $this->getModel();
    $strTabArray = $oResultsModel->getSearchResultsTabArray();
    $strTabHtml = $oResultsModel->getTabs( "warehouse", 0, $strTabArray, "results" );
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

    $oDbPagination = new DbPagination($iIndex, $iResultsCount, $iDisplay);
    $oDbPagination->computePageCount();
    $this->assignRef('pagination', $oDbPagination->getFooter($_SERVER['REQUEST_URI'], "frmResults", "project-list"));

    $this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );

    JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
    JFactory::getApplication()->getPathway()->addItem("Results","javascript:void(0)");
    parent::display($tpl);
  }
}

?>