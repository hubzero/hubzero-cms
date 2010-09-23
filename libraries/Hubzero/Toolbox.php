<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2009 by Purdue Research Foundation, West Lafayette, IN 47906.
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

class Hubzero_Toolbox 
{
	public function send_email($email, $subject, $message) 
	{
		$jconfig =& JFactory::getConfig();

		$contact_email = $jconfig->getValue('config.mailfrom');
		$contact_name = $jconfig->getValue('config.sitename') . ' Administrator';

		$args = "-f '" . $contact_email . "'";
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=utf-8\n";
		$headers .= "From: " . $contact_name . " <" . $contact_email . ">\n";
		$headers .= "Reply-To: " . $contact_name . " <" . $contact_email . ">\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: High\n";
		$headers .= "X-Mailer: " . $jconfig->getValue('config.sitename') . "\n";
		
		if (mail($email, $subject, $message, $headers, $args)) {
			return(1);
		}
    
		return(0);
	}
	
	//-----------

	public function thisurl($cutgetvars = 0) 
	{
		if (!empty($_SERVER['REDIRECT_URL'])) {
			$thisurl = $_SERVER['REDIRECT_URL'];
		} else {
			$thisurl = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], "/") + 1);
		}
		
		if ($cutgetvars) {
			$pvar = strpos($thisurl, "?");
		
			if ($pvar) {
				$thisurl = substr($thisurl, 0, $pvar);
			}
		}
    
		return($thisurl);
	}
}
