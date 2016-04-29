<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

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
			$database = App::get('db');

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
							$xseen->whenseen = Date::toSql();
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

		$database = App::get('db');

		// Create the message object
		$xmessage = new \Hubzero\Message\Message($database);

		if ($type == 'member_message')
		{
			$time_limit  = intval($this->params->get('time_limit', 30));
			$daily_limit = intval($this->params->get('daily_limit', 100));

			// First, let's see if they've surpassed their daily limit for sending messages
			$filters = array(
				'created_by'  => User::get('id'),
				'daily_limit' => $daily_limit
			);

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
					$last_time = Date::of($last_sent->created)->toUnix();
				}
				$time_difference = (Date::toUnix() + $time_limit) - $last_time;

				if ($time_difference < $time_limit)
				{
					return false;
				}
			}
		}

		// Store the message in the database
		$xmessage->message    = (is_array($message) && isset($message['plaintext'])) ? $message['plaintext'] : $message;

		// Do we have a subject line? If not, create it from the message
		if (!$subject && $xmessage->message)
		{
			$subject = substr($xmessage->message, 0, 70);
			if (strlen($subject) >= 70)
			{
				$subject .= '...';
			}
		}
		$xmessage->subject    = $subject;

		$xmessage->created    = Date::toSql();
		$xmessage->created_by = User::get('id');
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
			$mconfig = Component::params('com_members');

			// Get all the sender's groups
			if ($mconfig->get('user_messaging', 1) == 1 && !$bypassGroupsCheck)
			{
				$xgroups = User::groups('all');
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
				$recipient->created  = Date::toSql();
				$recipient->expires  = Date::of(time() + (168 * 24 * 60 * 60))->toSql();
				$recipient->actionid = 0; //(is_object($action)) ? $action->id : 0; [zooley] Phasing out action items

				// Get the user's methods for being notified
				$notify = new \Hubzero\Message\Notify($database);
				$methods = $notify->getRecords($uid, $type);

				//$user = User::getInstance($uid);
				$user = User::getInstance($uid);
				if (!is_object($user) || !$user->get('username'))
				{
					continue;
				}

				if ($mconfig->get('user_messaging', 1) == 1 && ($type == 'member_message' || $type == 'group_message'))
				{
					$pgroups = $user->groups('all');
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
							if (!Event::trigger('onMessage', array($from, $xmessage, $user, $action)))
							{
								$this->setError(Lang::txt('PLG_XMESSAGE_HANDLER_ERROR_UNABLE_TO_MESSAGE', $uid, $action));
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
						$p = Plugin::byType('members', 'messages');
						$pp = new \Hubzero\Config\Registry((is_object($p) ? $p->params : ''));

						$d = $pp->get('default_method', 'email');

						if (!$recipient->store())
						{
							$this->setError($recipient->getError());
						}

						// Use the Default in the case the user has no methods
						if (!Event::trigger('onMessage', array($from, $xmessage, $user, $d)))
						{
							$this->setError(Lang::txt('PLG_XMESSAGE_HANDLER_ERROR_UNABLE_TO_MESSAGE', $uid, $d));
						}
					}
				}
			}
		}

		return true;
	}
}

