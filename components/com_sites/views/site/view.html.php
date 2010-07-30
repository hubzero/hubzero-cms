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
 
class sitesViewSite extends JView
{
	
    function display($tpl = null)
    {
        // Grab facility from Oracle
	$facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
		
	//echo(print_r($facility));
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef( 'FacilityName', $fac_name);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name ); 
                    
        // Add facility name to breadcrumb
        $pathway->addItem( $fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));
        
        // Add Sensor tab info to breadcrumb
        $pathway->addItem( "Main",  JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));
                
        // Get url for facility image
	$imgFacilityURL = "/components/com_sites/images/facility_" . strtolower($fac_shortname) . ".jpg";
        $this->assignRef('imgFacilityURL', $imgFacilityURL);
  		
        // Get other facility info
        $this->assignRef('SiteName', $facility->getSiteName()); 
        $this->assignRef('Department', $facility->getDepartment());
        $this->assignRef('Laboratory', $facility->getLaboratory());
        $this->assignRef('NsfAwardUrl', $facility->getNsfAwardUrl()); 
        $this->assignRef('SiteUrl', $facility->getUrl());
        $this->assignRef('SiteDescription', $facility->getDescription());
        $this->assignRef('NsfAcknowledgement', $facility->getNsfAcknowledgement()); 
        $this->assignRef('facilityID', $facilityID); 
        
        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(0, $facilityID);
        $this->assignRef('tabs', $tabs); 
        
        $fileBrowserObj = new DataFileBrowserSimple($facility);
        $this->assignRef('fileBrowserObj', $fileBrowserObj); 

        $infotype = "Facility";

        $introDF 	= FacilityHelper::getFacilityDataFile($facilityID, $infotype, "Site Introduction");
        $descDF 	= FacilityHelper::getFacilityDataFile($facilityID, $infotype, "Site Description");
        $historyDF 	= FacilityHelper::getFacilityDataFile($facilityID, $infotype, "History");

        $introDataFileArr = array();
        $descDataFileArr = array();
        $historyDataFileArr = array();

        if($introDF) $introDataFileArr[] = $introDF;
        if($descDF) $descDataFileArr[] = $descDF;
        if($historyDF) $historyDataFileArr[] = $historyDF;

        $this->assignRef('introDataFileArr', $introDataFileArr);
        $this->assignRef('descDataFileArr', $descDataFileArr);
        $this->assignRef('historyDataFileArr', $historyDataFileArr);

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
    
    
   
}
