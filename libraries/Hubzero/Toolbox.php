<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Hubzero helper class for misc. functions
 */
class Hubzero_Toolbox
{
	/**
	 * Send an email
	 * 
	 * @param      string $email   Address to email
	 * @param      string $subject Message subject
	 * @param      string $message Message contents
	 * @return     integer 1 on success, 0 on failure
	 */
	public function send_email($email, $subject, $message)
	{
		$jconfig =& JFactory::getConfig();

		$contact_email = $jconfig->getValue('config.mailfrom');
		$contact_name  = $jconfig->getValue('config.sitename') . ' Administrator';

		$args = "-f '" . $contact_email . "'";
		$headers = "MIME-Version: 1.0\n";
		$headers .= "Content-type: text/plain; charset=utf-8\n";
		$headers .= "From: " . $contact_name . " <" . $contact_email . ">\n";
		$headers .= "Reply-To: " . $contact_name . " <" . $contact_email . ">\n";
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: High\n";
		$headers .= "X-Mailer: " . $jconfig->getValue('config.sitename') . "\n";

		if (mail($email, $subject, $message, $headers, $args)) 
		{
			return(1);
		}

		return(0);
	}

	/**
	 * Get the URL for this page
	 * 
	 * @param      integer $cutgetvars Strip querystring?
	 * @return     string
	 */
	public function thisurl($cutgetvars = 0)
	{
		if (!empty($_SERVER['REDIRECT_URL'])) 
		{
			$thisurl = $_SERVER['REDIRECT_URL'];
		} 
		else 
		{
			$thisurl = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/') + 1);
		}

		if ($cutgetvars) 
		{
			$pvar = strpos($thisurl, '?');

			if ($pvar) 
			{
				$thisurl = substr($thisurl, 0, $pvar);
			}
		}

		return $thisurl;
	}

	/**
	 * Replace discouraged characters introduced by Microsoft Word
	 * 
	 * @param      string  $text       Text to clean
	 * @param      boolean $quotesOnly Only clean quotes (single and double)
	 * @return     string
	 */
	public function cleanMsChar($text, $quotesOnly=false)
	{
		$y = array(
			"\x7f"=>'', 
			"\x80"=>'&#8364;', 
			"\x81"=>'', 
			"\x83"=>'&#402;', 
			"\x85"=>'&#8230;', 
			"\x86"=>'&#8224;', 
			"\x87"=>'&#8225;', 
			"\x88"=>'&#710;', 
			"\x89"=>'&#8240;', 
			"\x8a"=>'&#352;', 
			"\x8b"=>'&#8249;', 
			"\x8c"=>'&#338;', 
			"\x8d"=>'', 
			"\x8e"=>'&#381;', 
			"\x8f"=>'', 
			"\x90"=>'', 
			"\x95"=>'&#8226;', 
			"\x96"=>'&#8211;', 
			"\x97"=>'&#8212;', 
			"\x98"=>'&#732;', 
			"\x99"=>'&#8482;', 
			"\x9a"=>'&#353;', 
			"\x9b"=>'&#8250;', 
			"\x9c"=>'&#339;', 
			"\x9d"=>'', 
			"\x9e"=>'&#382;', 
			"\x9f"=>'&#376;',
		);
		$x = array(
			"\x82"=>'\'', 
			"\x84"=>'"', 
			"\x91"=>'\'', 
			"\x92"=>'\'', 
			"\x93"=>'"', 
			"\x94"=>'"'
		);
		if (!$quotesOnly)
		{
			$x = $y + $x;
		}

		$text = strtr($text, $x);

		return $text;
	}
}

