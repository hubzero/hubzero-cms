<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
require_once 'lib/data/DataFile.php';

class WarehouseViewSearchFiles extends JView {

    function display($tpl = null) {

      /* @var $oModel WarehouseModelSearchFiles */
      $oModel =& $this->getModel();

      $iFindBy = JRequest::getInt('findby', 1);
      $strTerm = JRequest::getString('term', '');
      $iDisplay = JRequest::getVar('limit', 10);
      $iPageIndex = JRequest::getVar('index', 0);

      $iLowerLimit = $oModel->computeLowerLimit($iPageIndex, $iDisplay);
      $iUpperLimit = $oModel->computeUpperLimit($iPageIndex, $iDisplay);

      $iNext = $iPageIndex + 1;
      $iPrev = $iPageIndex - 1;

      $oFileArray = null;
      $iFileCount = 0;

      switch ($iFindBy){
         case 1:
             $oFileArray = $oModel->findByTitle($strTerm, $iLowerLimit, $iUpperLimit);
             $iFileCount = $oModel->findByTitleCount($strTerm);
             break;
         default :
             $oFileArray = $oModel->findByName($strTerm, $iLowerLimit, $iUpperLimit);
             $iFileCount = $oModel->findByNameCount($strTerm);
             break;
      }

      $_REQUEST['FOUND_FILES'] = serialize($oFileArray);
      $this->assignRef( 'iFindBy', $iFindBy );
      $this->assignRef( 'iFileCount', $iFileCount );
      $this->assignRef( 'iNext', $iNext );
      $this->assignRef( 'iPrev', $iPrev );

      parent::display($tpl);
    }

//end display
}
?>