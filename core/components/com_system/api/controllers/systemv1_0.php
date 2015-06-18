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

namespace Components\System\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Update\Helpers\Cli;
use stdClass;
use Date;
use Request;
use Lang;
use Component;
use Config;
use Event;

/**
 * API controller class for system tasks
 */
class Systemv1_0 extends ApiController
{
	/**
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	public function infoTask()
	{
		$this->setMessageType(Request::getWord('format', 'json'));

		$values = Request::getVar('values', 'all');

		$response = new stdClass;

		/*$ip = Request::ip();
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
		}*/

		//$commit = shell_exec("git log -1 --pretty=format:'%H - %s (%ad)' --abbrev-commit");
		//shell_exec("git log -1 --pretty=format:'%h - %s (%ci)' --abbrev-commit git merge-base local-dev dev");

		// System
		$response->system = array(
			'cms'         => App::version(),
			'php'         => php_uname(),
			'dbversion'   => App::get('db')->getVersion(),
			'dbcollation' => App::get('db')->getCollation(),
			'phpversion'  => phpversion(),
			'server'      => $sf,
			'last_update' => null, //$commit,
			'last_core_update' => null,
			'environment' => Config::get('application_env', 'production')
		);

		require_once PATH_CORE . DS . 'components' . DS . 'com_update' . DS . 'helpers' . DS . 'cli.php';

		$source = Component::params('com_update')->get('git_repository_source', null);

		//$response->system['repositoryVersion']   = json_decode(Cli::version());
		//$response->system['repositoryVersion']   = $response->system['repositoryVersion'][0];
		//$response->system['repositoryMechanism'] = json_decode(Cli::mechanism());
		//$response->system['repositoryMechanism'] = $response->system['repositoryMechanism'][0];
		//$response->system['status']    = json_decode(Cli::status());
		$response->system['status']    = (count(json_decode(Cli::status())) > 0 ? 'dirty' : 'clean');
		$response->system['upcoming']  = json_decode(Cli::update(true, false, $source));
		$response->system['migration'] = json_decode(Cli::migration());

		// Get the last update
		$rows = json_decode(
			Cli::log(
				1,
				0,
				'',
				false,
				true,
				false,
				null
			)
		);
		if ($rows)
		{
			$props = get_object_vars($rows);
			foreach ($props as $key => $item)
			{
				$response->system['last_update'] = $item;
			}
		}

		// Get last core update
		$rows = json_decode(
			Cli::log(
				1,
				0,
				'Merge remote-tracking',
				false,
				true,
				false,
				null
			)
		);
		if ($rows)
		{
			$props = get_object_vars($rows);
			foreach ($props as $key => $item)
			{
				$response->system['last_update_core'] = $item;
			}
		}

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

		$results = Event::trigger('hubzero.onSystemOverview', array($values));
		if ($results)
		{
			$response->overview = array();

			foreach ($results as $result)
			{
				if ($result)
				{
					$response->overview[] = $result;
				}
			}
		}

		$this->send($response);
	}

	/**
	 * Grabs the session lifetime, in minutes
	 *
	 * @apiMethod GET
	 * @apiUri    /system/getSessionLifetime
	 * @return    void
	 */
	public function getSessionLifetimeTask()
	{
		$this->send([Config::get('lifetime')]);
	}
}