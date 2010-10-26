<?php 

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewFeatured extends JView{
	
  function display($tpl = null){
    /* @var $oWarehouseModel WarehouseModelFeatured */
    $oWarehouseModel =& $this->getModel();
    $strTabArray = $oWarehouseModel->getSearchTabArray(); 
    $strTabViewArray = $oWarehouseModel->getSearchTabViewArray();
    $strTabHtml = $oWarehouseModel->getTabs( "warehouse", 0, $strTabArray, $strTabViewArray, "featured" );
    $this->assignRef( "strTabs", $strTabHtml );

    $strTreeTabArray = $oWarehouseModel->getTreeBrowserTabArray();
    $strTreeTabHtml = $oWarehouseModel->getTreeTab( "warehouse", 0, $strTreeTabArray, "projects", true );
    $this->assignRef( "strTreeTabs", $strTreeTabHtml );

    $this->assignRef( Search::FUNDING_TYPE, $oWarehouseModel->getFundingOrgs() );
    //$this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );
    $this->assignRef( "mod_warehousepopularsearches", ComponentHtml::getModule("mod_warehousepopularsearches") );


    JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
	
    parent::display($tpl);
  }
  
  
}

?>