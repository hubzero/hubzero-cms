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
 
class sitesactivitiesViewupcomingexperiments extends JView
{
    function display($tpl = null)
    {
    	// Get the tabs for the top of the page
        $tabs = SitesActivitiesHelper:: getSitesActivitiesTabs(1);
        $this->assignRef('tabs', $tabs); 

        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();
        $pathway   =& $mainframe->getPathway();
        $document->setTitle('Site Experiments');

    	// Get the site 
        $facilityID = JRequest::getVar('id');

        if($facilityID == null)
        {
            $facilityID = 226; //hardcode the first one
            JRequest::setVar('id', '226');
        }

        $this->assign('facilityID', $facilityID);
    	$facility = FacilityPeer::find($facilityID);

        // Breadcrumb additions
        $pathway->addItem( 'Site Experiments', JRoute::_('/index.php?option=com_sitesactivities&view=upcomingexperiments'));
        $pathway->addItem( $facility->getName() . ' experiments', JRoute::_('/index.php?option=com_sitesactivities&id=' . $facilityID . '&view=upcomingexperiments'));

        // If a facility is defined for this page
    	if($facility)
        {
            $facilityName = $facility->getName();

            $facName =    $facility->getName();
            $op_status =  $facility->getNawiStatus();
            $userSOM =    $facility->getSiteOpUser();
            $userSysAd =  $facility->getSysadminUser();
            $flexURL =    $facility->getFlexTpsUrl();

            preg_match("/^(https?:\/\/)?([a-zA-Z0-9\\-\\.\\/]+)(\/?site\/?|\/?feeds\/?|\/?collaboration\/?|\/?portal\/?|\/?dvr\/?)?$/Ui", $flexURL, $matches);

            $cleanflexURL = isset($matches[2]) ? "http://" . rtrim($matches[2], '/') : "";
            $flexURL = rtrim($matches[0], '/');
            $first_href = isset($matches[0][2]) ? $matches[0][2] : "";

            $wsURL =      $facility->getUrl();
            $imgURL =     $facility->getImageUrl();
            $facDesc =    $facility->getDescription();
            $userAdmins = $facility->getNawiAdminUsers();

            $this->assignRef('flexURL', $flexURL);
            $this->assignRef('first_href', $first_href);

            ini_set('default_socket_timeout', 5);
            $xmlresult = @file_get_contents("$cleanflexURL/feeds");

            preg_match_all("/<stream\s+id=\"([^\"]*)\"\s+xlink:href=\"([^\"]*)\">/", $xmlresult, $matches, PREG_SET_ORDER);
            $first_href = isset($matches[0][2]) ? $matches[0][2] : "";
            $feedcount = sizeof($matches);

            $this->assignRef('feedcount', $feedcount);

            // Store off some rights for use later in edit and add links
            $canedit = SitesActivitiesHelper::canEdit($facility);
            $this->assignRef('canedit', $canedit);

            $cancreate = SitesActivitiesHelper::canCreate($facility);
            $this->assignRef('cancreate', $cancreate);

            // Get the current site status
            $sitestatus = SitesActivitiesHelper::getNawiStatus($facilityID);
            $translatedStatus = '';


            switch($sitestatus)
            {
                case('NEES'):  $translatedStatus = 'NEES Experiment Today';  break;
                case('FLEX'): $translatedStatus = 'NEES Research Activities';  break;
                case('SHARED'): $translatedStatus = 'NEES Support Activities';  break;
                case('NON_NEES'): $translatedStatus = 'Non-NEES Activities';  break;
            }

            $this->assignRef('translatedStatus', $translatedStatus);

            // Build the summary of experiments table
            $phasetable = "";

            $nawiphase_count = NAWIFacilityPeer::findNawiPhaseCount($facilityID);

            $total = 0;

            foreach ( $nawiphase_count as $p )
            {
            
                if ($p['exp_phase'] == 'DESIGN')
                {
                    $phase_name = "Design";
                }

                elseif ($p['exp_phase'] == 'FABRIC')
                {
                    $phase_name = "Fabrication";
                }

                elseif ($p['exp_phase'] == 'INSTRUMENT')
                {
                    $phase_name = "Instrumentation";
                }

                elseif ($p['exp_phase'] == 'TESTING')
                {
                    $phase_name = "Testing";
                }

                elseif ($p['exp_phase'] == 'DEMOLITION')
                {
                    $phase_name = "Demolition";
                }

                elseif ($p['exp_phase'] == 'ANALYSIS')
                {
                    $phase_name = "Data Review";
                }

                elseif ($p['exp_phase'] == 'COMPLETE')
                {
                    $phase_name = "Complete";
                }

                $phase_total = $p['total'];

                $phasetable .= '<tr><td>' . $phase_name . '</td><td>' . $phase_total. '</td></tr>';
                
                $total += $phase_total;
            }


            // Build the experiments detail table
            $nawiexp = NAWIFacilityPeer::findByFacility($facilityID);

            if (count($nawiexp) > 0)
            {
                $explistdesc = "";
                $k = 0;
                $explist = "";
                $explist_act = "";

                foreach ( $nawiexp as $nawifac )
                {
                    /* @var $nawifac NAWIFacility */
                    /* @var $nawi NAWI */
                    $nawi = $nawifac->getNAWI();
                    $fac = $nawifac->getOrganization();

                    $exp_name = stripslashes( $nawi->getExperimentName() );
                    $exp_descript = SitesActivitiesHelper::CreateHideMoreSection( $nawi->getExperimentDescription(), 250);

                    $movie_url = stripslashes( $nawi->getMovieUrl() );
                    $newDate = $nawi->getTestDate('Y-m-d H:i');
                    $expPhase = $nawi->getExperimentPhase();
                    $testTimeZone = $nawi->getTestTimeZone();
                    $nawiId = $nawi->getId();

                    if($canedit)
                    {
                        $editlink = '<a href="' . JRoute::_('/index.php?option=com_sitesactivities&view=editexperiment&id=' . $facilityID . '&experimentid=' . $nawiId) . '">[edit]</a>';
                    }
                    else
                    {
                        $editlink = '';
                    }


                    if ($expPhase == 'DESIGN') {
                        $phase_name = "Experimental Design";
                    }
                    elseif ($expPhase == 'FABRIC') {
                        $phase_name = "Specimen Fabrication";
                    }
                    elseif ($expPhase == 'INSTRUMENT') {
                        $phase_name = "Specimen Instrumentation";
                    }
                    elseif ($expPhase == 'TESTING') {
                        $phase_name = "Active Testing";
                    }
                    elseif ($expPhase == 'DEMOLITION') {
                        $phase_name = "Specimen Demolition";
                    }
                    elseif ($expPhase == 'ANALYSIS') {
                        $phase_name = "Data Review/Interpretation";
                    }
                    elseif ($expPhase == 'COMPLETE') {
                        $phase_name = "Completed";
                    }


                    $mlink = "";

                    if($movie_url)
                    {
                        $mlink = "<strong>[<a style='font-weight: normal;' href='$movie_url'>Movie</a>]</strong>";
                    }

                    if  ($nawi->getActive())
                    {
                        $flink = "";

                        if($feedcount)
                        {
                            $flink = "<strong>[<a style='font-weight: normal;' href='?facid=$facilityID&eloc=Feeds'>Video&nbsp;Stream</a>]</strong>";
                        }
                    
                        $explist_act .= <<<ENDHTML
                            <!-- begin record -->
                            <a name="$nawiId"></a>
                            <div name="$nawiId" style="border-bottom:1px dashed #ddd; margin-bottom:25px; width:80%">
                                <h4>$exp_name</h4>
                                <table style="border:none">
                                    <tr>
                                        <td style="width:50px;">
                                            <img src="/components/com_sitesactivities/images/ico_phases-$expPhase.jpg" width="41" height="37" alt="" />
                                        </td>
                                        <td>
                                            Status: $phase_name $flink $mlink<br/>
                                            Planned for $newDate $testTimeZone<br/>
                                            $editlink
                                        </td>
                                    </tr>
                                </table>
                                <p>$exp_descript</p>
                            </div>
                            <!-- end record -->
ENDHTML;
                    }

                    else
                    {
                        $explist_act .= <<<ENDHTML2

                        <!-- begin record -->
                        <a name="$nawiId"></a>
                        <div name="$nawiId" style="border-bottom:1px dashed #ddd; margin-bottom:25px; width:80%">
                            <h4>$exp_name</h4>
                                <table style="border:none">
                                <tr>
                                    <td style="width:50px;">
                                        <img src="/components/com_sitesactivities/images/ico_phases-$expPhase.jpg" width="41" height="37" alt="" />
                                    </td>
                                    <td>
                                        Status: $phase_name $mlink<br/>
                                        Planned for $newDate $testTimeZone<br/>
                                        $editlink
                                    </td>
                                </tr>
                            </table>
                            <p>$exp_descript</p>
                        </div>
                        <!-- end record -->

ENDHTML2;
                    }

                } // end foreach

            } // end if for experiement > 0 count check

        } 
        else // facility is not defined
        {
            $facilityName = '';

        }

        $this->assignRef('explist_act', $explist_act);
	$this->assignRef('facilityName', $facilityName);
        $this->assignRef('phasetable', $phasetable);

        parent::display($tpl);
    }
                        
}
