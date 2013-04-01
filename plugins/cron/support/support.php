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
		$obj = new stdClass();
		$obj->plugin = 'support';
		$obj->events = array(
			'onClosePending' => JText::_('Close pending tickets'),
			'sendTicketsReminder' => JText::_('Email reminder for open tickets'),
		);

		return $obj;
	}

	/**
	 * Close tickets in a pending state for a specific amount of time
	 * 
	 * @return     boolean
	 */
	public function onClosePending()
	{
		return true;
	}

	/**
	 * Send emails reminding people of their open tickets
	 * 
	 * @return     boolean
	 */
	public function sendTicketsReminder()
	{
		$database = JFactory::getDBO();
		$juri =& JURI::getInstance();
		
		$jconfig =& JFactory::getConfig();
		//$jconfig->getValue('config.sitename')
		//$config = JComponentHelper::getParams('com_support');

		$sql = "SELECT * FROM #__support_tickets WHERE open=1 AND status!=2 AND owner IS NOT NULL and owner !='' ORDER BY created";
		$database->setQuery($sql);
		if (!($results = $database->loadObjectList()))
		{
			return true;
		}

		ximport('Hubzero_Plugin_View');

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_support' . DS . 'helpers' . DS . 'utilities.php');
		$severities = SupportUtilities::getSeverities();

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
				/*$tickets[$result->owner] = array(
					'critical' => array(),
					'major'    => array(),
					'normal'   => array(),
					'minor'    => array(),
					'trivial'  => array()
				);*/
			}
			if (isset($tickets[$result->owner][$result->severity]))
			{
				$tickets[$result->owner][$result->severity] = $result;
			}
			else
			{
				$tickets[$result->owner]['unknown'] = $result;
			}
		}

		foreach ($tickets as $owner => $sevs)
		{
			// Get the user's account
			$juser = JUser::getInstance($owner);
			if (!$juser->get('id'))
			{
				continue;
			}

			// Build the list of tickets
			/*$msg = '';
			foreach ($sevs as $severity => $ts)
			{
				if (count($ts) <= 0)
				{
					continue;
				}
				$msg .= '=== ' . $severity . ' ===' . "\r\n";
				foreach ($ts as $t)
				{
					$sef = JRoute::_('index.php?option=com_support&controller=tickets&task=ticket&id='. $t->id);

					$msg .= '#' . $t->id . ' (' . $t->created . ') :: ' . $juri->base() . ltrim($sef, DS) . ' :: ' . stripslashes($t->summary) . "\r\n";
				}
			}*/
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'cron',
					'element' => $this->_name,
					'name'    => 'email'
				)
			);
			$view->option   = 'com_support';
			$view->sitename = $jconfig->getValue('config.sitename');
			$view->severities = $sevs;

			$message = $view->loadTemplate();
			$message = str_replace("\n", "\r\n", $message);

			// Send email
			//$message;
		}

		return true;
	}
}

