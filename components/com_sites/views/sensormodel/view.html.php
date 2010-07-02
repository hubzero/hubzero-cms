<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright	Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
require_once 'lib/data/SensorModel.php';
require_once 'lib/data/MeasurementUnit.php';
require_once 'lib/util/DataFileBrowser.php';
 
/**
 * 
 * 
 */
 
class sitesViewsensormodel extends JView
{
    function display($tpl = null)
    {
    	$facilityID = JRequest::getVar('id');
    	$sensorModelID = JRequest::getVar('sensormodelid');
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
        
        // The sensor model info
        $sensorModel = SensorModelPeer::find($sensorModelID);
        $this->assignRef('sensorModel', $sensorModel);

        // The group values (Standard Specifications) for the model
        $groupvalues = SensorModelPeer::findGroupValues( $sensorModelID );
        $this->assignRef('groupvalues', $groupvalues);
        
		//**** For the Sensor Model Documentation section
		
		// Make dirs for this Sensor if we forgot to make them
    	$this->sensorModel->makeSensorModelDirs();

    	// Setup the file browser
		$basepath = $sensorModel->getPathname() . "/Documentation";
		$baselink = '';
		$rootname = "Sensor Model Documentation";
		$dfObject = new DataFileBrowser(null, $baselink, $basepath, $rootname);
		$datafilebrowser = $dfObject->makeDataFileBrowser();
        $this->assignRef('datafilebrowser', $datafilebrowser);
		
        
        parent::display($tpl);
    }
    
    
    
	function getSensorModelMeasurementRange($sensorModel)
	{
		$measRange = "";
		$minMeas = $sensorModel->getMinMeasuredValue();
		$maxMeas = $sensorModel->getMaxMeasuredValue();
		if ($minMeas !== null) $measRange = "from $minMeas";
		if ($maxMeas !== null) $measRange .= " to $maxMeas";
		
		if ($measRange !== null)
		{
			$measRange .= " ".$this->getUnits( $sensorModel->getMeasuredValueUnitsId() );
		}
		
		return $measRange;
	}
    
    
	function getSensitivity($sensorModel)
	{
		$sens = $sensorModel->getSensitivity();
		
		if ($sens !== null )
		{
			$sens .= " " . $this->getUnits( $sensorModel->getSensitivityUnitsId() );
		}
		return $sens;
	}
	
	
	function getUnits($unitId)
	{
		if ( is_numeric( $unitId ))
		{
			$measUnit = MeasurementUnitPeer::find( $unitId );
			$units = $measUnit->getAbbreviation();
      
			if ( !$units ) $units = $measUnit->getName();
		}
		else
		{
			$units = $unitId;
		}
		return $units;
	}
	
	
	function getSensorModelOperatingTemperature($sensorModel)
	{
		$OpTRange = "";
		$minOpT = $sensorModel->getMinOpTemp();
		$maxOpT = $sensorModel->getMaxOpTemp();
		if ($minOpT !== null) $OpTRange = "from $minOpT";
		if ($maxOpT !== null) $OpTRange .= " to $maxOpT";
		
		if ($OpTRange !== null )
		{
			$OpTRange .= " " . $this->getUnits($sensorModel->getTempUnitsId() );
		}
		
		return $OpTRange;
	}
	
	
}
