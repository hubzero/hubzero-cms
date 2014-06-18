<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 *
 */

/**
 * API controller class for forum posts
 */
class SystemControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a request
	 *
	 * @return    void
	 */
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		$this->config = JComponentHelper::getParams('com_system');
		$this->database = JFactory::getDBO();

		switch ($this->segments[0])
		{
			case 'info':               $this->infoTask();               break;
			case 'overview':           $this->infoTask();               break;
			case 'getSessionLifetime': $this->getSessionLifetimeTask(); break;
			default:
				$this->serviceTask();
			break;
		}
	}

	/**
	 * Displays a available options and parameters the API
	 * for this comonent offers.
	 *
	 * @return  void
	 */
	private function serviceTask()
	{
		$response = new stdClass();
		$response->component = 'system';
		$response->tasks = array(
			'info' => array(
				'description' => JText::_('Get an overview of a hub\'s status.'),
				'parameters'  => array(
					'values'      => JText::_('The verbosity of information returned.'),
					'type'        => 'string',
					'default'     => 'all',
					'accepts'     => array('all', 'short', 'comma-separated list of keys [cms, php, dbversion, dbcollation, phpversion, server, last_commit]')
				),
			),
		);

		$this->setMessageType(JRequest::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	private function infoTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$values = JRequest::getVar('values', 'all');

		$response = new stdClass;

		$ip = JRequest::ip();
		$ips = explode(',', $this->config->get('whitelist', '127.0.0.1'));
		$ips = array_map('trim', $ips);
		if (!in_array($ip, $ips))
		{
			$this->setMessage($response);
			return;
		}

		if (isset($_SERVER['SERVER_SOFTWARE']))
		{
			$sf = $_SERVER['SERVER_SOFTWARE'];
		}
		else
		{
			$sf = getenv('SERVER_SOFTWARE');
		}

		$commit = shell_exec("git log -1 --pretty=format:'%H - %s (%ci)' --abbrev-commit");
		//shell_exec("git log -1 --pretty=format:'%h - %s (%ci)' --abbrev-commit git merge-base local-dev dev");

		// System
		$response->system = array(
			'cms'         => \Hubzero\Version\Version::VERSION,
			'php'         => php_uname(),
			'dbversion'   => $this->database->getVersion(),
			'dbcollation' => $this->database->getCollation(),
			'phpversion'  => phpversion(),
			'server'      => $sf,
			'last_commit' => $commit
		);

		if (strstr($values, ',') || ($values != 'all' && $values != 'short'))
		{
			$keys = explode(',', $values);
			$keys = array_map('trim', $keys);
			$keys = array_map('strtolower', $keys);
			$data = array();
			foreach ($keys as $key)
			{
				$data[$key] = $response->system[$key];
			}
			$response->system = $data;
		}

		if ($values == 'all')
		{
			JPluginHelper::importPlugin('hubzero');
			$dispatcher = JDispatcher::getInstance();
			$response->overview = $dispatcher->trigger('onSystemOverview');
		}

		$this->setMessage($response);
	}

	/**
	 * Get session lifetime, in minutes
	 *
	 * @return    void
	 */
	private function getSessionLifetimeTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$config = new \JConfig();

		$response   = array();
		$response[] = $config->lifetime;

		$this->setMessage($response);
	}
}
