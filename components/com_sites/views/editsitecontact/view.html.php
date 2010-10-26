<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitesVieweditsitecontact extends JView
{

    function display($tpl = null)
    {

        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef('FacilityName', $fac_name);
        $this->assignRef('facility', $facility);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add current page
        $pathway->addItem("Change Facility Contact", JRoute::_('index.php?option=com_sites&view=editfacilitycontact&id=' . $facilityID ));

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(1, $facilityID);
        $this->assignRef('tabs', $tabs); 

        
        $sitePeople = PersonPeer::findAllInEntity($facility);

        $contactPersonOptionsList = '';
        foreach ($sitePeople as $site_person)
        {
            $contactPersonOptionsList .= '<option value="' . $site_person->getId() . '">' . $site_person->getLastName() . ', ' . $site_person->getFirstName() . ' (' . $site_person->getUserName() . ')</option>';
        }
        $this->assignRef('contactPersonOptionsList', $contactPersonOptionsList);

        $allowEdit = FacilityHelper::canEdit($facility);
        if($allowEdit)
                parent::display($tpl);
            else
                echo 'You are not authorized to change site contacts';

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




}// end class
