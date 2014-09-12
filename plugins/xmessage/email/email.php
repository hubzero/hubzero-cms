<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * XMessage plugin class for email
 */
class plgXMessageEmail extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return this messaging type
	 * 
	 * @return     string
	 */
	public function onMessageMethods()
	{
		return 'email';
	}

	/**
	 * Send a message to a user
	 * 
	 * @param      array   $from     Message 'from' data (e.g., name, address)
	 * @param      object  $xmessage The message to send
	 * @param      object  $user     User to send the message to
	 * @param      string  $action   Messaging method (e.g., email, smstxt, etc.)
	 * @return     boolean True if message was sent
	 */
	public function onMessage($from, $xmessage, $user, $action)
	{
		//make sure were supposed to be performing this action
		if ($this->onMessageMethods() != $action) 
		{
			return true;
		}

		//check to make sure users account is confirmed
		if ($user->get('emailConfirmed') <= 0) 
		{
			return false;
		}

		//get users email
		$email = $user->get('email');

		//if we dont have an email stop
		if (!$email) 
		{
			return false;
		}

		//get site config
		$jconfig =& JFactory::getConfig();

		//if we dont have a from set the use site from name and email
		if (!isset($from['name']) || $from['name'] == '') 
		{
			$from['name'] = $jconfig->getValue('config.sitename') . ' Administrator';
		}
		if (!isset($from['email']) || $from['email'] == '') 
		{
			$from['email'] = $jconfig->getValue('config.mailfrom');
		}

		//set mail headers
		$headers  = "MIME-Version: 1.0 \n";
		if (array_key_exists('multipart', $from))
		{
			$headers .= "Content-Type: multipart/alternative;boundary=" . chr(34) . $from['multipart'] . chr(34) . "\r\n";
		}
		else
		{
			$headers .= "Content-type: text/plain; charset=utf-8\n";
		}
		$headers .= "X-Priority: 3\n";
		$headers .= "X-MSMail-Priority: Normal\n";
		$headers .= "Importance: Normal\n";
		$headers .= "X-Mailer: PHP/" . phpversion()  . "\r\n";
		$headers .= "X-Component: " . $xmessage->component . "\r\n";
		$headers .= "X-Component-Object: " . $xmessage->type . "\r\n";
		$headers .= "From: " . $from['name'] . " <" . $from['email'] . ">\n";

		// In case a different reply to email address is specified
		if (array_key_exists('replytoemail', $from))
		{
			$replytoname = (isset($from['replytoname']) && $from['replytoname'] != '') ? $from['replytoname'] : $from['name'];
			$headers .= "Reply-To: " . $replytoname . " <" . $from['replytoemail'] . ">\n";
		}
		else
		{
			$headers .= "Reply-To: " . $from['name'] . " <" . $from['email'] . ">\n";
		}

		//set mail additional args (mail return path - used for bounces)
		$args = '-f hubmail-bounces@' . $_SERVER['HTTP_HOST'];

		//fancy email
		if (strpos($user->get('name'), ','))
		{
			$fullEmailAddress = "\"" . $user->get('name') . "\" <" . $user->get('email') . ">";
		}
		else
		{
			$fullEmailAddress = $user->get('name') . " <" . $user->get('email') . ">";
		}

		// Want to add some extra headers? We put them into the from array 
		// If none are there, this breaks nothing
        if (array_key_exists('xheaders', $from))
		{
			$hs = $from['xheaders'];

			// The xheaders array has name and value pairs
			foreach ($hs as $n => $v)
			{
				$headers .= $n . ": " . $v . "\n";
			}
		}

		if (is_array($xmessage->message))
		{
			$message = isset($xmessage->message['multipart']) ? $xmessage->message['multipart'] : current($xmessage->message);
		}
		else
		{
			$message = $xmessage->message;
		}

		//set mail
		if (mail($fullEmailAddress, $jconfig->getValue('config.sitename') . ' ' . $xmessage->subject, $message, $headers, $args))
		{
			return true;
		}
		return false;
	}
}
