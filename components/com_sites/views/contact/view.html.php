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
 
class sitesViewContact extends JView
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
        $pathway->addItem( $fac_name, JRoute::_('/index.php?option=com_sites&view=site&id=' . $facilityID));
        
        // Add Sensor tab info to breadcrumb
        $pathway->addItem( "Contact Info",  JRoute::_('/index.php?option=com_sites&view=contact&id=' . $facilityID));
                
    	
    	// Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);
        $this->assignRef('facilityID', $facilityID);
        
    	// Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(1, $facilityID);
        $this->assignRef('tabs', $tabs); 
        
        
	$facilityContactPerson = $this->findFacilityContactPerson($facility);      
        $this->assignRef('facilityContactPerson', $facilityContactPerson); 
		
        // For all the files to be included 
        $fileBrowserObj = new DataFileBrowserSimple($facility);
        $this->assignRef('fileBrowserObj', $fileBrowserObj); 
        $infotype =  "VisitorInformation";
        
        
        $drivingFDs = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Driving Instruction", "");
        $driving_datafiles = array();

        foreach($drivingFDs as $fd)
        {
            $driving_datafiles[] = $fd->getDataFile();
        }


        $mapFDs = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Site Location Map", "");
        $map_datafiles = array();

        foreach($mapFDs as $fd)
        {
            $map_datafiles[] = $fd->getDataFile();
        }


        $localFDs = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Local Area Information", "");
        $local_datafiles = array();

        foreach($localFDs as $fd)
        {
            $local_datafiles[] = $fd->getDataFile();
        }


        $additionalFDs = FacilityDataFilePeer::findByDetails($facilityID, $infotype, "Additional Document", "");
        $additional_datafiles = array();

        foreach($additionalFDs as $fd)
        {
            $additional_datafiles[] = $fd->getDataFile();
        }


        $this->assignRef('driving_datafiles', $driving_datafiles);
        $this->assignRef('map_datafiles', $map_datafiles);
        $this->assignRef('local_datafiles', $local_datafiles);
        $this->assignRef('additional_datafiles', $additional_datafiles);

        // Some rights based lookups
        $allowEdit = FacilityHelper::canEdit($facility);
        $this->assignRef('allowEdit', $allowEdit);
        $allowCreate = FacilityHelper::canCreate($facility);
        $this->assignRef('allowCreate', $allowCreate);

        // Code the redirect URL
        $uri  =& JURI::getInstance();
        $redirectURL = $uri->toString(array('path', 'query'));
        $redirectURL = base64_encode($redirectURL);
        $redirectURL = $redirectURL;
        $this->assignRef('redirectURL', $redirectURL);

        
        parent::display($tpl);
    }
    
    
    
    
	function findFacilityContactPerson($facility)
	{
            #   assumed that there is only one 'Site Contact' but this easily changed
            #   Get the Role Id for 'Site Contact';
            $roles = RolePeer::findByName("Site Contact");
            $role = $roles[0];

            // Get a list of people with "Site Contact" role on the facility.
            $PERs = PersonEntityRolePeer::findByEntityRole($facility, $role);
            if(count($PERs) > 0)
            {
                    $PER = $PERs[0];
                    return $PER->getPerson();
            }
	}
    
    
    
    
    
    
}
