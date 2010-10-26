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
 
class Sitesviewmajorequipment extends JView
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
                    	
        // Add Sensor tab info to breadcrumb
        $pathway->addItem( "Equipment",  JRoute::_('index.php?option=com_sites&view=majorequipment&id=' . $facilityID));
                
    	// Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);
        
    	// Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(3, $facilityID);
        $this->assignRef('tabs', $tabs); 
    	 
        // Get the equipment
        $majorEquipment = EquipmentPeer::findAllMajorByOrganization($facilityID);
        $this->assignRef('majorEquipment', $majorEquipment); 
        $this->assignRef('facilityID', $facilityID); 
        

        // See if current logged in user can add/edit in this facility
	$canEdit = FacilityHelper::canEdit($facility);
	$canAdd = FacilityHelper::canCreate($facility);
	$this->assignRef('canEdit', $canEdit);
	$this->assignRef('canAdd', $canAdd);

        $addNewEquipment = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=editequipment&id=' . $facilityID . '&equipmentid=-1') . '">Add New Equipment</a>';
        $this->assignRef('addnewequipment', $addNewEquipment);


        // The add new equipment model is reserved for admins and super admins only, it has a sitewide effect
        $user =& JFactory::getUser();
        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
            $isadmin = true;
        $this->assignRef('isadmin', $isadmin);

        $addNewEquipmentModel = '<a class="button2" href="' . JRoute::_('/index.php?option=com_sites&view=editequipmentmodel&id=' . $facilityID . '&equipmentmodelid=-1') . '">Add New Equipment Model</a>';
        $this->assignRef('addnewequipmentmodel', $addNewEquipmentModel);


        parent::display($tpl);
    }
}
