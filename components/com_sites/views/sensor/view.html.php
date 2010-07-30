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
 
class sitesViewSensor extends JView
{
    function display($tpl = null)
    {
    	$facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle($fac_name);             

        // Add facility name to breadcrumb
        $pathway->addItem( $fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));
        
        // Add Sensor tab info to breadcrumb
        $pathway->addItem( "Sensors",  JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID));
        
    	// Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);
        
    	// Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs); 

        $this->assignRef('facilityID', $facilityID); 

        $sensorid = JRequest::getVar('sensorid');
        $sensor = SensorPeer::find($sensorid);
        $this->assignRef('sensor', $sensor);
        $this->assignRef('sensorid', $sensorid);
        
        // Add the parent sensor model to the breadcrumb
        $sm = $sensor->getSensorModel();
        $pathway->addItem( 'Sensor: ' . $sensor->getName(),  JRoute::_('index.php?option=com_sites&view=sensor&id=' . $facilityID) . '&sensormodelid=' . $sm->getId());
        
        $canedit = FacilityHelper::canEdit($facility);
        $this->assignRef('canedit', $canedit);


        // *********************************************
        // Build calibrations section

        $calibrationsHtml = '';

        $sensorCalibrations = $sensor->getCalibrations();

        if(count($sensorCalibrations) > 0)
        {
            foreach($sensorCalibrations as $calibration)
            {
                $calibId = $calibration->getid();
                $name = $calibration->getCalibrator();
                $measRange = $calibration->getMeasuredRange();
                $sens = $calibration->getSensitivityWithUnit();
                $ref = $calibration->getReferenceWithUnit();
                $fac = $calibration->getCalibFactorWithUnit();

                $calibrationsHtml .= '<tr>';
                $calibrationsHtml .=     '<td>' . $calibration->getCalibDate() . '</td>';
                $calibrationsHtml .=     '<td>' . $name . '</td>';
                $calibrationsHtml .=     '<td>' . $calibration->getAdjustments() . '</td>';
                $calibrationsHtml .=     '<td>' . $measRange . '</td>';
                $calibrationsHtml .=     '<td>' . $sens . '</td>';
                $calibrationsHtml .=     '<td>' . $ref . '</td>';
                $calibrationsHtml .=     '<td>' . $fac . '</td>';
                $calibrationsHtml .= '</tr>';
            }
        }

        $this->assignRef('calibrationsHtml', $calibrationsHtml);


        parent::display($tpl);
    }
}
