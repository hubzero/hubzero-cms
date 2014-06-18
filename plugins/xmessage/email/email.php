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

/**
 * XMessage plugin class for email
 */
class plgXMessageEmail extends \Hubzero\Plugin\Plugin
{
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

		//if we dont have an email stop
		if (!$user->get('email'))
		{
			return false;
		}

		// get site config
		$jconfig = JFactory::getConfig();

		// if we dont have a from set the use site from name and email
		if (!isset($from['name']) || $from['name'] == '')
		{
			$from['name'] = $jconfig->getValue('config.sitename') . ' Administrator';
		}
		if (!isset($from['email']) || $from['email'] == '')
		{
			$from['email'] = $jconfig->getValue('config.mailfrom');
		}

		$message = new \Hubzero\Mail\Message();
		$message->setSubject($jconfig->getValue('config.sitename') . ' ' . $xmessage->subject)
		        ->addFrom($from['email'], $from['name'])
		        ->addTo($user->get('email'), $user->get('name'));

		// In case a different reply to email address is specified
		if (array_key_exists('replytoemail', $from))
		{
			$replytoname = (isset($from['replytoname']) && $from['replytoname'] != '') ? $from['replytoname'] : $from['name'];

			$message->addReplyTo($from['replytoemail'], $replytoname);
		}
		else
		{
			$message->addReplyTo($from['email'], $from['name']);
		}

		//set mail additional args (mail return path - used for bounces)
		$message->addHeader('X-Component', $xmessage->component)
		        ->addHeader('X-Component-Object', $xmessage->type);

		// Want to add some extra headers? We put them into the from array
		// If none are there, this breaks nothing
		if (array_key_exists('xheaders', $from))
		{
			// The xheaders array has name and value pairs
			foreach ($from['xheaders'] as $n => $v)
			{
				$message->addHeader($n, $v);
			}
		}

		if (is_array($xmessage->message))
		{
			$message->addPart($xmessage->message['plaintext'], 'text/plain')
			        ->addPart($xmessage->message['multipart'], 'text/html');
		}
		else
		{
			$message->setBody($xmessage->message);
		}

		// send mail
		if ($message->send())
		{
			return true;
		}
		return false;
	}
}
