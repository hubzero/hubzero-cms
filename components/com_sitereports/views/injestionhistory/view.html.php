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

class sitereportsViewinjestionhistory extends JView
{

    function display($tpl = null)
    {
        $html = '';
        $years = array(2010, 2011, 2012, 2013, 2014);
        $quarters = array(1,2,3,4);


        // Tabs for the page
        $tabs = SiteReportsHelper::getFacilityTabs(SiteReportsHelper::tabInjestionHistory);
        $this->assignRef('tabs', $tabs);

        $html .= '<table style="border: 1px solid #F6F5E8">';
        $html .= '<tr>';
        $html .= '<td width="100"></td>';
        // build the table header
        foreach($years as $year)
        {
            $html .= '<td colspan="8">' . $year ."</td>";
        }

        $html .= '</tr><tr>';
        $html .= '<td></td>';
        foreach($years as $year)
        {
            foreach($quarters as $quarter)
            {
                $html .= '<td colspan="2">' . $quarter . '</td>';
            }
        }

        $html .= '</tr><tr>';
        $html .= '<td></td>';
        foreach($years as $year)
        {
            foreach($quarters as $quarter)
            {
                $html .= '<td>QAR</td>';
                $html .= '<td>QFR</td>';
            }
        }

        // Get all the orgs and loop through them
        $repOrgs = SiteReportsHelper::getReportingOrgs();

        /* @var $reportingSite SiteReportsSite */
        foreach($repOrgs as $reportingSite)
        {
            $html .= '<tr>';
            $html .= '<td nowrap="nowrap">' . $reportingSite->getOrganization()->getName() . '</td>';

            foreach($years as $year)
            {
                foreach($quarters as $quarter)
                {
                    $c = $this->lookupCount('SITEREPORTS_QAR', $reportingSite->getOrganization()->getId(), $year, $quarter);
                    $html .= ($c==1) ? '<td> <font color="green"> X </font> </td>' : '<td>  <font color="red">O</font> </td>';

                    $c = $this->lookupCount('SITEREPORTS_QFR', $reportingSite->getOrganization()->getId(), $year, $quarter);
                    $html .= ($c==1) ? '<td>  <font color="green"> X </font> </td>' : '<td> <font color="red"> O </font> </td>';
                }
            }


            $html .= '<tr>';
        }




        $html .= '</tr>';
        $html .= '</table>';
        $this->assignRef('html', $html);

        parent::display($tpl);
    }



    function lookupCount($table, $siteID, $year, $quarter)
    {
        $count = 0;

        $sql = 'SELECT COUNT(FACILITY_ID) "X" FROM '. $table . ' WHERE FACILITY_ID = ? AND YEAR = ? AND QUARTER = ?';

        $conn = Propel::getConnection();
        $stmt = $conn->prepareStatement($sql);
        $stmt->setString(1, $siteID);
        $stmt->setString(2, $year);
        $stmt->setString(3, $quarter);

        $oResultSet = $stmt->executeQuery(ResultSet::FETCHMODE_ASSOC);

        while($oResultSet->next())
        {
            $count = $oResultSet->get('X');
        }

        return $count;

    }



}
