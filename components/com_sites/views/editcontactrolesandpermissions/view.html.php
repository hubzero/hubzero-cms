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
 
class sitesVieweditcontactrolesandpermissions extends JView
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
        $pathway->addItem( $fac_name,  JRoute::_('/index.php?option=com_sites&view=site&id=' . $facilityID));
                
        // Add Staff tab info to breadcrumb
        $pathway->addItem( "Staff",  JRoute::_('/index.php?option=com_sites&view=staff&id=' . $facilityID));
        
    	// Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);
        
    	// Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(2, $facilityID);
        $this->assignRef('tabs', $tabs); 
        
		// Get all facility related roles
        $roles = RolePeer::findByEntityType(DomainEntityType::ENTITY_TYPE_FACILITY);
        $this->assignRef('roles', $roles); 

        $this->assignRef('facilityID', $facilityID);
        
		// Edit a member
//    	if ($personId = $request->getProperty("personId")) 
//    	{
//			$editPerson = PersonPeer::find($personId);
//		}
    	
        $user =& JFactory::getUser();
		$username = $user->get('username');
       
        $auth = Authorizer::getInstanceForUseOnHub($username, $facilityID, DomainEntityType::ENTITY_TYPE_FACILITY);
        $this->assignref('auth',$auth);

        // The id of the person we are trying to edit
        $editPersonID = JRequest::getVar('editpersonid');
        $this->assignref('editPersonID',$editPersonID);
        
        // The person we are trying to edit
        $editPerson = PersonPeer::find($editPersonID);
        $this->assignref('editPerson',$editPerson);
        
        
        parent::display($tpl);
    }
    
    
    
    
	public function hasRole(Person $person, Role $role, $facility) 
	{
		$pers = PersonEntityRolePeer::findByPersonEntity($person, $facility);

		foreach ($pers as $per) {
		
		// Hack !!! should be $role->getId(), however, we were messed up for records have same role name but different entity_type_id in database
		//if ($per->getRole()->getId() == $role->getId()) {
		if ($per->getRole()->getName() == $role->getName()) 
		{
			return true;
		}
    }

    
    
    return false;
  }
    
    
    
    
    
    
}
