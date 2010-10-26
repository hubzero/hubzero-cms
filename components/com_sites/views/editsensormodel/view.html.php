<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright	Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitesVieweditsensormodel extends JView
{

    function display($tpl = null)
    {

        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
        $sensorModelID = JRequest::getVar('sensormodelid', -1);
    	$facility = FacilityPeer::find($facilityID);
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef( 'FacilityName', $fac_name);
        $this->assignRef( 'facility', $facility);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // add or edit? (-1 will mean new)
        $this->assignRef('sensormodelid', $sensorModelID);

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Sensor to breadcrumb
        $pathway->addItem("Sensors", JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID));

        // Add current page to breadcrumb
        $pathway->addItem((($sensorModelID == -1) ? 'Add' : 'Edit'). ' Sensor Model' , JRoute::_('index.php?option=com_sites&view=editsensormodel&id=' . $facilityID) . '&sensormodelid=' . $sensorModelID);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs); 


        $fieldValues = array();

        if($sensorModelID > -1)
            $sensorModel = SensorModelPeer::find($sensorModelID);
        else
            $sensorModel = new SensorModel();

        // Grab field values for editing
        $errorMsg = JRequest::getVar('errorMsg', '');
        $propertyNames =  SensorModelPeer::getFieldNames();
        foreach ($propertyNames as $propName)
        {
            // If we're editing and got an error grab info from the previously submitted form, not the database
            if(!empty($errorMsg))
                $fieldValues[$propName] = JRequest::getVar($propName, '');
            else
                $fieldValues[$propName] = $sensorModel->getByName($propName);
        }
        
        $this->assignRef('fieldValues', $fieldValues);


        // Generate and store dropdown lists
        $sensortypes = $this->getSensorTypeDropDownList($fieldValues["SensorTypeId"]);
        $mfgs = SensorModelPeer::getManufacturerOptions($fieldValues["Manufacturer"]);

        // If we're editing and got an error grab info from the previously submitted form, not the database
        if(!empty($errorMsg))
        {
            $measuredValueUnits =  $this->getUnitOptions($fieldValues["MeasuredValueUnitsId"]);
            $sensitivityUnits =  $this->getUnitOptions($fieldValues["SensitivityUnitsId"]);
            $temperatureUnits =  $this->getUnitOptions($fieldValues["TempUnitsId"]);
        }
        else
        {
            $measuredValueUnits = $this->getUnitOptions($sensorModel->getMeasuredValueUnitsId());
            $sensitivityUnits = $this->getUnitOptions($sensorModel->getSensitivityUnitsId());
            $temperatureUnits = $this->getUnitOptions($sensorModel->getTempUnitsId());
        }


        $this->assignRef('sensortypes', $sensortypes);
        $this->assignRef('mfgs', $mfgs);
        $this->assignRef('measuredvalueunits', $measuredValueUnits);
        $this->assignRef('sensitivityunits', $sensitivityUnits);
        $this->assignRef('temperatureunits', $temperatureUnits);



        // See if current logged in user can edit in this facility
	$allowEdit = FacilityHelper::canEditSensorModel($facility);
        $allowCreate = FacilityHelper::canCreateSensorModel($facility);

        // Check rights to be here, even though the referring page won't display a link here
        // without rights, we need to stop the hackers too
        if($sensorModelID == -1) // we're creating a new sensor
        {
            if($allowCreate)
                parent::display($tpl);
            else
                echo 'You are not authorized to create sensor models';
        }
        else // we're doing an edit
        {
            if($allowEdit)
                parent::display($tpl);
            else
                echo 'You are not authorized to edit sensor models';
        }






    }


    function getSensorTypeDropDownList($selectedTypeId) {

        if (!$selectedTypeId)
            $selectedTypeId = 1;

        $smTypeCol = SensorTypePeer::findAll();
        $smTypes = array();
        foreach ($smTypeCol as $smType)
            $smTypes[$smType->getId()] = $smType->getName();

        asort($smTypes);

        $ret = "";
        foreach ($smTypes as $smTypeId => $smTypeName) {
            $selAtt = ($selectedTypeId == $smTypeId) ? ' selected' : '';
            $ret .= "<option value='$smTypeId' $selAtt>$smTypeName</option> ";
        }

        return $ret;
    }


    function getUnitOptions($unitId) {

        $ops = "<option value=\"\"></option>\n";
        $units = MeasurementUnitPeer::findAll();

        foreach ($units as $unit) {
            $sel = "";

            if (intval($unitId) == $unit->getId())
                $sel = " selected ";

            // DRB Bug fix for ticketID: 940 (9.7.2010). Below, the === used to be == and
            // one of our abbreviations was 1/Ω, and for some crazy reason
            // $unitID == $unit->getAbbreviation() was being evaluated to true (i.e. 1==1/Ω)
            //
            // Weird because 1/Ω is actually undefined, assuming this string was being evaluated
            // as an expression, so not sure why this would ever return true. Either way,
            // the === added the type check and fixed it
            if ($unitId === $unit->getAbbreviation() && $unit->getAbbreviation())
                $sel = " selected ";

            $name = ( is_null($unit->getAbbreviation()) || $unit->getAbbreviation() == "" ) ? $unit->getName() : $unit->getName() . " ( " . $unit->getAbbreviation() . " )";
            $ops .= "<option $sel value=\"" . $unit->getId() . "\" >$name</option>\n";
        }
        return $ops;
    }












    
}
