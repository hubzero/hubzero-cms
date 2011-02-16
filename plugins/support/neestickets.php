<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.$option.DS.'tables'.DS.'ticket.php' );
jimport('joomla.event.plugin');

class plgSupportneestickets extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param object $subject The object to observe
     * @param array $config An array that holds the plugin configuration
     */
    function plgSupportneestickets(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }


    function onValidateTicketSubmission($reporter, $problem)
    {
	if (isset($_POST['isSecurityIncident']) && $_POST['isSecurityIncident'] == '1') {
	    if (isset($_POST['security-site']) && isset($_POST['security-poc-phone']) && $_POST['security-site'] != "" && $_POST['security-poc-phone'] != "") {
		if ($_POST['security-incidenttype'] != "other" || isset($_POST['security-incidenttype-other']) && $_POST['security-incident-type-other'] != "") {
	    	    return true;
		}
		return false;		
	    }
	    return false;
	}
	else {
	    return true;
	}

    }
    
    function onPreTicketSubmission()
    {


    }

    function onTicketSubmission($ticket)
    {
        $siText = '';

        if (  isset($_REQUEST['isSecurityIncident']) && $_REQUEST['isSecurityIncident'] == '1' )
        {

            // Format the changes
            $siText .= "\n\nThis problem is a security incident.  The following security information has been reported:\n";
            $siText .=  "Security site: ". $_POST['security-site']. "\n";
            $siText .= "Site POC Phone: ". $_POST['security-poc-phone']. "\n";
            $siText .= "Incident type: ". $_POST['security-incidenttype'];
            if ($_POST['security-incidenttype'] == "other") {
                    $siText .= ": ". $_POST['security-incidenttype-other'];
            }

            // Apply and save changes
            $ticket->report .= $siText;
            $ticket->store();
        }
    }

}

?>
