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
 * Cron plugin for support tickets
 */
class plgCronSupport extends JPlugin
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
				'label'  => JText::_('PLG_CRON_SUPPORT_CLOSE_PENDING'),
				'params' => 'ticketpending'
			),
			array(
				'name'   => 'sendTicketsReminder',
				'label'  =>  JText::_('PLG_CRON_SUPPORT_EMAIL_REMINDER'),
				'params' => 'ticketreminder'
			),
			array(
				'name'   => 'sendTicketList',
				'label'  =>  JText::_('PLG_CRON_SUPPORT_EMAIL_LIST'),
				'params' => 'ticketlist'
			),
			array(
				'name'   => 'cleanTempUploads',
				'label'  =>  JText::_('PLG_CRON_SUPPORT_CLEAN_UPLOADS'),
				'params' => 'tickettemp'
			)
		);

		return $obj;
	}

	/**
	 * Close tickets in a pending state for a specific amount of time
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function cleanTempUploads(CronModelJob $job)
	{
		$params = $job->get('params');

		$sconfig = JComponentHelper::getParams('com_support');
		$path = JPATH_ROOT . DS . trim($sconfig->get('webpath', '/site/tickets'), DS);

		$days = intval($params->get('support_tickettemp_age', '7'));

		$old = time() - ($days * 24 * 60 * 60);

		jimport('joomla.filesystem.file');

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
				JFolder::delete($file->getPathname());
			}
		}
	}

	/**
	 * Close tickets in a pending state for a specific amount of time
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function onClosePending(CronModelJob $job)
	{
		$params = $job->get('params');

		$database = JFactory::getDBO();

		$sql = "UPDATE `#__support_tickets` AS t SET t.`open`=0, t.`status`=0, t.`closed`=" . $database->quote(JFactory::getDate()->toSql());

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
					$user = JUser::getInstance($username);
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

			if ($tags = $params->get('support_ticketpending_excludeTags', 'fixedinstable, fixedinmaster, pendingdevpush, pendingcorepush, pendingupdate'))
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

			if ($tags = $params->get('support_ticketpending_includeTags', 'pendingreview'))
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
						$timestamp = JFactory::getDate('-1 day');
					break;

					case '-week':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 week');
					break;

					case '-2week':
						$op = '<=';
						$timestamp = JFactory::getDate('-2 week');
					break;

					case '-3week':
						$op = '<=';
						$timestamp = JFactory::getDate('-3 week');
					break;

					case '-month':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 month');
					break;

					case '-6month':
						$op = '<=';
						$timestamp = JFactory::getDate('-6 month');
					break;

					case '-year':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 year');
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
			$timestamp = JFactory::getDate('-2 week');
			$where[] = "t.`created` <= " . $database->quote($timestamp->toSql());
		}

		if (count($where)  > 0)
		{
			$sql .= " WHERE " . implode(" AND ", $where);
		}
		//echo $sql;
		$database->setQuery($sql);
		if (!$database->query())
		{
			$logger = \JFactory::getLogger();
			$logger->logError('CRON query failed: ' . $database->getErrorMsg());
		}

		return true;
	}

	/**
	 * Send emails reminding people of their open tickets
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function sendTicketsReminder(CronModelJob $job)
	{
		$params = $job->get('params');

		$database = JFactory::getDBO();
		$juri = JURI::getInstance();

		$jconfig = JFactory::getConfig();
		$sconfig = JComponentHelper::getParams('com_support');

		$lang = JFactory::getLanguage();
		$lang->load('com_support');
		$lang->load('com_support', JPATH_BASE);

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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');

		if (is_object($params) && $params->get('support_ticketreminder_severity', 'all') != 'all')
		{
			$severities = explode(',', $params->get('support_ticketreminder_severity', 'all'));
		}
		else
		{
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');
			$severities = SupportUtilities::getSeverities($sconfig->get('severities'));
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
			'name'      => $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SUPPORT'),
			'email'     => $jconfig->getValue('config.mailfrom'),
			'multipart' => md5(date('U'))
		);

		//set mail additional args (mail return path - used for bounces)
		if ($host = JRequest::getVar('HTTP_HOST', '', 'server'))
		{
			$args = '-f hubmail-bounces@' . $host;
		}

		$subject = JText::_('COM_SUPPORT') . ': ' . JText::_('COM_SUPPORT_OPEN_TICKETS');

		$mailed = array();

		foreach ($tickets as $owner => $usertickets)
		{
			// Get the user's account
			$juser = JUser::getInstance($owner);
			if (!$juser->get('id'))
			{
				continue;
			}
			// Try to ensure no duplicates
			if (in_array($juser->get('username'), $mailed))
			{
				continue;
			}

			// Plain text
			$eview = new \Hubzero\Component\View(array(
				'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_support',
				'name'      => 'emails',
				'layout'    => 'tickets_plain'
			));
			$eview->option     = 'com_support';
			$eview->controller = 'tickets';
			$eview->tickets    = $tickets;
			$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
			$eview->tickets    = $usertickets;

			$plain = $eview->loadTemplate();
			$plain = str_replace("\n", "\r\n", $plain);

			// HTML
			$eview->setLayout('tickets_html');

			$html = $eview->loadTemplate();
			$html = str_replace("\n", "\r\n", $html);

			// Build message
			$message = new \Hubzero\Mail\Message();
			$message->setSubject($subject)
			        ->addFrom($from['email'], $from['name'])
			        ->addTo($juser->get('email'), $juser->get('name'))
			        ->addHeader('X-Component', 'com_support')
			        ->addHeader('X-Component-Object', 'support_ticket_reminder');

			$message->addPart($plain, 'text/plain');

			$message->addPart($html, 'text/html');

			// Send mail
			if (!$message->send())
			{
				$this->setError(JText::sprintf('Failed to mail %s', $fullEmailAddress));
			}
			$mailed[] = $juser->get('username');
		}

		return true;
	}

	/**
	 * Send emails reminding people of their open tickets
	 *
	 * @param   object   $job  CronModelJob
	 * @return  boolean
	 */
	public function sendTicketList(CronModelJob $job)
	{
		$params = $job->get('params');

		$database = JFactory::getDBO();
		$juri = JURI::getInstance();

		$jconfig = JFactory::getConfig();
		$sconfig = JComponentHelper::getParams('com_support');

		$lang = JFactory::getLanguage();
		$lang->load('com_support');
		$lang->load('com_support', JPATH_BASE);

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
					$user = JUser::getInstance($username);
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
						$timestamp = JFactory::getDate('-1 day');
					break;

					case '-week':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 week');
					break;

					case '-2week':
						$op = '<=';
						$timestamp = JFactory::getDate('-2 week');
					break;

					case '-3week':
						$op = '<=';
						$timestamp = JFactory::getDate('-3 week');
					break;

					case '-month':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 month');
					break;

					case '-6month':
						$op = '<=';
						$timestamp = JFactory::getDate('-6 month');
					break;

					case '-year':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 year');
					break;

					// Created since (newer than)
					case '+day':
						$op = '>=';
						$timestamp = JFactory::getDate('-1 day');
					break;

					case '+week':
						$op = '>=';
						$timestamp = JFactory::getDate('-1 week');
					break;

					case '+2week':
						$op = '>=';
						$timestamp = JFactory::getDate('-2 week');
					break;

					case '+3week':
						$op = '>=';
						$timestamp = JFactory::getDate('-3 week');
					break;

					case '+month':
						$op = '>=';
						$timestamp = JFactory::getDate('-1 month');
					break;

					case '+6month':
						$op = '>=';
						$timestamp = JFactory::getDate('-6 month');
					break;

					case '+year':
						$op = '>=';
						$timestamp = JFactory::getDate('-1 year');
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
						$timestamp = JFactory::getDate('-1 day');
					break;

					case '-week':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 week');
					break;

					case '-2week':
						$op = '<=';
						$timestamp = JFactory::getDate('-2 week');
					break;

					case '-3week':
						$op = '<=';
						$timestamp = JFactory::getDate('-3 week');
					break;

					case '-month':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 month');
					break;

					case '-6month':
						$op = '<=';
						$timestamp = JFactory::getDate('-6 month');
					break;

					case '-year':
						$op = '<=';
						$timestamp = JFactory::getDate('-1 year');
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

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'models' . DS . 'ticket.php');

		if ($params->get('support_ticketlist_severity', 'all') != 'all')
		{
			$severities = explode(',', $params->get('support_ticketlist_severity', 'all'));
		}
		else
		{
			include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');
			$severities = SupportUtilities::getSeverities($sconfig->get('severities'));
		}

		$from = array();
		$from['name']      = $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SUPPORT');
		$from['email']     = $jconfig->getValue('config.mailfrom');
		$from['multipart'] = md5(date('U'));

		// Set mail additional args (mail return path - used for bounces)
		if ($host = JRequest::getVar('HTTP_HOST', '', 'server'))
		{
			$args = '-f hubmail-bounces@' . $host;
		}

		$subject = JText::_('COM_SUPPORT') . ': ' . JText::_('COM_SUPPORT_TICKETS');

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
				$name  = $jconfig->getValue('config.mailfrom');
				$email = $jconfig->getValue('config.mailfrom');
			}
			else if (strstr($owner, '@'))
			{
				$name  = $owner;
				$email = $owner;
			}
			else
			{
				// Get the user's account
				$juser = JUser::getInstance($owner);
				if (!is_object($juser) || !$juser->get('id'))
				{
					continue;
				}

				$name  = $juser->get('name');
				$email = $juser->get('email');
			}

			// Try to ensure no duplicates
			if (in_array($email, $mailed))
			{
				continue;
			}

			$eview = new \Hubzero\Component\View(array(
				'base_path' => JPATH_ROOT . DS . 'components' . DS . 'com_support',
				'name'      => 'emails',
				'layout'    => 'ticketlist_plain'
			));
			$eview->option     = 'com_support';
			$eview->controller = 'tickets';
			$eview->delimiter  = '~!~!~!~!~!~!~!~!~!~!';
			$eview->boundary   = $from['multipart'];
			$eview->tickets    = $results;

			$plain = $eview->loadTemplate();
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
			$logger = \JFactory::getLogger();
			if (!$message->send())
			{
				//$this->setError(JText::sprintf('Failed to mail %s', $fullEmailAddress));
				$logger->error('CRON email failed: ' . JText::sprintf('Failed to mail %s', $email));
			}
			$mailed[] = $email;
		}

		return true;
	}
}

