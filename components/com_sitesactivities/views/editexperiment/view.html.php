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
 
class sitesactivitiesVieweditexperiment extends JView
{
    function display($tpl = null)
    {
    	// Get the tabs for the top of the page
        $tabs = SitesActivitiesHelper:: getSitesActivitiesTabs(-1);
        $this->assignRef('tabs', $tabs); 
    	    	
    	$document  =& JFactory::getDocument();
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();
        $pathway   =& $mainframe->getPathway();

        // Get the site
    	$facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $experimentID = JRequest::getVar('experimentid');

    	if($facility)
            $facilityName = $facility->getName();
        else
            $facilityName = '';
        	
        $this->assignRef('facilityName', $facilityName);
        $this->assignRef('facilityid', $facilityID);
        $this->assignRef('experimentid', $experimentID);


        $canedit = SitesActivitiesHelper::canEdit($facility);
        $this->assignRef('canedit', $canedit);

        // Get the current site status
        $sitestatus = SitesActivitiesHelper::getNawiStatus($facilityID);
        $this->assignRef('status', $sitestatus);

        // Get activity (or experiment)
        if($experimentID > -1)
        {
            $experiment = NAWIFacilityPeer::findByFacilityAndNawi($facilityID, $experimentID);

            if(count($experiment) > 0)
            {
                $nawifac = $experiment[0];

                $p = $nawifac->getNAWI();

                $expName = $p->getExperimentName();
                $expDesc = $p->getExperimentDescription();
                $expPhase = $p->getExperimentPhase();

                $this->assignRef('name', $expName);
                $this->assignRef('exp_descript', $expDesc);
                $this->assignRef('expphase', $expPhase);

                /*
                $expPhase = $p->getExperimentPhase();
                $testDt = date("m-d-Y H:i", strtotime($p->getTestDate()));
                $testTz = $p->getTestTimeZone();
                $testStart = date("m-d-Y", strtotime($p->getTestStartDate()));
                $testEnd = date("m-d-Y", strtotime($p->getTestEndDate()));
                $cName = $p->getContactName();
                $cEmail = $p->getContactEmail();
                $mURL = $p->getMovieUrl();
                $expAct = $p->getActive();
                */
            }
            else
            {
                JError::raiseError( 500, "Cannot find experimentID:" . experimentID . ' for facitilityID:' .$facilityID );
            }
        }
        else
        {
           // We must be adding a new experiemnt

        }


        // See if user should be allowed to see this page
        if($canedit)
            parent::display($tpl);
        else
            echo 'You do not have permission to edit an experiment for this site';

    }







}
