<?php
/**
 * Primary controller file for the siteactivities component 
 * 
 * @package		NEEShub 
 * @author		David Benham (dbenham@purdue.edu)
 * @copyright	Copyright 2010 by NEESCommIT
 */
 
// No direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport('joomla.application.component.controller');
jimport('joomla.application.component.view');

//require_once "bulkupload/FileUploadReader.php";
//require_once "lib/data/Calibration.php";


/**
 *Facility Component Controller
 *
 * @package    NEEShub
 * @subpackage Components
 */
class SitesActivitiesController extends JController
{

	function __construct()
	{
            parent::__construct();
   
            $this->registerTask( 'updatesitestatus' , 'updatesitestatus' );
            $this->registerTask( 'updateexperiment' , 'updateexperiment' );
        }
	




    function updateexperiment()
    {
        $msg = '';
        $errorMsg = '';

        $facilityid = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityid);
        $canEdit = SitesActivitiesHelper::canEdit($facility);
        $experimentid = JRequest::getVar('experimentid');

        // Think this error is hard enough to warrant 500 code, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit)
            JError::raiseError( 500, "You do not have permission to edit an experiment of this site");
        else
        {

            // Grab form data
            $expName   = JRequest::getVar('exp_name');
            $expDesc   = JRequest::getVar('exp_descript');
            $expPhase  = JRequest::getVar('exp_phase');
            $testDt    = JRequest::getVar('test_dt');
            $testStart = JRequest::getVar('test_start');
            $testEnd   = JRequest::getVar('test_end');
            $testTz    = JRequest::getVar('test_tz');
            $cName     = JRequest::getVar('contact_name');
            $cEmail    = JRequest::getVar('contact_email');
            $mURL      = JRequest::getVar('movie_url');

            $expAct = isset($_REQUEST['active']) && $_REQUEST['active'] ?  1 : 0;


            // do validations here
            if($expName == '')
                $errorMsg = 'Name is a required field';

            // Start date time validation check

            // Add or update ONLY if all went well
            if($errorMsg == '')
            {
                // Update experiment
                if($experimentid > -1)
                {
                    NAWIPeer::update_NAWI_exp($experimentid, $expName, $expDesc, $expPhase, $testDt, $testStart, $testEnd, $testTz, $cName, $cEmail, $mURL, $expAct);

                    $msg = 'Site status updated sucessfully';

                    JRequest::setVar('msg', $msg);
                    JRequest::setVar('errorMsg', $errorMsg);

                    JRequest::setVar('view','upcomingexperiments');
                    parent::display();
                }
                else //Add new experiment
                {




                    JRequest::setVar('msg', $msg);
                    JRequest::setVar('errorMsg', $errorMsg);

                    JRequest::setVar('view','upcomingexperiments');
                    parent::display();
                }

            }
            else
            {
                    JRequest::setVar('msg', $msg);
                    JRequest::setVar('errorMsg', $errorMsg);

                    //Return to the experiment edit page
                    JRequest::setVar('view','editexperiment');
                    parent::display();
            }

        }





    }



    function updatesitestatus()
    {
        $msg = '';
        $errorMsg = '';

        $facilityID = JRequest::getVar('id');
        $facility = FacilityPeer::find($facilityID);
        $canEdit = SitesActivitiesHelper::canEdit($facility);
        $optstat = JRequest::getVar('optstat');

        // Think this error is hard enough to warrant 500 code, no legit reason to get here unless
        // you are hacking or a major problem has occured
        if(!$canEdit)
            JError::raiseError( 500, "You do not have permission to edit the status of this site");
        else
        {
            FacilityPeer::updateNAWI_Status($facilityID, $optstat);

            $msg = 'Site status updated sucessfully';

            JRequest::setVar('msg', $msg);
            JRequest::setVar('errorMsg', $errorMsg);

            JRequest::setVar('view','upcomingexperiments');
            parent::display();
        }
    }


	
    function display()
    {
    	parent::display();
    }
  
}
