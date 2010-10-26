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
            $testTime  = JRequest::getvar('test_time');
            $testStart = JRequest::getVar('test_start');
            $testEnd   = JRequest::getVar('test_end');
            $testTz    = JRequest::getVar('test_tz');
            $cName     = JRequest::getVar('contact_name');
            $cEmail    = JRequest::getVar('contact_email');
            $mURL      = JRequest::getVar('movie_url');
            $expAct = isset($_REQUEST['active']) && $_REQUEST['active'] ?  1 : 0;


            // Name requirement validation
            if($expName == '')
                $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Experiment Name is a required field';

            // Test Date/Time required and format validation
            if(empty($testDt) || empty($testTime) )
               $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Experiment Test Date/Time is required';
            else
            {
                // Parse the date
                preg_match ('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4}) (20|21|22|23|[01]\d|\d):([0-5]\d{1,2})/',  (trim($testDt) . ' ' . trim($testTime)), $matches_dt);

                //var_dump($matches_dt);
                //return;

                if( (count($matches_dt) != 6) || (!checkdate( $matches_dt[1], $matches_dt[2], $matches_dt[3])))
                    $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Please enter a valid Test Date/Time.';
            }
            
            // Start date validation
            if(empty($testStart))
               $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Experiment Start Date is required';
            else
            {
                // Parse the date
                preg_match ('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/',  $testStart, $matches_start);

                if( (count($matches_start) != 4) || !checkdate( $matches_start[1], $matches_start[2], $matches_start[3]))
                   $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Please enter a valid Start Date.';
            }


            // Endate validation
            if(!empty($testEnd))
            {
                preg_match ('/([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})/', $testEnd, $matches_end);

                //var_dump($testEnd);
                //echo '<br/>';
                //var_dump($matches_end);
                //return;

                if(count($matches_end) == 4)
                {
                    if(!checkdate($matches_end[1], $matches_end[2], $matches_end[3]))
                        $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Please enter a valid End Date';
                }
                else
                   $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Please enter a valid End Date';

            }

            // Name requirement validation
            if($cName == '')
                $errorMsg .= (strlen($errorMsg) ? '<br/>' : '' ) . 'Contact Name is a required field';


            // Add or update ONLY if all went well
            if($errorMsg == '')
            {

            // Good ol date formatting, grab parsed date from preg_match above
            $testDt = $matches_dt[3].'-'.$matches_dt[1].'-'.$matches_dt[2]." ".$matches_dt[4].':'.$matches_dt[5];

            $testStart = $matches_start[3]."-".$matches_start[1]."-".$matches_start[2];

            if(!empty($testEnd))
                $testEnd = $matches_end[3]."-".$matches_end[1]."-".$matches_end[2];


            // Update experiment
                if($experimentid > -1)
                {

                    NAWIPeer::update_NAWI_exp($experimentid,
                            $expName,
                            $expDesc,
                            $expPhase,
                            $testDt,
                            $testStart,
                            $testEnd,
                            $testTz,
                            $cName,
                            $cEmail,
                            $mURL,
                            $expAct);

                    $msg = 'Experiment updated sucessfully';

                    JRequest::setVar('msg', $msg);
                    JRequest::setVar('errorMsg', $errorMsg);
                    JRequest::setVar('id', $facilityid);
                    JRequest::setVar('experimentid', $experimentid);
                    JRequest::setVar('view','editexperiment');
                    parent::display();
                }
                else //Add new experiment
                {

                    $new_nawi = new NAWI();
                    $new_nawi->setExperimentName($expName);
                    $new_nawi->setExperimentDescription($expDesc);
                    $new_nawi->setExperimentPhase($expPhase);
                    $new_nawi->setTestDate($testDt);
                    $new_nawi->setTestStartDate($testStart);
                    $new_nawi->setTestEndDate($testEnd);
                    $new_nawi->setTestTimeZone($testTz);
                    $new_nawi->setContactName($cName);
                    $new_nawi->setContactEmail($cEmail);
                    $new_nawi->setMovieUrl($mURL);
                    $new_nawi->setActive($expAct);
                    $new_nawi->save();
                    $nawiid = $new_nawi->getId();

                    $new_nawifac = new NAWIFacility();
                    $new_nawifac->setNawiId($nawiid);
                    $new_nawifac->setFacilityId($facilityid);
                    $new_nawifac->save();



                    $msg = 'Experiment added sucessfully';
                    JRequest::setVar('msg', $msg);
                    JRequest::setVar('errorMsg', $errorMsg);
                    JRequest::setVar('experimentid', $nawiid);

                    JRequest::setVar('view','editexperiment');
                    parent::display();
                }

            }
            else // validation error
            {
                    JRequest::setVar('msg', $msg);
                    JRequest::setVar('errorMsg', $errorMsg);
                    
                    // Be nice and set all previously entered values for the error page display
                    JRequest::setVar('exp_name', $expName);
                    JRequest::setVar('exp_descript', $expDesc);
                    JRequest::setVar('exp_phase', $expPhase);
                    JRequest::setVar('test_dt', $testDt);
                    JRequest::setvar('test_time', $testTime);
                    JRequest::setVar('test_start', $testStart);
                    JRequest::setVar('test_end', $testEnd);
                    JRequest::setVar('test_tz', $testTz);
                    JRequest::setVar('contact_name', $cName);
                    JRequest::setVar('contact_email', $cEmail);
                    JRequest::setVar('movie_url', $mURL);
                    JRequest::setVar('active', $expAct);

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
