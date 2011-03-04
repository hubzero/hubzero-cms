<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
require_once 'api/org/nees/static/Search.php';

class WarehouseViewSearchFilter extends JView {

    function display($tpl = null) {
      //incoming
      $strType = JRequest::getVar("type");
      $strAction = JRequest::getVar("action");
      $strTarget = JRequest::getVar("target");
      $strField = JRequest::getVar("field");

      $oFilterArray = array();
      $iCount = 0;
      switch ($strType) {
          case "site":
            $oFilterArray = $_SESSION[Search::NEES_SITE_FILTER];
            break;
          case "sponsor":
            $oFilterArray = $_SESSION[Search::SPONSORS_FILTER];
            break;
          case "material":
            $oFilterArray = $_SESSION[Search::MATERIAL_TYPES_FILTER];
            break;
          case "pi":
            $oFilterArray = $_SESSION[Search::PRINCIPLE_INVESTIGATORS_FILTER];
            break;
          case "researchtype":
            $oFilterArray = $_SESSION[Search::NEES_RESEARCH_TYPES_FILTER];
            break;
          default:
              break;
      }

      $this->assignRef("strSelectedFilterArray", $oFilterArray);
      $this->assignRef("strSelectedAction", $strAction);
      $this->assignRef("strSelectedTarget", $strTarget);
      $this->assignRef("strSelectedType", $strType);
      $this->assignRef("strSelectedField", $strField);

      /* @var $oModel WarehouseModelSearchFilter */
      $oModel = & $this->getModel();

      parent::display($tpl);
    }

//end display
}
?>