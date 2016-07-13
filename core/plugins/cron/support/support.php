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
 * Cron plugin for support tickets
 */
class plgCronSupport extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = 'support';

		$obj->events = array(
			array(
				'name'   => 'onClosePending',
				'label'  => Lang::txt('PLG_CRON_SUPPORT_CLOSE_PENDING'),
				'params' => 'ticketpending'
			),
			array(
				'name'   => 'sendTicketsReminder',
				'label'  =>  Lang::txt('PLG_CRON_SUPPORT_EMAIL_REMINDER'),
				'params' => 'ticketreminder'
			),
			array(
				'name'   => 'sendTicketList',
				'label'  =>  Lang::txt('PLG_CRON_SUPPORT_EMAIL_LIST'),
				'params' => 'ticketlist'
			),
			array(
				'name'   => 'cleanTempUploads',
				'label'  =>  Lang::txt('PLG_CRON_SUPPORT_CLEAN_UPLOADS'),
				'params' => 'tickettemp'
			)
		);

		return $obj;
	}

	/**
	 * Close tickets in a pending state for a specific amount of time
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function cleanTempUploads(\Components\Cron\Models\Job $job)
	{
		$params = $job->params;

		$sconfig = Component::params('com_support');
		$path = PATH_APP . DS . trim($sconfig->get('webpath', '/site/tickets'), DS);

		$days = intval($params->get('support_tickettemp_age', '7'));

		$old = time() - ($days * 24 * 60 * 60);

		$dirIterator = new DirectoryIterator($path);
		foreach ($dirIterator as $file)
		{
			if (!$file->isDir())
			{
				continue;
			}

			$name = $file->getFilename();
			if (substr($name, 0, 1) != '-')
			{
				continue;
			}

			if (abs($name) < $old)
			{
				Filesystem::delete($file->getPathname());
			}
		}
	}

	/**
	 * Close tickets in a pending state for a specific amount of time
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function onClosePending(\Components\Cron\Models\Job $job)
	{
		$params = $job->params;

		$database = App::get('db');
		$sconfig = Component::params('com_support');

		$slc = "SELECT id, login, email, name FROM `#__support_tickets` AS t";
		$upd = "UPDATE `#__support_tickets` AS t SET t.`open`=0, t.`status`=0, t.`closed`=" . $database->quote(Date::toSql());

		$where = array();

		$where[] = "t.`type`=0";
		$where[] = "t.`open`=1";

		if (is_object($params))
		{
			$statuses = array();
			if (is_numeric($params->get('support_ticketpending_status1')))
			{
				$statuses[] = $params->get('support_ticketpending_status1');
			}
			if (is_numeric($params->get('support_ticketpending_status2')))
			{
				$statuses[] = $params->get('support_ticketpending_status2');
			}
			if (is_numeric($params->get('support_ticketpending_status3')))
			{
				$statuses[] = $params->get('support_ticketpending_status3');
			}
			if (count($statuses))
			{
				$where[] = "t.`status` IN (" . implode(',', $statuses) . ")";
			}

			if ($group = $params->get('support_ticketpending_group'))
			{
				$where[] = "t.`group`=" . $database->quote($group);
			}

			if ($owners = $params->get('support_ticketpending_owners'))
			{
				$usernames = explode(',', $owners);
				$usernames = array_map('trim', $usernames);
				foreach ($usernames as $k => $username)
				{
					$user = User::getInstance($username);
					$usernames[$k] = $database->quote($user->get('id'));
				}

				$where[] = "t.`owner` IN (" . implode(", ", $usernames) . ")";
			}

			if ($severity = $params->get('support_ticketpending_severity'))
			{
				if ($severity != 'all')
				{
					$severities = explode(',', $severity);
					$severities = array_map('trim', $severities);
					foreach ($severities as $k => $severity)
					{
						$severities[$k] = $database->quote($severity);
					}
					$where[] = "t.`severity` IN (" . implode(", ", $severities) . ")";
				}
			}

			if ($owned = intval($params->get('support_ticketpending_owned', 0)))
			{
				if ($owned == 1)
				{
					$where[] = "(t.`owner` IS NULL OR `owner`='')";
				}
				else if ($owned == 2)
				{
					$where[] = "(t.`owner` IS NOT NULL AND `owner` !='')";
				}
			}

			if ($submitters = $params->get('support_ticketpending_submitters'))
			{
				$usernames = explode(',', $submitters);
				$usernames = array_map('trim', $usernames);
				foreach ($usernames as $k => $username)
				{
					$usernames[$k] = $database->quote($username);
				}

				$where[] = "t.`login` IN (" . implode(", ", $usernames) . ")";
			}

			if ($tags = $params->get('support_ticketpending_excludeTags', ''))
			{
				$tags = explode(',', $tags);
				$tags = array_map('trim', $tags);
				foreach ($tags as $k => $tag)
				{
					$tags[$k] = $database->quote($tag);
				}

				$where[] = "t.`id` NOT IN (
							SELECT jto.`objectid` FROM `#__tags_object` AS jto
							JOIN `#__tags` AS jt ON jto.`tagid`=jt.`id`
							WHERE jto.`tbl`='support'
							AND (
								jt.`tag` IN (" . implode(", ", $tags) . ") OR jt.`raw_tag` IN (" . implode(", ", $tags) . ")
							)
						)";
			}

			if ($tags = $params->get('support_ticketpending_includeTags', ''))
			{
				$tags = explode(',', $tags);
				$tags = array_map('trim', $tags);
				foreach ($tags as $k => $tag)
				{
					$tags[$k] = $database->quote($tag);
				}

				$where[] = "t.`id` IN (
							SELECT jto.`objectid` FROM `#__tags_object` AS jto
							JOIN `#__tags` AS jt ON jto.`tagid`=jt.`id`
							WHERE jto.`tbl`='support'
							AND (
								jt.`tag` IN (" . implode(", ", $tags) . ") OR jt.`raw_tag` IN (" . implode(", ", $tags) . ")
							)
						)";
			}

			if ($created = $params->get('support_ticketpending_activity', '-2week'))
			{
				$op = '';
				switch ($created)
				{
					// Created before (older than)
					case '-day':
						$op = '<=';
						$timestamp = Date::modify('-1 day');
					break;

					case '-week':
						$op = '<=';
						$timestamp = Date::modify('-1 week');
					break;

					case '-2week':
						$op = '<=';
						$timestamp = Date::modify('-2 week');
					break;

					case '-3week':
						$op = '<=';
						$timestamp = Date::modify('-3 week');
					break;

					case '-month':
						$op = '<=';
						$timestamp = Date::modify('-1 month');
					break;

					case '-6month':
						$op = '<=';
						$timestamp = Date::modify('-6 month');
					break;

					case '-year':
						$op = '<=';
						$timestamp = Date::modify('-1 year');
					break;

					case '--':
						$op = '';
					break;
				}

				if ($op)
				{
					$where[] = "(SELECT MAX(c.`created`) FROM `#__support_comments` AS c WHERE c.`ticket`=t.`id`) " . $op . $database->quote($timestamp->toSql());
				}
			}
		}
		else
		{
			$timestamp = Date::modify('-2 week');
			$where[] = "t.`created` <= " . $database->quote($timestamp->toSql());
		}

		if (count($where)  > 0)
		{
			$slc .= " WHERE " . implode(" AND ", $where);
			$upd .= " WHERE " . implode(" AND ", $where);
		}

		$message_id = $params->get('support_ticketpending_message');

		// Get a list of tickets before we update them
		$tickets = array();
		if ($message_id)
		{
			$database->setQuery($slc);
			$tickets = $database->loadObjectList();
		}

		// Update the tickets
		$database->setQuery($upd);
		if (!$database->query())
		{
			Log::error('CRON query failed: ' . $database->getErrorMsg());
		}
		// If we're sending a message...
		else if ($message_id && !empty($tickets))
		{
			Lang::load('com_support') ||
			Lang::load('com_support', PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site');

			include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'message.php');
			include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');

			$message = new \Components\Support\Tables\Message($database);
			$message->load($message_id);

			// Make sure we have a message to send
			if ($message->message)
			{
				$from = array(
					'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT'),
					'email'     => Config::get('mailfrom'),
					'multipart' => md5(date('U'))
				);

				// Set mail additional args (mail return path - used for bounces)
				if ($host = Request::getVar('HTTP_HOST', '', 'server'))
				{
					$args = '-f hubmail-bounces@' . $host;
				}

				$subject = Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKETS');

				$mailed = array();

				$message->message = stripslashes($message->message);
				$message->message = str_replace('{sitename}', Config::get('sitename'), $message->message);
				$message->message = str_replace('{siteemail}', Config::get('mailfrom'), $message->message);

				foreach ($tickets as $submitter)
				{
					$name  = null;
					$email = null;

					if ($submitter->login)
					{
						// Get the user's account
						$user = User::getInstance($submitter->login);
						if (is_object($user) && $user->get('id'))
						{
							$name  = $user->get('name');
							$email = $user->get('email');
						}
					}

					$email = $email ?: $submitter->email;
					$name  = $name  ?: $submitter->name;
					$name  = $name  ?: $email;

					if (!$email)
					{
						continue;
					}

					// Try to ensure no duplicates
					/*if (in_array($email, $mailed))
					{
						continue;
					}*/

					$old = new \Components\Support\Models\Ticket($submitter->id);
					$old->set('open', 1);

					$row = clone $old;
					$row->set('open', 0);

					$comment = new \Components\Support\Models\Comment();
					$comment->set('created', Date::toSql());
					$comment->set('created_by', 0);
					$comment->set('access', 0);
					$comment->set('comment', $message->message);
					$comment->set('comment', str_replace('#XXX', '#' . $row->get('id'), $comment->get('comment')));
					$comment->set('comment', str_replace('{ticket#}', $row->get('id'), $comment->get('comment')));

					// Compare fields to find out what has changed for this ticket and build a changelog
					$comment->changelog()->diff($old, $row);
					$comment->set('ticket', $row->get('id'));

					if (!$comment->store())
					{
						$this->setError($comment->getError());
					}

					$eview = new \Hubzero\Mail\View(array(
						'base_path' => PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site',
						'name'      => 'emails',
						'layout'    => 'comment_plain'
					));
					$eview->option     = 'com_support';
					$eview->controller = 'tickets';
					$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
					$eview->boundary   = $from['multipart'];
					$eview->comment    = $comment;
					$eview->config     = $sconfig;
					$eview->ticket     = $row;

					$plain = $eview->loadTemplate(false);
					$plain = str_replace("\n", "\r\n", $plain);

					// HTML
					$eview->setLayout('comment_html');

					$html = $eview->loadTemplate();
					$html = str_replace("\n", "\r\n", $html);

					// Build message
					$message = new \Hubzero\Mail\Message();
					$message->setSubject($subject)
					        ->addFrom($from['email'], $from['name'])
					        ->addTo($email, $name)
					        ->addHeader('X-Component', 'com_support')
					        ->addHeader('X-Component-Object', 'support_ticket_comment');

					$message->addPart($plain, 'text/plain');

					$message->addPart($html, 'text/html');

					// Send mail
					if (!$message->send())
					{
						echo 'CRON email failed: ' . Lang::txt('Failed to mail %s', $email);
						//$this->setError(Lang::txt('Failed to mail %s', $fullEmailAddress));
						Log::error('CRON email failed: ' . Lang::txt('Failed to mail %s', $email));
					}
					$mailed[] = $email;
				}
			}
		}

		return true;
	}

	/**
	 * Send emails reminding people of their open tickets
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function sendTicketsReminder(\Components\Cron\Models\Job $job)
	{
		$params = $job->params;

		$database = App::get('db');

		$sconfig = Component::params('com_support');

		Lang::load('com_support') ||
		Lang::load('com_support', PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site');

		$sql = "SELECT * FROM `#__support_tickets` WHERE `open`=1 AND `status`!=2";

		if (is_object($params) && $params->get('support_ticketreminder_group'))
		{
			$group = \Hubzero\User\Group::getInstance($params->get('support_ticketreminder_group'));

			$users = array();
			if ($group)
			{
				$users = $group->get('members');
			}

			$sql .= " AND owner IN ('" . implode("','", $users) . "') ORDER BY created";
		}
		else
		{
			$sql .= " AND owner IS NOT NULL AND owner !='0' ORDER BY created";
		}

		$database->setQuery($sql);
		if (!($results = $database->loadObjectList()))
		{
			return true;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');

		if (is_object($params) && $params->get('support_ticketreminder_severity', 'all') != 'all')
		{
			$severities = explode(',', $params->get('support_ticketreminder_severity', 'all'));
		}
		else
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');
			$severities = \Components\Support\Helpers\Utilities::getSeverities($sconfig->get('severities'));
		}

		$tickets = array();
		foreach ($results as $result)
		{
			if (!isset($tickets[$result->owner]))
			{
				$tickets[$result->owner] = array();
				foreach ($severities as $severity)
				{
					$tickets[$result->owner][$severity] = array();
				}
				$tickets[$result->owner]['unknown'] = array();
			}
			if (isset($tickets[$result->owner][$result->severity]))
			{
				$tickets[$result->owner][$result->severity][] = $result;
			}
			/*else
			{
				$tickets[$result->owner]['unknown'][] = $result;
			}*/
		}

		$from = array(
			'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT'),
			'email'     => Config::get('mailfrom'),
			'multipart' => md5(date('U'))
		);

		//set mail additional args (mail return path - used for bounces)
		if ($host = Request::getVar('HTTP_HOST', '', 'server'))
		{
			$args = '-f hubmail-bounces@' . $host;
		}

		$subject = Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_OPEN_TICKETS');

		$mailed = array();

		foreach ($tickets as $owner => $usertickets)
		{
			// Get the user's account
			$user = User::getInstance($owner);
			if (!$user->get('id'))
			{
				continue;
			}
			// Try to ensure no duplicates
			if (in_array($user->get('username'), $mailed))
			{
				continue;
			}

			// Plain text
			$eview = new \Hubzero\Mail\View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site',
				'name'      => 'emails',
				'layout'    => 'tickets_plain'
			));
			$eview->option     = 'com_support';
			$eview->controller = 'tickets';
			$eview->tickets    = $tickets;
			$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
			$eview->config     = $sconfig;
			$eview->tickets    = $usertickets;

			$plain = $eview->loadTemplate(false);
			$plain = str_replace("\n", "\r\n", $plain);

			// HTML
			$eview->setLayout('tickets_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			// Build message
			$message = new \Hubzero\Mail\Message();
			$message->setSubject($subject)
			        ->addFrom($from['email'], $from['name'])
			        ->addTo($user->get('email'), $user->get('name'))
			        ->addHeader('X-Component', 'com_support')
			        ->addHeader('X-Component-Object', 'support_ticket_reminder');

			$message->addPart($plain, 'text/plain');

			$message->addPart($html, 'text/html');

			// Send mail
			if (!$message->send())
			{
				$this->setError(Lang::txt('Failed to mail %s', $fullEmailAddress));
			}
			$mailed[] = $user->get('username');
		}

		return true;
	}

	/**
	 * Send emails reminding people of their open tickets
	 *
	 * @param   object   $job  \Components\Cron\Models\Job
	 * @return  boolean
	 */
	public function sendTicketList(\Components\Cron\Models\Job $job)
	{
		$params = $job->params;

		$database = App::get('db');

		$sconfig = Component::params('com_support');

		Lang::load('com_support') ||
		Lang::load('com_support', PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site');

		$sql = "SELECT t.*, o.`name` AS owner_name FROM `#__support_tickets` AS t LEFT JOIN `#__users` AS o ON o.`id`=t.`owner`";

		$where = array();

		$where[] = "t.`type`=0";

		if (is_object($params))
		{
			if ($val = $params->get('support_ticketlist_open', 1))
			{
				$where[] = "t.`open`=" . $val;
			}

			$statuses = array();
			if (is_numeric($params->get('support_ticketlist_status1')))
			{
				$statuses[] = $params->get('support_ticketlist_status1');
			}
			if (is_numeric($params->get('support_ticketlist_status2')))
			{
				$statuses[] = $params->get('support_ticketlist_status2');
			}
			if (is_numeric($params->get('support_ticketlist_status3')))
			{
				$statuses[] = $params->get('support_ticketlist_status3');
			}

			if (count($statuses))
			{
				$where[] = "t.`status` IN (" . implode(',', $statuses) . ")";
			}

			if ($group = $params->get('support_ticketlist_group'))
			{
				$where[] = "t.`group`=" . $database->quote($group);
			}

			if ($owners = $params->get('support_ticketlist_owners'))
			{
				$usernames = explode(',', $owners);
				$usernames = array_map('trim', $usernames);
				foreach ($usernames as $k => $username)
				{
					$user = User::getInstance($username);
					$usernames[$k] = $database->quote($user->get('id'));
				}

				$where[] = "t.`owner` IN (" . implode(", ", $usernames) . ")";
			}

			if ($severity = $params->get('support_ticketlist_severity'))
			{
				if ($severity != 'all')
				{
					$severities = explode(',', $severity);
					$severities = array_map('trim', $severities);
					foreach ($severities as $k => $severity)
					{
						$severities[$k] = $database->quote($severity);
					}
					$where[] = "t.`severity` IN (" . implode(", ", $severities) . ")";
				}
			}

			if ($owned = intval($params->get('support_ticketlist_owned', 0)))
			{
				if ($owned == 1)
				{
					$where[] = "(t.`owner` IS NULL OR t.`owner`='0')";
				}
				else if ($owned == 2)
				{
					$where[] = "(t.`owner` IS NOT NULL AND t.`owner` !='0')";
				}
			}

			if ($submitters = $params->get('support_ticketlist_submitters'))
			{
				$usernames = explode(',', $submitters);
				$usernames = array_map('trim', $usernames);
				foreach ($usernames as $k => $username)
				{
					$usernames[$k] = $database->quote($username);
				}

				$where[] = "t.`login` IN (" . implode(", ", $usernames) . ")";
			}

			if ($tags = $params->get('support_ticketlist_excludeTags'))
			{
				$tags = explode(',', $tags);
				$tags = array_map('trim', $tags);
				foreach ($tags as $k => $tag)
				{
					$tags[$k] = $database->quote($tag);
				}

				$where[] = "t.`id` NOT IN (
							SELECT jto.`objectid` FROM `#__tags_object` AS jto
							JOIN `#__tags` AS jt ON jto.`tagid`=jt.`id`
							WHERE jto.`tbl`='support'
							AND (
								jt.`tag` IN (" . implode(", ", $tags) . ") OR jt.`raw_tag` IN (" . implode(", ", $tags) . ")
							)
						)";
			}

			if ($tags = $params->get('support_ticketlist_includeTags'))
			{
				$tags = explode(',', $tags);
				$tags = array_map('trim', $tags);
				foreach ($tags as $k => $tag)
				{
					$tags[$k] = $database->quote($tag);
				}

				$where[] = "t.`id` IN (
							SELECT jto.`objectid` FROM `#__tags_object` AS jto
							JOIN `#__tags` AS jt ON jto.`tagid`=jt.`id`
							WHERE jto.`tbl`='support'
							AND (
								jt.`tag` IN (" . implode(", ", $tags) . ") OR jt.`raw_tag` IN (" . implode(", ", $tags) . ")
							)
						)";
			}

			if ($created = $params->get('support_ticketlist_created', '+week'))
			{
				$op = '';
				switch ($created)
				{
					// Created before (older than)
					case '-day':
						$op = '<=';
						$timestamp = Date::modify('-1 day');
					break;

					case '-week':
						$op = '<=';
						$timestamp = Date::modify('-1 week');
					break;

					case '-2week':
						$op = '<=';
						$timestamp = Date::modify('-2 week');
					break;

					case '-3week':
						$op = '<=';
						$timestamp = Date::modify('-3 week');
					break;

					case '-month':
						$op = '<=';
						$timestamp = Date::modify('-1 month');
					break;

					case '-6month':
						$op = '<=';
						$timestamp = Date::modify('-6 month');
					break;

					case '-year':
						$op = '<=';
						$timestamp = Date::modify('-1 year');
					break;

					// Created since (newer than)
					case '+day':
						$op = '>=';
						$timestamp = Date::modify('-1 day');
					break;

					case '+week':
						$op = '>=';
						$timestamp = Date::modify('-1 week');
					break;

					case '+2week':
						$op = '>=';
						$timestamp = Date::modify('-2 week');
					break;

					case '+3week':
						$op = '>=';
						$timestamp = Date::modify('-3 week');
					break;

					case '+month':
						$op = '>=';
						$timestamp = Date::modify('-1 month');
					break;

					case '+6month':
						$op = '>=';
						$timestamp = Date::modify('-6 month');
					break;

					case '+year':
						$op = '>=';
						$timestamp = Date::modify('-1 year');
					break;
				}

				if ($op)
				{
					$where[] = "t.`created`" . $op . $database->quote($timestamp->toSql());
				}
			}

			if ($created = $params->get('support_ticketlist_activity', '--'))
			{
				$op = '';
				switch ($created)
				{
					// Created before (older than)
					case '-day':
						$op = '<=';
						$timestamp = Date::modify('-1 day');
					break;

					case '-week':
						$op = '<=';
						$timestamp = Date::modify('-1 week');
					break;

					case '-2week':
						$op = '<=';
						$timestamp = Date::modify('-2 week');
					break;

					case '-3week':
						$op = '<=';
						$timestamp = Date::modify('-3 week');
					break;

					case '-month':
						$op = '<=';
						$timestamp = Date::modify('-1 month');
					break;

					case '-6month':
						$op = '<=';
						$timestamp = Date::modify('-6 month');
					break;

					case '-year':
						$op = '<=';
						$timestamp = Date::modify('-1 year');
					break;

					case 'all':
					case '--':
						$op = '';
					break;
				}

				if ($op)
				{
					$where[] = "(SELECT MAX(c.`created`) FROM `#__support_comments` AS c WHERE c.`ticket`=t.`id`) " . $op . $database->quote($timestamp->toSql());
				}
			}
		}
		else
		{
			$where[] = "t.`open`=1";
		}

		if (count($where)  > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}
		$sql .= " ORDER BY t.`created` ASC LIMIT 0, 500";

		$database->setQuery($sql);
		if (!($results = $database->loadObjectList()))
		{
			return true;
		}

		include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');

		if ($params->get('support_ticketlist_severity', 'all') != 'all')
		{
			$severities = explode(',', $params->get('support_ticketlist_severity', 'all'));
		}
		else
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');
			$severities = \Components\Support\Helpers\Utilities::getSeverities($sconfig->get('severities'));
		}

		$from = array();
		$from['name']      = Config::get('sitename') . ' ' . Lang::txt('COM_SUPPORT');
		$from['email']     = Config::get('mailfrom');
		$from['multipart'] = md5(date('U'));

		// Set mail additional args (mail return path - used for bounces)
		if ($host = Request::getVar('HTTP_HOST', '', 'server'))
		{
			$args = '-f hubmail-bounces@' . $host;
		}

		$subject = Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKETS');

		$usernames = array();
		if ($users = $params->get('support_ticketlist_notify'))
		{
			$usernames = explode(',', $users);
			$usernames = array_map('trim', $usernames);
		}

		$mailed = array();

		foreach ($usernames as $owner)
		{
			if ($owner == '{config.mailfrom}')
			{
				$name  = Config::get('mailfrom');
				$email = Config::get('mailfrom');
			}
			else if (strstr($owner, '@'))
			{
				$name  = $owner;
				$email = $owner;
			}
			else
			{
				// Get the user's account
				$user = User::getInstance($owner);
				if (!is_object($user) || !$user->get('id'))
				{
					continue;
				}

				$name  = $user->get('name');
				$email = $user->get('email');
			}

			// Try to ensure no duplicates
			if (in_array($email, $mailed))
			{
				continue;
			}

			$eview = new \Hubzero\Mail\View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'site',
				'name'      => 'emails',
				'layout'    => 'ticketlist_plain'
			));
			$eview->option     = 'com_support';
			$eview->controller = 'tickets';
			$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
			$eview->boundary   = $from['multipart'];
			$eview->tickets    = $results;
			$eview->config     = $sconfig;

			$plain = $eview->loadTemplate(false);
			$plain = str_replace("\n", "\r\n", $plain);

			// HTML
			$eview->setLayout('ticketlist_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			// Build message
			$message = new \Hubzero\Mail\Message();
			$message->setSubject($subject)
			        ->addFrom($from['email'], $from['name'])
			        ->addTo($email, $name)
			        ->addHeader('X-Component', 'com_support')
			        ->addHeader('X-Component-Object', 'support_ticket_list');

			$message->addPart($plain, 'text/plain');

			$message->addPart($html, 'text/html');

			// Send mail
			if (!$message->send())
			{
				//$this->setError(Lang::txt('Failed to mail %s', $fullEmailAddress));
				Log::error('CRON email failed: ' . Lang::txt('Failed to mail %s', $email));
			}
			$mailed[] = $email;
		}

		return true;
	}
}

