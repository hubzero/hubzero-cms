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

namespace Hubzero\Message;

use Hubzero\Base\Object;

/**
 * Hubzero message class for handling message routing
 */
class Helper extends Object
{
	/**
	 * Marks action items as completed
	 *
	 * @param      string  $type      Item type
	 * @param      array   $uids      User IDs
	 * @param      string  $component ITem component
	 * @param      unknown $element Parameter description (if any) ...
	 * @return     boolean True if no errors
	 */
	public function takeAction($type, $uids=array(), $component='', $element=null)
	{
		// Do we have the proper bits?
		if (!$element || !$component || !$type)
		{
			return false;
		}

		// Do we have any user IDs?
		if (count($uids) > 0)
		{
			$database = \JFactory::getDBO();

			// Loop through each ID
			foreach ($uids as $uid)
			{
				// Find any actions the user needs to take for this $component and $element
				$action = new Action($database);
				$mids = $action->getActionItems($component, $element, $uid, $type);

				// Check if the user has any action items
				if (count($mids) > 0)
				{
					$recipient = new Recipient($database);
					if (!$recipient->setState(1, $mids))
					{
						$this->setError(\JText::sprintf('Unable to update recipient records %s for user %s', implode(',', $mids), $uid));
					}
				}
			}
		}

		return true;
	}

	/**
	 * Send a message to one or more users
	 *
	 * @param      string  $type        Message type (maps to #__xmessage_component table)
	 * @param      string  $subject     Message subject
	 * @param      string  $message     Message to send
	 * @param      array   $from        Message 'from' data (e.g., name, address)
	 * @param      array   $to          List of user IDs
	 * @param      string  $component   Component name
	 * @param      integer $element     ID of object that needs an action item
	 * @param      string  $description Action item description
	 * @param      integer $group_id    Parameter description (if any) ...
	 * @return     mixed   True if no errors else error message
	 */
	public function sendMessage($type, $subject, $message, $from=array(), $to=array(), $component='', $element=null, $description='', $group_id=0)
	{
		// Do we have a message?
		if (!$message)
		{
			return false;
		}

		// Do we have a subject line? If not, create it from the message
		if (!$subject && $message)
		{
			$subject = substr($message, 0, 70);
			if (strlen($subject) >= 70)
			{
				$subject .= '...';
			}
		}

		$database = \JFactory::getDBO();
		$juser = \JFactory::getUser();

		// Create the message object and store it in the database
		$xmessage = new Message($database);
		$xmessage->subject    = $subject;
		$xmessage->message    = $message;
		$xmessage->created    = \JFactory::getDate()->toSql();
		$xmessage->created_by = $juser->get('id');
		$xmessage->component  = $component;
		$xmessage->type       = $type;
		$xmessage->group_id   = $group_id;
		if (!$xmessage->store())
		{
			return $xmessage->getError();
		}

		// Does this message require an action?
		// **DEPRECATED**
		$action = new Action($database);
		/*if ($element || $description)
		{
			$action->class       = $component;
			$action->element     = $element;
			$action->description = $description;
			if (!$action->store())
			{
				return $action->getError();
			}
		}*/

		// Do we have any recipients?
		if (count($to) > 0)
		{
			// Loop through each recipient
			foreach ($to as $uid)
			{
				// Create a recipient object that ties a user to a message
				$recipient = new Recipient($database);
				$recipient->uid      = $uid;
				$recipient->mid      = $xmessage->id;
				$recipient->created  = \JFactory::getDate()->toSql();
				$recipient->expires  = \JFactory::getDate(time() + (168 * 24 * 60 * 60))->toSql();
				$recipient->actionid = $action->id;
				if (!$recipient->store())
				{
					return $recipient->getError();
				}

				// Get the user's methods for being notified
				$notify = new Notify($database);
				$methods = $notify->getRecords($uid, $type);

				$user = \JUser::getInstance($uid);

				// Load plugins
				\JPluginHelper::importPlugin('xmessage');
				$dispatcher = \JDispatcher::getInstance();

				// Do we have any methods?
				if ($methods)
				{
					// Loop through each method
					foreach ($methods as $method)
					{
						$action = strtolower($method->method);

						if (!$dispatcher->trigger('onMessage', array($from, $xmessage, $user, $action)))
						{
							$this->setError(\JText::sprintf('Unable to message user %s with method %s', $uid, $action));
						}
					}
				}
			}
		}

		return true;
	}
}

