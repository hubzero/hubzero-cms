<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Update plugin for support tickets
 */
class plgUpdateSupport extends \Hubzero\Plugin\Plugin
{
	/**
	 * Close tickets in a specified state
	 *
	 * @return  boolean
	 */
	public function onAfterRepositoryUpdate()
	{
		$database = App::get('db');
		$sconfig = Component::params('com_support');

		$open   = 0;
		$status = $this->params->get('support_ticket_closed', 0);
		$status = ($status == '-1' ? 0 : $status);
		if ($status)
		{
			include_once(PATH_CORE . DS . 'components' . DS . 'com_support' . DS . 'tables' . DS . 'status.php');

			$st = new \Components\Support\Tables\Status($database);
			$st->load($status);

			$open = $st->open;
		}

		$slc = "SELECT id, login, email, name FROM `#__support_tickets` AS t";
		$upd = "UPDATE `#__support_tickets` AS t SET t.`open`=" . $database->quote($open) . ", t.`status`=" . $database->quote($status) . ", t.`closed`=" . $database->quote(Date::toSql());

		$where = array();

		$where[] = "t.`type`=0";
		$where[] = "t.`open`=1";

		// Gather a list of statuses
		$statuses = array();
		if (is_numeric($this->params->get('support_ticket_state1')))
		{
			$statuses[] = $this->params->get('support_ticket_state1');
		}
		if (is_numeric($this->params->get('support_ticket_state2')))
		{
			$statuses[] = $this->params->get('support_ticket_state2');
		}
		if (is_numeric($this->params->get('support_ticket_state3')))
		{
			$statuses[] = $this->params->get('support_ticket_state3');
		}

		if (count($statuses))
		{
			$where[] = "t.`status` IN (" . implode(',', $statuses) . ")";
		}

		// Only tickets for a specified group?
		if ($group = $this->params->get('support_ticket_group'))
		{
			if (!is_numeric($group))
			{
				$g = \Hubzero\User\Group::getInstance($group);
				if ($g)
				{
					$group = $g->get('gidNumber');
				}
			}
			$where[] = "t.`group_id`=" . $database->quote((int)$group);
		}

		// Only tickets for specified owners?
		if ($owners = $this->params->get('support_ticket_owners'))
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

		// Tickets with a specified severity?
		if ($severity = $this->params->get('support_ticket_severity'))
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

		// Only tickets by specified submitters
		if ($submitters = $this->params->get('support_ticket_submitters'))
		{
			$usernames = explode(',', $submitters);
			$usernames = array_map('trim', $usernames);
			foreach ($usernames as $k => $username)
			{
				$usernames[$k] = $database->quote($username);
			}

			$where[] = "t.`login` IN (" . implode(", ", $usernames) . ")";
		}

		// Tickets WITHOUT specified tags
		if ($tags = $this->params->get('support_ticket_excludeTags', ''))
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

		// Tickets WITH specified tags
		if ($tags = $this->params->get('support_ticket_includeTags', ''))
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

		// Last activity within specified time range
		if ($created = $this->params->get('support_ticket_activity'))
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

		if (count($where)  > 0)
		{
			$slc .= " WHERE " . implode(" AND ", $where);
			$upd .= " WHERE " . implode(" AND ", $where);
		}

		$message_id = $this->params->get('support_ticket_message');

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
			Log::error('Ticket query failed: ' . $database->getErrorMsg());
			return false;
		}

		// If we're sending a message...
		if ($message_id && !empty($tickets))
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

				$message->message = str_replace('{sitename}', Config::get('sitename'), $message->message);
				$message->message = str_replace('{siteemail}', Config::get('mailfrom'), $message->message);

				$comment = new \Components\Support\Models\Comment();
				$comment->set('created', Date::toSql());
				$comment->set('created_by', 0);
				$comment->set('access', 0);
				$comment->set('comment', $message->message);

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
					if (in_array($email, $mailed))
					{
						continue;
					}

					$old = new \Components\Support\Models\Ticket($submitter->id);
					$old->set('open', 1);

					$row = clone $old;
					$row->set('open', 0);

					$comment->set('comment', str_replace('#XXX', '#' . $row->get('id'), $comment->get('comment')));
					$comment->set('comment', str_replace('{ticket#}', $row->get('id'), $comment->get('comment')));

					// Compare fields to find out what has changed for this ticket and build a changelog
					$comment->changelog()->diff($old, $row);
					$comment->set('ticket', $row->get('id'));

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
						Log::error('Ticket email failed: ' . Lang::txt('Failed to mail %s', $email));
					}

					$mailed[] = $email;
				}
			}
		}

		return true;
	}
}
