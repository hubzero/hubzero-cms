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
 
class sitesViewuploadsensorcalibrations extends JView
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
        $pathway->addItem( "Sensors",  JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID));
    	
        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs); 

        // Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);
        
        
        parent::display($tpl);
	}
    
    
}
