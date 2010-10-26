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
 
class sitesVieweditsitefile extends JView
{
	
    function display($tpl = null)
    {
        // Grab facility from Oracle
	$facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef( 'FacilityName', $fac_name);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             
        $pathway->addItem( $fac_name,  JRoute::_('/index.php?option=com_sites&view=site&id=' . $facilityID));
                
        $this->assignRef('facility', $facility); 
        $this->assignRef('facilityID', $facilityID);
        
        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(-1, $facilityID);
        $this->assignRef('tabs', $tabs); 

        // These three are used for constructing the path to a general doc upload for the facility
        // i.e. driving directions, Training and certification, Education and Outreach, etc
        $infotype = JRequest::getVar('infotype');
        $subinfo = JRequest::getVar('subinfo');
        $groupby = JRequest::getVar('groupby');

        // These will be set when the uploaded file is a equipment documentation file, sensor doc file
        // or sensormodel file, etc
        $equipmentid = JRequest::getVar('equipmentid');
        $sensorid = JRequest::getVar('sensorid');
        $sensormodelid = JRequest::getVar('sensormodelid');
        $calibrationid = JRequest::getVar('calibrationid');
        $equipmentmodelid = JRequest::getVar('equipmentmodelid');
        $equipmentmodelfiletype = JRequest::getVar('equipmentmodelfiletype');

        // Where to go when we are done, base64 encoded
        $redirectURL = JRequest::getVar('redirectURL');

        // Some conditional checks based on where the file is going
        if(!empty($infotype))
        {
            $filedescription = $infotype . ':' . $subinfo . ( !empty($groupby) ? ": " . $groupby : '' );
            $task = 'savefile'; // generic faciltiy save file
        }
        elseif(!empty($equipmentid))
        {
            $equipment = EquipmentPeer::find($equipmentid);
            $filedescription = 'Equipment documentation for: ' . $equipment->getName();
            $task = 'saveequipmentdocfile'; // specific save for equipment doc

        }
        elseif(!empty($sensorid))
        {
            /* @var $sensor Sensor */
            $sensor = SensorPeer::find($sensorid);
            $filedescription = 'Sensor documentation for: ' . $sensor->getname();
            $task = 'savesensordocfile'; // specific save for sensor doc
        }
        elseif(!empty($sensormodelid))
        {
            /* @var $sensormodel SensorModel */
            $sensormodel = SensorModelPeer::find($sensormodelid);
            $filedescription = 'Sensor Model documentation for: ' . $sensormodel->getname();
            $task = 'savesensormodeldocfile'; // specific save for sensormodel doc
        }
        elseif(!empty($calibrationid))
        {
            /* @var $calibration Calibration */
            $calibration = CalibrationPeer::find($calibrationid);
            $filedescription = 'Calibration documentation for: ' . $calibration->getSensor()->getName();
            $task = 'savecalibrationdocfile'; // specific save for sensormodel doc
        }
        elseif(!empty($equipmentmodelid))
        {
            $equipmentmodel = EquipmentModelPeer::find($equipmentmodelid);
            $filedescription = 'Uploading Equipment Model documentation file';
            $task = 'saveequipmentmodelfile'; // specific save for equipmentmodel
        }
        else
        {
            return JError::raiseError( 500, "Editsitefile does not have information on file destination");
        }

        // Pass to the view
        $this->assignRef('infotype', $infotype);
        $this->assignRef('subinfo', $subinfo);
        $this->assignRef('groupby', $groupby);
        $this->assignRef('equipmentid', $equipmentid);
        $this->assignRef('sensorid', $sensorid);
        $this->assignRef('sensormodelid', $sensormodelid);
        $this->assignRef('calibrationid', $calibrationid);
        $this->assignRef('equipmentmodelid', $equipmentmodelid);
        $this->assignRef('equipmentmodelfiletype', $equipmentmodelfiletype);
        $this->assignRef('task', $task);
        $this->assignRef('filedescription', $filedescription);
        $this->assignRef('redirectURL', $redirectURL);

        parent::display($tpl);
    }




    function getDocTypeOptions($default = null) {
        if (!$this->docTypeOptions) {
            return;
        }

        foreach ($this->docTypeOptions as $doctype) {
            $sel = '';
            if ($default && $doctype->getId() == $default->getId()) {
                $sel = 'selected';
            }
            print("<option value=\"{$doctype->getId()}\" $sel>{$doctype->getName()}</option>\n");
            }
    }

}
