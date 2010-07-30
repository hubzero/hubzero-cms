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

        $infotype = JRequest::getVar('infotype');
        $subinfo = JRequest::getVar('subinfo');
        $groupby = JRequest::getVar('groupby');
        $redirectURL = JRequest::getVar('redirectURL');

        $this->assignRef('infotype', $infotype);
        $this->assignRef('subinfo', $subinfo);
        $this->assignRef('groupby', $groupby);
        $this->assignRef( 'redirectURL', $redirectURL);
		
        parent::display($tpl);
	}
    
}
