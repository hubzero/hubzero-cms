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

namespace Hubzero\Base;

/**
 * Client detector
 */
class ClientDetector
{
	/**
	 * Request URI
	 */
	private $request = null;

	/**
	 * Create a new application instance.
	 *
	 * @return  void
	 */
	public function __construct($request)
	{
		$this->request = substr($request, strlen($_SERVER['SCRIPT_NAME']));
	}

	/**
	 * Detect the application's current environment.
	 *
	 * @param   array|string  $environments
	 * @param   array|null    $consoleArgs
	 * @return  string
	 */
	public function detect($environments, $consoleArgs = null)
	{
		if ($consoleArgs)
		{
			return $this->detectConsoleEnvironment($environments, $consoleArgs);
		}
		else
		{
			return $this->detectWebEnvironment($environments);
		}
	}

	/**
	 * Set the application environment for a web request.
	 *
	 * @param  mixed   $environments  array|string
	 * @return string
	 */
	protected function detectWebEnvironment($environments)
	{
		//$path = str_replace('index.php', '', $this->request);
		//$path = trim($path, '/');

		$default = 'site';

		foreach ($environments as $environment => $url)
		{
			if ($client = ClientManager::client($environment, true))
			{
				//$client->url = $url;
				$const = 'JPATH_' . strtoupper($environment);

				if (!defined($const)) continue;

				// To determine the current environment, we'll simply iterate through the possible
				// environments and look for the host that matches the host for this request we
				// are currently processing here, then return back these environment's names.
				//if (substr($path, 0, strlen($url)) == $url)
				if (JPATH_BASE == constant($const)) return $environment;
			}
		}

		return $default;
	}

	/**
	 * Set the application environment from command-line arguments.
	 *
	 * @param   mixed   $environments
	 * @param   array   $args
	 * @return  string
	 */
	protected function detectConsoleEnvironment($environments, array $args)
	{
		// First we will check if an environment argument was passed via console arguments
		// and if it was that automatically overrides as the environment. Otherwise, we
		// will check the environment as a "web" request like a typical HTTP request.
		if ( ! is_null($value = $this->getEnvironmentArgument($args)))
		{
			return reset(array_slice(explode('=', $value), 1));
		}
		else
		{
			return $this->detectWebEnvironment($environments);
		}
	}

	/**
	 * Get the enviornment argument from the console.
	 *
	 * @param   array  $args
	 * @return  mixed  string|null
	 */
	protected function getEnvironmentArgument(array $args)
	{
		foreach ($args as $k => $v)
		{
			if (substr($v, 0, strlen('--env')) == '--env')
			{
				return $v;
			}
		}

		return null;
	}
}