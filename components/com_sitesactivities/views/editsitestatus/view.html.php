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
 
class sitesactivitiesVieweditsitestatus extends JView
{
    function display($tpl = null)
    {
    	// Get the tabs for the top of the page
        $tabs = SitesActivitiesHelper:: getSitesActivitiesTabs(-1);
        $this->assignRef('tabs', $tabs); 
    	    	
    	$document  =& JFactory::getDocument();
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();
        $pathway   =& $mainframe->getPathway();

        // Get the site
    	$facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);

    	if($facility)
            $facilityName = $facility->getName();
        else
            $facilityName = '';
        	
        $this->assignRef('facilityName', $facilityName);
        $this->assignRef('facilityid', $facilityID);

        $canedit = SitesActivitiesHelper::canEdit($facility);
        $this->assignRef('canedit', $canedit);

        // Get the current site status
        $sitestatus = SitesActivitiesHelper::getNawiStatus($facilityID);
        $this->assignRef('status', $sitestatus);

        if($canedit)
            parent::display($tpl);
        else
            echo 'You do not have permission to edit the status for this site';

    }







}
