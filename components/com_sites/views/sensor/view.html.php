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

        $allowCreate = FacilityHelper::canCreate($facility);
        $this->assignRef('allowCreate', $allowCreate);


        // *********************************************
        // Build calibrations section

        $calibrationsHtml = '';

        $sensorCalibrations = $sensor->getCalibrations();

        if(count($sensorCalibrations) > 0)
        {
            foreach($sensorCalibrations as $calibration)
            {
                /* @var $calibration Calibration */
                $calibId = $calibration->getid();
                $description = $calibration->getDescription();
                $name = $calibration->getCalibrator();
                $measRange = $calibration->getMeasuredRange();
                $sens = $calibration->getSensitivityWithUnit();
                $ref = $calibration->getReferenceWithUnit();
                $fac = $calibration->getCalibFactorWithUnit();
                $calibrationid = $calibration->getId();



                $calibrationsHtml .= '<tr>';
                $calibrationsHtml .=     '<td><a href="' . JRoute::_('index.php?option=com_sites&view=calibration&id=' . $facilityID . '&sensorid=' . $sensorid . '&calibrationid=' . $calibrationid) . '"> ' . $calibrationid . '</a></td>';
                $calibrationsHtml .=     '<td>' . $calibration->getCalibDate() . '</td>';
                $calibrationsHtml .=     '<td style="width:250px">' . $description . '</td>';
                $calibrationsHtml .=     '<td>' . $name . '</td>';
                $calibrationsHtml .=     '<td>' . $calibration->getAdjustments() . '</td>';
                $calibrationsHtml .=     '<td>' . $measRange . '</td>';
                $calibrationsHtml .=     '<td>' . $sens . '</td>';
                $calibrationsHtml .=     '<td>' . $ref . '</td>';
                $calibrationsHtml .=     '<td>' . $fac . '</td>';
                $calibrationsHtml .=     '<td><a class="imagelink-no-underline" onclick="return confirm(\'Are you sure you want to delete this calibration?\');" href="' . JRoute::_('index.php?option=com_sites&view=sensor&task=deletecalibration&id=' . $facilityID . '&sensorid=' . $sensorid . '&calibrationid=' . $calibrationid) . '"><img src="/components/com_sites/images/cross.png"></a></td>';
                $calibrationsHtml .= '</tr>';











                }
        }

        $this->assignRef('calibrationsHtml', $calibrationsHtml);

        $fileSection = $this->getDocList($sensor, $facility);
        $this->assignRef('fileSection', $fileSection);

        
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
