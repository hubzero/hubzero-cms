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
 
class sitesViewStaff extends JView
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
        $pathway->addItem( "Staff",  JRoute::_('index.php?option=com_sites&view=staff&id=' . $facilityID));
            	
    	// Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);
        
    	// Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(2, $facilityID);
        $this->assignRef('tabs', $tabs); 
        
        $facilityMembers = $this->getFacilityMembers($facilityID);
        $this->assignRef('members', $facilityMembers);
        $this->assignRef('facility', $facility);
        $this->assignRef('facilityID', $facilityID);

        // Grab a complete list of users that can be granted rights to this site
        $candidates = PersonPeer::getCandidateMembersForEntity($facilityID, DomainEntityType::ENTITY_TYPE_FACILITY);
        $candidates->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $this->assignRef('candidates', $candidates);

        // See if current logged in user should be presented an edit button
	$allowGrant = FacilityHelper::canGrant($facility);
	$this->assignRef('allowGrant', $allowGrant);

        $permissionArr = AuthorizationPeer::listPermissionsForAllPeopleInEntity($facility);
        $this->assignRef('permissionArr', $permissionArr);

        parent::display($tpl);
    }
    
    
    public function getFacilityMembers($facid)
    {
        return PersonPeer::findMembersPermissionsForEntity($facid, DomainEntityType::ENTITY_TYPE_FACILITY);
    }
    
    
    
}
