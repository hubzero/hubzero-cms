<?php

defined( '_JEXEC' ) or die( 'Restricted access' );
 
require_once 'api/org/nees/html/TabHtml.php';





class SiteReportsHelper
{

    const tabNone = -1;
    const tabReports = 0;
    const tabSiteSubmissions = 1;
    const tabInjestionHistory = 2;
    const tabSecurity = 3;
    
    /*
     * Tabs for the component
     *
     */
    static function getFacilityTabs($activeTabIndex)
    {

        $tabArrayLinks = array("reports",
            "sitesubmissions",
            "injestionhistory",
            "security");

        $tabArrayText  = array("Reports",
            "Site Submission Injestion",
            "Injestion History",
            "Security");

        $strHtml  = '<div id="sub-menu">';
        $strHtml .= '<ul>';
        $i = 0;

        foreach ($tabArrayText as $tabEntryText)
        {
            if ($tabEntryText != '')
            {
                $strHtml .= '<li id="sm-'.$i.'"';
                $strHtml .= ($i==$activeTabIndex) ? ' class="active"' : '';
                $strHtml .= '><a class="tab" rel="' . $tabEntryText . '" href="' . JRoute::_('/index.php?option=com_sitereports&view=' . strtolower($tabArrayLinks[$i])) . '"><span>' . $tabEntryText . '</span></a></li>';
                $i++;
            }
        }

        $strHtml .= '</ul>';
        $strHtml .= '<div class="clear"></div>';
        $strHtml .= '</div><!-- / #sub-menu -->';

        return $strHtml;
    }



    public static function canRun($SiteReportsSite)
    {
        /* @var $SiteReportsSite SiteReportsSite */
        $can_run = false;
        $user =& JFactory::getUser();

        /* Short circuit test for admins */
        if($user->usertype == "Super Administrator" || $user->usertype == "Administrator")
        {
            $can_run = true;
        }
        else
        {
            if($user->id > 1)
            {
                $username = $user->get('username');

                print_r($SiteReportsSite);
                //$id = $SiteReportsSite->getID();

                $auth = HubAuthorizer::getInstanceForUseOnHub($username, $SiteReportsSite->getId(), DomainEntityType::ENTITY_TYPE_SITEREPORTSSITE);
                $can_run = $auth->canView($SiteReportsSite);
            }
            else
            {
                $can_run = false;
            }
        }

        return $can_run;
    }



    /*
     * Can the current logged in user make permission changes and grants for at least one SiteReportsSite?
     *
     */
    public static function canGrantOnAtLeastOneSite()
    {
        /* @var $SiteReportsSite SiteReportsSite */
        $can_grant = false;
        $user =& JFactory::getUser();

        $allSiteReportsSites = SiteReportsHelper::getSiteReportsSites();

        /* @var $site SiteResportsSite */
        foreach($allSiteReportsSites as $site)
        {
            $can_grant = SiteReportsHelper::canRun($site);

            // Stop checking once you hit one
            if($can_grant) break;
        }

        return $can_grant;
    }



    /*
     * Given a facilityID, return the SiteReportsSite object with that ID
     */
    public static function getSiteReportsSite($facilityID)
    {

        $c = new Criteria();
        $c->add(SiteReportsSitePeer::FACILITY_ID, $facilityID);
        /* @var $site SiteReportsSite */
        $site = SiteReportsSitePeer::doSelectJoinOrganization($c);

        if(count($site) == 0)
            return null;
        else
            return $site[0];
    }

    /*
     *  Grab all SiteReportsSites
     */
    public static function getSiteReportsSites()
    {
        return SiteReportsSitePeer::doSelectJoinOrganization(new Criteria());
    }



    public static function columnLetter($n)
    {
        $n = intval($n);

        if($n > 16384) throw new Exception('Column too large');

        $letter = '';

        if ($n <= 0) return '';

        while($n != 0){
           $p = ($n - 1) % 26;
           $n = intval(($n - $p) / 26);
           $letter = chr(65 + $p) . $letter;
        }

        return $letter;
    }


    public static function getReportingOrgs()
    {
        $c = new Criteria();
        $c->addAscendingOrderByColumn('SHORT_NAME');
        $c->add('ORGANIZATION.SHORT_NAME', 'NEESit', Criteria::NOT_EQUAL);
        $c->add('ORGANIZATION.SHORT_NAME', 'Colorado', Criteria::NOT_EQUAL);
        $repOrgs = SiteReportsSitePeer::doSelectJoinOrganization($c);

        return $repOrgs;
    }


}