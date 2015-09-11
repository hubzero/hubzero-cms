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
 *
 */

namespace Components\System\Api\Controllers;

use Hubzero\Component\ApiController;
use Components\Update\Helpers\Cli;
use stdClass;
use Component;
use Request;
use Config;
use Event;
use Lang;
use Date;

/**
 * API controller class for system tasks
 */
class Systemv1_0 extends ApiController
{
	/**
	 * Display system information
	 *
	 * @apiMethod GET
	 * @apiUri    /system/info
	 * @apiParameter {
	 * 		"name":          "values",
	 * 		"description":   "Amount of data to return",
	 * 		"type":          "string",
	 * 		"required":      false,
	 * 		"default":       "all",
	 * 		"allowedValues": "all, short"
	 * }
	 * @return    void
	 */
	public function infoTask()
	{
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