<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitesVieweditcalibration extends JView
{

    function display($tpl = null)
    {

        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
        $calibrationID = JRequest::getVar('calibrationid', -1);
    	$facility = FacilityPeer::find($facilityID);
        $errorMsg = JRequest::getVar('errorMsg', '');
        $sensorID = Jrequest::getVar('sensorid', -1);
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef('FacilityName', $fac_name);
        $this->assignRef('facility', $facility);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // add or edit? (-1 will mean new)
        $this->assignRef('calibrationid', $calibrationID);
        $this->assignRef('sensorid', $sensorID);

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Sensors tab to breadcrumb
        $pathway->addItem("Sensor", JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID ));

        // Add current page to breadcrumb
        $pathway->addItem((($calibrationID == -1) ? 'Add' : 'Edit'). ' Calibration' , JRoute::_('index.php?option=com_sites&view=editcalibration&id=' . $facilityID) . '&calibrationid=' . $calibrationID);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs); 

        // We'll need the sensor later to attach this calibraiton to
        $sensor = SensorPeer::find($sensorID);

        // If editing, grab the calibration we are updating to populate form
        $classSpecificFields = '';
        $errorMsg = JRequest::getVar('errorMsg');

        if($calibrationID > -1) // edit
        {

            $formfields = array();

            // THis means we've had a failed save, populate the form from the previously filled in fields
            if(!empty($errorMsg))
            {

                $MeasuredValueUnitsOptions = $this->getUnitOptions(JRequest::getVar('measuredValueUnits', ''));
                $this->assignRef('MeasuredValueUnitsOptions', $MeasuredValueUnitsOptions);

                $SensitivityUnitsOptions = $this->getUnitOptions(JRequest::getVar('sensitivityUnits', ''));
                $this->assignRef('SensitivityUnitsOptions', $SensitivityUnitsOptions);

                $ReferenceUnitsOptions = $this->getUnitOptions(JRequest::getVar('referenceUnits', ''));
                $this->assignRef('ReferenceUnitsOptions', $ReferenceUnitsOptions);

                $CalibFactorUnitsOptions = $this->getUnitOptions(JRequest::getVar('calibFactorUnits', ''));
                $this->assignRef('CalibFactorUnitsOptions', $CalibFactorUnitsOptions);

                $formfields['CalibDate'] = JRequest::getVar('CalibDate');
                $formfields['calibrator'] = JRequest::getVar('calibrator', '');
                $formfields['description'] = JRequest::getVar('description', '');
                $formfields['adjustments'] = JRequest::getVar('adjustments', '');
                $formfields['minMeasuredValue'] = JRequest::getVar('minMeasuredValue', '');
                $formfields['maxMeasuredValue'] = JRequest::getVar('maxMeasuredValue', '');
                $formfields['sensitivity'] = JRequest::getVar('sensitivity', '');
                $formfields['reference'] = JRequest::getVar('reference', '');
                $formfields['calibFactor'] = JRequest::getVar('calibFactor', '');

 
            }
            else // grab the values from the database
            {
                $calibration = CalibrationPeer::find($calibrationID);
                $formfields['CalibDate'] = $calibration->getCalibDate('%m-%d-%Y');

                $MeasuredValueUnitsOptions = $this->getUnitOptions($calibration->getMeasuredValueUnits());
                $this->assignRef('MeasuredValueUnitsOptions', $MeasuredValueUnitsOptions);

                $SensitivityUnitsOptions = $this->getUnitOptions($calibration->getSensitivityUnits());
                $this->assignRef('SensitivityUnitsOptions', $SensitivityUnitsOptions);

                $ReferenceUnitsOptions = $this->getUnitOptions($calibration->getReferenceUnits());
                $this->assignRef('ReferenceUnitsOptions', $ReferenceUnitsOptions);

                $CalibFactorUnitsOptions = $this->getUnitOptions($calibration->getCalibFactorUnits());
                $this->assignRef('CalibFactorUnitsOptions', $CalibFactorUnitsOptions);

                $formfields['calibrator'] = $calibration->getCalibrator();
                $formfields['description'] = $calibration->getDescription();
                $formfields['adjustments'] = $calibration->getAdjustments();
                $formfields['minMeasuredValue'] = $calibration->getMinMeasuredValue();
                $formfields['maxMeasuredValue'] = $calibration->getMaxMeasuredValue();
                $formfields['sensitivity'] = $calibration->getSensitivity();
                $formfields['reference'] = $calibration->getReference();
                $formfields['calibFactor'] = $calibration->getCalibFactor();

                }

        }
        else
        {
            // We're creating a new calibration, blank out the form so we dont' have to do conditional checks
            // on the form page to check for defined variables

            $MeasuredValueUnitsOptions = $this->getUnitOptions(JRequest::getVar('measuredValueUnits', ''));
            $this->assignRef('MeasuredValueUnitsOptions', $MeasuredValueUnitsOptions);

            $SensitivityUnitsOptions = $this->getUnitOptions(JRequest::getVar('sensitivityUnits', ''));
            $this->assignRef('SensitivityUnitsOptions', $SensitivityUnitsOptions);

            $ReferenceUnitsOptions = $this->getUnitOptions(JRequest::getVar('referenceUnits', ''));
            $this->assignRef('ReferenceUnitsOptions', $ReferenceUnitsOptions);

            $CalibFactorUnitsOptions = $this->getUnitOptions(JRequest::getVar('calibFactorUnits', ''));
            $this->assignRef('CalibFactorUnitsOptions', $CalibFactorUnitsOptions);

            $formfields['CalibDate'] = '';
            $formfields['calibrator'] =  '';
            $formfields['description'] =  '';
            $formfields['adjustments'] =  '';
            $formfields['minMeasuredValue'] = '';
            $formfields['maxMeasuredValue'] = '';
            $formfields['sensitivity'] =  '';
            $formfields['reference'] =  '';
            $formfields['calibFactor'] =  '';

        }

        // Pass this to the form
        $this->assignRef('formfields', $formfields);

        //$calibration->getCalibDate()

        // If we're editing and got an error grab info from the previously submitted form, not the database
        $calibrationValues = array();
        if(!empty($errorMsg))
        {

        }
        else
        {
            
        }

        //Pass the main array for populating fields to the form
        $this->assignRef('calibrationvalues', $calibrationValues);



        // See if current logged in user can edit in this facility
	$allowEdit = FacilityHelper::canEditSensorModel($facility);
        $allowCreate = FacilityHelper::canCreateSensorModel($facility);



        // Check rights to be here, even though the referring page won't display a link here
        // without rights, we need to stop the hackers too
        if($calibrationID == -1) // we're creating a new piece of equipment
        {
            if($allowCreate)
                parent::display($tpl);
            else
                echo 'You are not authorized to create calibration records';
        }
        else // we're doing an edit
        {
            if($allowEdit)
                parent::display($tpl);
            else
                echo 'You are not authorized to edit calibration records';
        }

    }

    
    function getMeasuredValueUnitOptions($calibration)
    {
        return $this->getUnitOptions($calibration, "MeasuredValueUnits");
    }


    function getSensitivityUnitOptions($calibration)
    {
        return $this->getUnitOptions($calibration, "SensitivityUnits");
    }


    function getReferenceUnitOptions($calibration)
    {
        return $this->getUnitOptions($calibration, "ReferenceUnits");
    }


    function getCalibFactorUnitOptions($calibration)
    {
        return $this->getUnitOptions($calibration, "CalibFactorUnits");
    }


    function getUnitOptions($selectedUnitID) {

        $ops = "<option value=\"\"></option>\n";

        $units = MeasurementUnitPeer::findAll();

        foreach ($units as $unit) {
            $selected = "";
            if (!is_null($selectedUnitID) && ($selectedUnitID == $unit->getId() || $selectedUnitID == $unit->getAbbreviation()))
                $selected = " selected $selectedUnitID";

            $name = ( is_null($unit->getAbbreviation()) || $unit->getAbbreviation() == "" ) ? $unit->getName() : $unit->getName() . " ( " . $unit->getAbbreviation() . " )";
            $ops .= "<option $selected value=\"" . $unit->getId() . "\" >$name</option>\n";
        }
        return $ops;
    }

}// end class
