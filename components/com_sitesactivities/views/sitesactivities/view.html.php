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
 
class sitesactivitiesViewsitesactivities extends JView
{
    function display($tpl = null)
    {
    	// Get the tabs for the top of the page
        $tabs = SitesActivitiesHelper:: getSitesActivitiesTabs(0);
        $this->assignRef('tabs', $tabs); 

        $facilityID = JRequest::getVar('id');

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();
        $pathway   =& $mainframe->getPathway();

        // Add crrent page name to breadcrumb
        $pathway->addItem( 'Activities Map', JRoute::_('/sitesactivities'));
    	
        parent::display($tpl);
    }
}
