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
 
class sitesViewCalibration extends JView
{
    function display($tpl = null)
    {
    	$facilityID = JRequest::getVar('id');
        $calibrationID = JRequest::getVar('calibrationid');
        $sensorID = JRequest::getVar('sensorid');

    	$facility = FacilityPeer::find($facilityID);
  	$fac_name = $facility->getName();

        // For access to the page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle($fac_name);             

    	// Get the tabs for the top of the page (4-Sensors)
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs);

        // Add facility name to breadcrumb
        $pathway->addItem( $fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));
        
        // Add Sensor tab info to breadcrumb
        $pathway->addItem( "Sensors",  JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID));

        // Grab the calibration and the base sensor
        $calibration = CalibrationPeer::findBySensorAndId($sensorID, $calibrationID);
        $sensor = SensorPeer::find($sensorID);
        
        // Add the sensor to the breadcrumb
        $pathway->addItem( 'Sensor: ' . $sensor->getName(),  JRoute::_('index.php?option=com_sites&view=sensor&id=' . $facilityID) . '&sensorid=' . $sensor->getId());

        // Documentation for this calibration
        $basepath = $calibration->getPathname() . "/Documentation";
        $dfs = DataFilePeer::findDataFileBrowser($basepath);
        $datafiles = array();

        foreach ($dfs as $df) {
            /* @var $df DataFile */
            $datafiles[] = $df;
        }

        // Rights information
        $canedit = FacilityHelper::canEdit($facility);
        $this->assignRef('canedit', $canedit);

        $allowCreate = FacilityHelper::canCreate($facility);
        $this->assignRef('allowCreate', $allowCreate);

        // pass stuff to the page
        $this->assignRef('facilityID', $facilityID);
        $this->assignRef('facility', $facility);
        $this->assignRef('sensor', $sensor);
        $this->assignRef('sensorid', $sensorID);
        $this->assignRef('calibration', $calibration);
        $this->assignRef('calibrationid', $calibrationID);
        $this->assignRef('datafiles', $datafiles);

        // Used to redirect here after a documentation file addition
        $uri = & JURI::getInstance();
        $redirectURL = $uri->toString(array('path', 'query'));
        $redirectURL = base64_encode($redirectURL);
        $redirectURL = $redirectURL;
        $this->assignRef('redirectURL', $redirectURL);

        parent::display($tpl);
    }


    function getDocList($sensor, $facility) {
        /* @var $sensor Sensor */
        $basepath = $sensor->getPathname() . "/Documentation";
        $dfs = DataFilePeer::findDataFileBrowser($basepath);
        $datafiles = array();

        foreach ($dfs as $df) {
            /* @var $df DataFile */
            $datafiles[] = $df;
        }

        return $datafiles;
    }

}
