<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

class ProjectEditorViewMproperties extends JView{
	
  function display($tpl = null){
    $iMaterialIndex = JRequest::getVar('materialIndex');
    $this->assignRef('materialIndex', $iMaterialIndex);

    $oModel =& $this->getModel();
    $oMeasurementUnitArray = $oModel->getMeasurementUnits();
    $_REQUEST['MEASUREMENT_UNITS'] = serialize($oMeasurementUnitArray);
    parent::display($tpl);
  }
  
}
?>
