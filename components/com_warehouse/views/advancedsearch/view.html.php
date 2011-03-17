<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class WarehouseViewAdvancedSearch extends JView{

  function display($tpl = null){
    $oWarehouseModel =& $this->getModel();
    $strTabArray = $oWarehouseModel->getSearchTabArray();
    $strTabViewArray = $oWarehouseModel->getSearchTabViewArray();
    $strTabTitleArray = $oWarehouseModel->getSearchTabTitleArray();
    $strTabHtml = $oWarehouseModel->getTabs( "warehouse", 0, $strTabArray, $strTabViewArray, "search", $strTabTitleArray );
    $this->assignRef( "strTabs", $strTabHtml );

    $strTreeTabArray = $oWarehouseModel->getTreeBrowserTabArray();
    $strTreeTabHtml = $oWarehouseModel->getTreeTab( "warehouse", 0, $strTreeTabArray, "projects", true );
    $this->assignRef( "strTreeTabs", $strTreeTabHtml );

    $this->assignRef( Search::FUNDING_TYPE, $oWarehouseModel->getFundingOrgs() );
    $_REQUEST[FacilityPeer::TABLE_NAME] = serialize($oWarehouseModel->getNeesFacilities());

    $this->assignRef( "mod_treebrowser", ComponentHtml::getModule("mod_treebrowser") );
    $this->assignRef( "mod_warehousepopularsearches", ComponentHtml::getModule("mod_warehousepopularsearches") );

    /*
     * When clicking on Project Warehouse from the menu, the text displays in the breadcrumbs.
     * Clicking off featured and back again removes "Project Warehouse" from the breadcrumbs.
     * Therefore, if the pathwaynames size == 1, add "Project Warehouse" to the breadcrumbs.
     */
    if(sizeof(JFactory::getApplication()->getPathway()->getPathwayNames())===1){
      JFactory::getApplication()->getPathway()->addItem("Project Warehouse","/warehouse");
    }

    parent::display($tpl);
  }


}

?>