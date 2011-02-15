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
 
class sitereportsViewreports extends JView
{
    function display($tpl = null)
    {

        // Tabs for the page
        $tabs = SiteReportsHelper::getFacilityTabs(SiteReportsHelper::tabReports);
        $this->assignRef('tabs', $tabs);


        parent::display($tpl);
    }


}
