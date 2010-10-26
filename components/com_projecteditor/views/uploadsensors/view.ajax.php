<?php 

/**
 * @see components/com_projecteditor/models/uploadform.php
 * @see modules/mod_warehouseupload
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'lib/data/LocationPlan.php';
require_once 'api/org/nees/util/StringHelper.php';

class ProjectEditorViewUploadSensors extends JView{
	
  function display($tpl = null){
    $strErrorArray = array();
    $oEntityTypeArray = null;
    $strEntityTypesHTML = "";

    /* @var $oModel ProjectEditorModelUploadSensors */
    $oModel =& $this->getModel();

    //Incoming 
    $iProjectId = JRequest::getInt("projid", 0);
    if(!$iProjectId){
      echo "No project selected.";
      return;
    }
    $iExperimentId = JRequest::getInt("experimentId", 0);
    if(!$iExperimentId){
      echo "No experiment selected.";
      return;
    }

    $iLocationPlanId = JRequest::getInt("locationPlanId", 0);
    if(!$iLocationPlanId){
      echo "No sensor list selected.";
      return;
    }

    $strPlanType = "Sensor";
    $strAlert = StringHelper::EMPTY_STRING;

    /* @var $oLocationPlan LocationPlan */
    $oLocationPlan = $oModel->findLocationPlanById($iLocationPlanId);
    $this->assignRef( 'lpName', $oLocationPlan->getName() );
    $this->assignRef( 'lpid', $oLocationPlan->getId() );
    $this->assignRef( 'planType', $strPlanType);
    $this->assignRef( 'alert', $strAlert);
    $this->assignRef( 'iExperimentId', $iExperimentId);
    $this->assignRef( 'iLocationPlanId', $iLocationPlanId);
    parent::display($tpl);
  }
  
}

?>