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
 
class sitesactivitiesViewequipmentavailability extends JView
{
    function display($tpl = null)
    {
    	// Get the tabs for the top of the page
        $tabs = SitesActivitiesHelper:: getSitesActivitiesTabs(2);
        $this->assignRef('tabs', $tabs); 
    	
    	JHTML::_('behavior.mootools');
    	
    	$document =& JFactory::getDocument();
    	$js = 'window.addEvent(\'domready\', function(){ init_calendar(); });';
	$document->addScriptDeclaration($js);		

        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();
        $pathway   =& $mainframe->getPathway();
        $document->setTitle('Site Equipment Schedules');

        // Get the site
    	$facilityID = JRequest::getVar('id');

        if($facilityID == null)
        {
            $facilityID = 226; //hardcode the first one
            JRequest::setVar('id', '226');
        }


        $facility = FacilityPeer::find($facilityID);


        // Breadcrumb additions
        $pathway->addItem( 'Site Equipment Schedules', JRoute::_('index.php?option=com_sitesactivities&view=equipmentavailability'));
        $pathway->addItem( $facility->getName() . ' major equipment schedule', JRoute::_('/index.php?option=com_sitesactivities&id=' . $facilityID . '&view=equipmentavailability'));


    	if($facility)
            $facilityName = $facility->getName();
        else
            $facilityName = '';
        	
        $this->assignRef('facilityName', $facilityName);
			
	// Page has no edit, but needs this know if it should display the KB article on how to setup the calendars
        $canedit = SitesActivitiesHelper::canEdit($facility);
        $this->assignRef('canedit', $canedit);

        parent::display($tpl);
    }
}
