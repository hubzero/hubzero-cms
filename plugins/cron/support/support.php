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
	 * @return     array
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
			)
		);

		return $obj;
	}

	/**
	 * Close tickets in a pending state for a specific amount of time
	 * 
	 * @return     boolean
	 */
	public function onClosePending($params=null)
	{
		return true;
	}

	/**
	 * Send emails reminding people of their open tickets
	 * 
	 * @return     boolean
	 */
	public function sendTicketsReminder($params=null)
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_support');

		$database = JFactory::getDBO();
		$juri = JURI::getInstance();

		$jconfig = JFactory::getConfig();
		//$jconfig->getValue('config.sitename')
		$sconfig = JComponentHelper::getParams('com_support');

		//JLanguage::load('com_support');
		$lang = JFactory::getLanguage();
		$lang->load('com_support', JPATH_BASE);

		$sql = "SELECT * FROM #__support_tickets WHERE open=1 AND status!=2";

		if (is_object($params) && $params->get('support_ticketreminder_group'))
		{
			$group = Hubzero_Group::getInstance($params->get('support_ticketreminder_group'));

			if ($group)
			{
				$users = $group->get('members');
				$database->setQuery("SELECT username FROM #__users WHERE id IN (" . implode(',', $users) . ");");
				if (!($usernames = $database->loadResultArray()))
				{
					$usernames = array();
				}
			}

			$sql .= " AND owner IN ('" . implode("','", $usernames) . "') ORDER BY created";
		}
		else
		{
			$sql .= " AND owner IS NOT NULL and owner !='' ORDER BY created";
		}

		$database->setQuery($sql);
		if (!($results = $database->loadObjectList()))
		{
			return true;
		}

		ximport('Hubzero_Plugin_View');

		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'tags.php');

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

		$from = array();
		$from['name']      = $jconfig->getValue('config.sitename') . ' ' . JText::_('COM_SUPPORT');
		$from['email']     = $jconfig->getValue('config.mailfrom');
		$from['multipart'] = md5(date('U'));

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
			$eview = new JView(array(
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
				$this->setError('Failed to mail %s', $juser->get('email'));
			}
			$mailed[] = $juser->get('username');
		}

		return true;
	}
}

