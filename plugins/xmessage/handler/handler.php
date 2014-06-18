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
 * XMessage plugin class for handling message routing
 */
class plgXMessageHandler extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Marks action items as completed
	 *
	 * @param      string  $type      Item type
	 * @param      array   $uids      User IDs
	 * @param      string  $component ITem component
	 * @param      unknown $element Parameter description (if any) ...
	 * @return     boolean True if no errors
	 */
	public function onTakeAction($type, $uids=array(), $component='', $element=null)
	{
		// Do we have the proper bits?
		if (!$element || !$component || !$type)
		{
			return false;
		}

		// Do we have any user IDs?
		if (count($uids) > 0)
		{
			$database = JFactory::getDBO();

			// Loop through each ID
			foreach ($uids as $uid)
			{
				// Find any actions the user needs to take for this $component and $element
				$action = new \Hubzero\Message\Action($database);
				$mids = $action->getActionItems($type, $component, $element, $uid);

				// Check if the user has any action items
				if (count($mids) > 0)
				{
					foreach ($mids as $mid)
					{
						$xseen = new \Hubzero\Message\Seen($database);
						$xseen->mid = $mid;
						$xseen->uid = $uid;
						$xseen->loadRecord();
						if ($xseen->whenseen == ''
						 || $xseen->whenseen == $database->getNullDate()
						 || $xseen->whenseen == NULL)
						{
							$xseen->whenseen = JFactory::getDate()->toSql();
							$xseen->store(true);
						}
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
	public function onSendMessage($type, $subject, $message, $from=array(), $to=array(), $component='', $element=null, $description='', $group_id=0, $bypassGroupsCheck = false)
	{
		// Do we have a message?
		if (!$message)
		{
			return false;
		}

		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

		// Create the message object
		$xmessage = new \Hubzero\Message\Message($database);

		if ($type == 'member_message')
		{
			$time_limit  = intval($this->params->get('time_limit', 30));
			$daily_limit = intval($this->params->get('daily_limit', 100));

			// First, let's see if they've surpassed their daily limit for sending messages
			$filters = array();
			$filters['created_by'] = $juser->get('id');
			$filters['daily_limit'] = $daily_limit;

			$number_sent = $xmessage->getSentMessagesCount($filters);

			if ($number_sent >= $daily_limit)
			{
				return false;
			}

			// Next, we see if they've passed the time limit for sending consecutive messages
			$filters['limit'] = 1;
			$filters['start'] = 0;
			$sent = $xmessage->getSentMessages($filters);
			if (count($sent) > 0)
			{
				$last_sent = $sent[0];

				$last_time = 0;
				if ($last_sent->created)
				{
					$last_time = JFactory::getDate($last_sent->created)->toUnix();
				}
				$time_difference = (JFactory::getDate()->toUnix() + $time_limit) - $last_time;

				if ($time_difference < $time_limit)
				{
					return false;
				}
			}
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

		// Store the message in the database
		$xmessage->subject    = $subject;
		$xmessage->message    = (is_array($message) && isset($message['plaintext'])) ? $message['plaintext'] : $message;
		$xmessage->created    = JFactory::getDate()->toSql();
		$xmessage->created_by = $juser->get('id');
		$xmessage->component  = $component;
		$xmessage->type       = $type;
		$xmessage->group_id   = $group_id;

		if (!$xmessage->store())
		{
			return $xmessage->getError();
		}

		if (is_array($message))
		{
			$xmessage->message = $message;
		}

		// Do we have any recipients?
		if (count($to) > 0)
		{
			// Load plugins
			$dispatcher = JDispatcher::getInstance();

			$mconfig = JComponentHelper::getParams('com_members');

			// Get all the sender's groups
			if ($mconfig->get('user_messaging', 1) == 1 && !$bypassGroupsCheck)
			{
				$profile = \Hubzero\User\Profile::getInstance($juser->get('id'));
				$xgroups = $profile->getGroups('all');
				$usersgroups = array();
				if (!empty($xgroups))
				{
					foreach ($xgroups as $group)
					{
						if ($group->regconfirmed)
						{
							$usersgroups[] = $group->cn;
						}
					}
				}
			}

			// Loop through each recipient
			foreach ($to as $uid)
			{
				// Create a recipient object that ties a user to a message
				$recipient = new \Hubzero\Message\Recipient($database);
				$recipient->uid      = $uid;
				$recipient->mid      = $xmessage->id;
				$recipient->created  = JFactory::getDate()->toSql();
				$recipient->expires  = JFactory::getDate(time() + (168 * 24 * 60 * 60))->toSql();
				$recipient->actionid = 0; //(is_object($action)) ? $action->id : 0; [zooley] Phasing out action items

				// Get the user's methods for being notified
				$notify = new \Hubzero\Message\Notify($database);
				$methods = $notify->getRecords($uid, $type);

				//$user = JUser::getInstance($uid);
				$user = \Hubzero\User\Profile::getInstance($uid);
				if (!is_object($user) || !$user->get('username'))
				{
					continue;
				}

				if ($mconfig->get('user_messaging', 1) == 1 && ($type == 'member_message' || $type == 'group_message'))
				{
					$pgroups = $user->getGroups('all');
					$profilesgroups = array();
					if (!empty($pgroups))
					{
						foreach ($pgroups as $group)
						{
							if ($group->regconfirmed)
							{
								$profilesgroups[] = $group->cn;
							}
						}
					}
					// Find the common groups
					if (!$bypassGroupsCheck)
					{
						$common = array_intersect($usersgroups, $profilesgroups);
						if (count($common) <= 0)
						{
							continue;
						}
					}
				}

				// Do we have any methods?
				if ($methods)
				{
					// Loop through each method
					foreach ($methods as $method)
					{
						$action = strtolower($method->method);
						if ($action == 'internal')
						{
							if (!$recipient->store())
							{
								$this->setError($recipient->getError());
							}
						}
						else
						{
							if (!$dispatcher->trigger('onMessage', array($from, $xmessage, $user, $action)))
							{
								$this->setError(JText::sprintf('PLG_XMESSAGE_HANDLER_ERROR_UNABLE_TO_MESSAGE', $uid, $action));
							}
						}
					}
				}
				else
				{
					// First check if they have ANY methods saved (meaning they've changed their default settings)
					// If They do have some methods, then they simply turned off everything for this $type
					$methods = $notify->getRecords($uid);
					if (!$methods || count($methods) <= 0)
					{
						// Load the default method
						$p = JPluginHelper::getPlugin('members', 'messages');
						$pp = new JRegistry($p->params);

						$d = $pp->get('default_method');
						$d = ($d) ? $d : 'email';

						if (!$recipient->store())
						{
							$this->setError($recipient->getError());
						}

						// Use the Default in the case the user has no methods
						if (!$dispatcher->trigger('onMessage', array($from, $xmessage, $user, $d)))
						{
							$this->setError(JText::sprintf('PLG_XMESSAGE_HANDLER_ERROR_UNABLE_TO_MESSAGE', $uid, $d));
						}
					}
				}
			}
		}

		return true;
	}
}

