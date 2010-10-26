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

        // Breadcrumb additions
        $pathway->addItem( 'Site Experiments', JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments'));

        $pathway->addItem( $facility->getName() . ' experiments', JRoute::_('/index.php?option=com_sitesactivities&id=' .
                $facilityID . '&view=upcomingexperiments'));

        $pathway->addItem( 'Edit Experiment', JRoute::_('/index.php?option=com_sitesactivities' .
                '&view=editexperiment&id=' . $facilityID . '&experimentid=' . $experimentID));


        $canedit = SitesActivitiesHelper::canEdit($facility);
        $cancreate = SitesActivitiesHelper::canCreate($facility);

        // Get the current site status
        $sitestatus = SitesActivitiesHelper::getNawiStatus($facilityID);
        $this->assignRef('status', $sitestatus);

        // Get activity (or experiment)
        if($experimentID > -1)
        {

            if(!$canedit)
                JError::raiseError( 500, 'You do not have rights to edit experiemntID:' . experimentID . ' for facitilityID:' .$facilityID );

            $experiment = NAWIFacilityPeer::findByFacilityAndNawi($facilityID, $experimentID);

            if(count($experiment) > 0)
            {
                // If this is an failed edit submission with errors
                $errorMsg = trim(JRequest::getVar('errorMsg', ''));
                if(!empty($errorMsg))
                {
                    // Pass everything to the view from the previous form submission
                    $this->setFormFromPreviousSubmission();
                }
                else // this is an initial edit
                {

                    /* @var $nawifac NAWIFacility */
                    $nawifac = $experiment[0];

                    /* @var $p NAWI */
                    $p = $nawifac->getNAWI();

                    $expName = $p->getExperimentName();
                    $expDesc = $p->getExperimentDescription();
                    $expPhase = $p->getExperimentPhase();

                    // testDateTime logic (the field is spread across two fields)
                    $testDateTime = $p->getTestDate('m-d-Y H:i');

                    // Shouldn't be empty, but lets not assume
                    if(!empty($testDateTime))
                    {
                        $testDateTimeArray = explode(' ', $testDateTime);
                        $testTime = $testDateTimeArray[1];
                        $testDate = $testDateTimeArray[0];
                    }
                    else
                    {
                        $testTime = "";
                        $testDate = "";
                    }

                    $timezone = $p->getTestTimeZone();

                    $startDate = $p->getTestStartDate('m-d-Y');
                    $endDate = $p->getTestEndDate('m-d-Y');
                    $contactName = $p->getContactName();
                    $contactEmail = $p->getContactEmail();
                    $movieURL = $p->getMovieUrl();
                    $feedsAvailable = $p->getActive();

                    // Pass everything to the view
                    $this->assignRef('name', $expName);
                    $this->assignRef('exp_descript', $expDesc);
                    $this->assignRef('expphase', $expPhase);
                    $this->assignRef('testDate', $testDate);
                    $this->assignRef('testTime', $testTime);
                    $this->assignRef('timezone', $timezone);
                    $this->assignRef('startDate', $startDate);
                    $this->assignRef('endDate', $endDate);
                    $this->assignRef('contactName', $contactName);
                    $this->assignRef('contactEmail', $contactEmail);
                    $this->assignRef('movieURL', $movieURL);
                    $this->assignRef('feedsAvailable', $feedsAvailable);

                }
            }
            else
            {
                JError::raiseError( 500, "Cannot find experimentID:" . experimentID . ' for facitilityID:' .$facilityID );
            }
        }
        else
        {
           // We must be adding a new experiemnt
            if(!$cancreate)
                JError::raiseError( 500, 'You do not have rights to create and experiemnt for facitilityID:' .$facilityID );

                $errorMsg = trim(JRequest::getVar('errorMsg', ''));
                if(!empty($errorMsg))
                {
                    // Pass everything to the view from the previous form submission
                    $this->setFormFromPreviousSubmission();
                }
                else
                {
                    // Easier to do this than to have the form conditionally grab stuff for edit/versus new items
                    $blank = '';
                    $this->assignRef('name', $blank);
                    $this->assignRef('exp_descript', $blank);
                    $this->assignRef('expphase', $blank);
                    $this->assignRef('testDate', $blank);
                    $this->assignRef('testTime', $blank);
                    $this->assignRef('timezone', $blank);
                    $this->assignRef('startDate', $blank);
                    $this->assignRef('endDate', $blank);
                    $this->assignRef('contactName', $blank);
                    $this->assignRef('contactEmail', $blank);
                    $this->assignRef('movieURL', $blank);
                    $this->assignRef('feedsAvailable', $blank);
                }

        }


        // See if user should be allowed to see this page
        if($canedit)
            parent::display($tpl);
        else
            echo 'You do not have permission to edit an experiment for this site';

    }


    function setFormFromPreviousSubmission()
    {
        // Pass everything to the view from the previous form submission
        $expName = JRequest::getVar('exp_name');
        $expDesc = JRequest::getVar('exp_descript');
        $expPhase = JRequest::getVar('exp_phase');
        $testDate = JRequest::getVar('test_dt');
        $testTime = JRequest::getVar('test_time');
        $timezone = JRequest::getVar('test_tz');
        $startDate = JRequest::getVar('test_start');
        $endDate = JRequest::getVar('test_end');
        $contactName = JRequest::getVar('contact_name');
        $contactEmail = JRequest::getVar('contact_email');
        $movieURL = JRequest::getVar('movie_url');
        $feedsAvailable = JRequest::getVar('active');

        $this->assignRef('name', $expName);
        $this->assignRef('exp_descript', $expDesc);
        $this->assignRef('expphase', $expPhase);
        $this->assignRef('testDate', $testDate);
        $this->assignRef('testTime', $testTime);
        $this->assignRef('timezone', $timezone);
        $this->assignRef('startDate', $startDate);
        $this->assignRef('endDate', $endDate);
        $this->assignRef('contactName', $contactName);
        $this->assignRef('contactEmail', $contactEmail);
        $this->assignRef('movieURL', $movieURL);
        $this->assignRef('feedsAvailable', $feedsAvailable);
    }



}
