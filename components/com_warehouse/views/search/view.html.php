<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewSearch extends JView{
	
  function display($tpl = null){
    $oWarehouseModel =& $this->getModel();
	$strTabArray = $oWarehouseModel->getSearchTabArray();
	$strTabHtml = $oWarehouseModel->getTabs( "warehouse", 0, $strTabArray, "search" );
	$this->assignRef( "strTabs", $strTabHtml );
	
	$strTreeTabArray = $oWarehouseModel->getTreeBrowserTabArray();
	$strTreeTabHtml = $oWarehouseModel->getTreeTab( "warehouse", 0, $strTreeTabArray, "projects", true );
	$this->assignRef( "strTreeTabs", $strTreeTabHtml );
	
	$this->assignRef( Search::FUNDING_TYPE, $oWarehouseModel->getFundingOrgs() );
	$this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );
	$this->assignRef( "mod_warehousepopularsearches", ComponentHtml::getModule("mod_warehousepopularsearches") );
	
	JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
	
	//echo "search...".sizeof(JFactory::getApplication()->getPathway())."<br>";
	
    parent::display($tpl);
  }
  
  
}

?>