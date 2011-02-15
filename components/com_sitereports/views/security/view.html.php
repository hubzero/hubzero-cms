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
 
class sitereportsViewSecurity extends JView
{

    function display($tpl = null)
    {

        // Tabs for the page
        $tabs = SiteReportsHelper::getFacilityTabs(SiteReportsHelper::tabSecurity);
        $this->assignRef('tabs', $tabs);

        // Grab the Facility_ID of the SiteReportsSite we're looking to assign users to (1002 is the facilityID of NEESit)
        $facilityID = JRequest::getVar('facilityid', 1002);

        if($facilityID == -1)
            return JError::raiseError( 500, "No facilityid provided");

        // Get the siteReportsSite object that corresponds to the facility
        /* @var $siteReportsSite SiteReportsSite */
        $siteReportsSite = SiteReportsHelper::getSiteReportsSite($facilityID);

        // Grab a complete list of users that can be granted rights
        $candidates = PersonPeer::getCandidateMembersForEntity($siteReportsSite->getID(), DomainEntityType::ENTITY_TYPE_SITEREPORTSSITE);
        $candidates->setFetchmode(ResultSet::FETCHMODE_ASSOC);
        $this->assignRef('candidates', $candidates);

        // See if current logged in user should be presented an edit button
	$allowPageView = SiteReportsHelper::canGrantOnAtLeastOneSite();
        $grantCurrentSite = SiteReportsHelper::canRun($siteReportsSite);
	$this->assignRef('allowPageView', $allowPageView);
	$this->assignRef('grantCurrentSite', $grantCurrentSite);

        // List of users who are already given rights
        $usersWithRights = PersonPeer::findMembersPermissionsForEntity( $siteReportsSite->getID(), DomainEntityType::ENTITY_TYPE_SITEREPORTSSITE);

        //Grab all SiteReportSites
        $allSiteReportSites = SiteReportsHelper::getSiteReportsSites();

        // Pass to the page
        $this->assignRef('allSiteReportSites', $allSiteReportSites);
        $this->assignRef('sitereportsite', $siteReportsSite);
        $this->assignRef('members', $usersWithRights);
        $this->assignRef('allowGrant', $allowGrant);
        $this->assignRef('facilityid', $facilityID);

        parent::display($tpl);
    }
    
    
    
    
    
}
