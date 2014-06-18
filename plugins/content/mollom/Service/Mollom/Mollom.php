<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Plugins\Content\Mollom\Service\Mollom;

require_once __DIR__ . '/AbstractMollom.php';

/**
 * The base class for Mollom client implementations.
 */
class Mollom extends AbstractMollom
{
	public $configuration = array(
		'publicKey'  => null,
		'privateKey' => null
	);

	/**
	 * Constructor
	 *
	 * @param  array $keys Public and private API keys to set
	 * @return void
	 */
	public function __construct($keys = null)
	{
		if (is_array($keys) && !empty($keys))
		{
			foreach ($keys as $key => $value)
			{
				$this->saveConfiguration($key, $value);
			}
		}
		$this->publicKey  = $this->loadConfiguration('publicKey');
		$this->privateKey = $this->loadConfiguration('privateKey');
	}

	/**
	 * Loads a configuration value from client-side storage.
	 *
	 * @param string $name
	 *   The configuration setting name to load, one of:
	 *   - publicKey: The public API key for Mollom authentication.
	 *   - privateKey: The private API key for Mollom authentication.
	 *
	 * @return mixed The stored configuration value or NULL if there is none.
	 *
	 * @see Mollom::saveConfiguration()
	 * @see Mollom::deleteConfiguration()
	 */
	protected function loadConfiguration($name)
	{
		return $this->configuration[$name];
	}

	/**
	 * Saves a configuration value to client-side storage.
	 *
	 * @param string $name  The configuration setting name to save.
	 * @param mixed  $value The value to save.
	 *
	 * @see Mollom::loadConfiguration()
	 * @see Mollom::deleteConfiguration()
	 */
	protected function saveConfiguration($name, $value)
	{
		$this->configuration[$name] = $value;
		return true;
	}

	/**
	 * Deletes a configuration value from client-side storage.
	 *
	 * @param string $name The configuration setting name to delete.
	 *
	 * @see Mollom::loadConfiguration()
	 * @see Mollom::saveConfiguration()
	 */
	protected function deleteConfiguration($name)
	{
		$this->configuration[$name] = null;
		return true;
	}

	/**
	 * Returns platform and version information about the Mollom client.
	 *
	 * Retrieves platform and Mollom client version information to send along to
	 * Mollom when verifying keys.
	 *
	 * This information is used to speed up support requests and technical
	 * inquiries. The data may also be aggregated to help the Mollom staff to make
	 * decisions on new features or the necessity of back-porting improved
	 * functionality to older versions.
	 *
	 * @return array
	 *   An associative array containing:
	 *   - platformName: The name of the platform/distribution; e.g., "Drupal".
	 *   - platformVersion: The version of platform/distribution; e.g., "7.0".
	 *   - clientName: The official Mollom client name; e.g., "Mollom".
	 *   - clientVersion: The version of the Mollom client; e.g., "7.x-1.0".
	 */
	public function getClientInformation()
	{
		$data = array(
			'platformName'    => 'HUBzero',
			'platformVersion' => \Hubzero\Version\Version::VERSION,
			'clientName'      => 'Mollom',
			'clientVersion'   => '2.1',
		);
		return $data;
	}

	/**
	 * Performs a HTTP request to a Mollom server.
	 *
	 * @param string $method
	 *   The HTTP method to use; i.e., 'GET', 'POST', or 'PUT'.
	 * @param string $server
	 *   The base URL of the server to perform the request against; e.g.,
	 *   'http://foo.mollom.com'.
	 * @param string $path
	 *   The REST path/resource to request; e.g., 'site/1a2b3c'.
	 * @param string $query
	 *   (optional) A prepared string of HTTP query parameters to append to $path
	 *   for $method GET, or to use as request body for $method POST.
	 * @param array $headers
	 *   (optional) An associative array of HTTP request headers to send along
	 *   with the request.
	 *
	 * @return object
	 *   An object containing response properties:
	 *   - code: The HTTP status code as integer returned by the Mollom server.
	 *   - message: The HTTP status message string returned by the Mollom server,
	 *     or NULL if there is no message.
	 *   - headers: An associative array containing the HTTP response headers
	 *     returned by the Mollom server. Header name keys are expected to be
	 *     lower-case; i.e., "content-type" instead of "Content-Type".
	 *   - body: The HTTP response body string returned by the Mollom server, or
	 *     NULL if there is none.
	 *
	 * @see Mollom::handleRequest()
	 */
	protected function request($method, $server, $path, $query = NULL, array $headers = array())
	{
		$method = strtoupper($method);

		$url = $server . '/' . $path;
		$data = array(
			'headers' => $headers,
			'timeout' => $this->requestTimeout
		);

		if ($method == 'GET')
		{
			$function = 'remote_get';
			$url .= '?' . $query;
		}
		else
		{
			$function = 'remote_post';
			$data['body'] = $query;
		}

		$result = $function($url, $data);

		$response = new \stdClass;

		if (!$result)
		{
			$response->code    = $result['response']['code'];
			$response->message = $result['response']['message'];
			$response->headers = array();
			$response->body    = null;
		}
		else
		{
			$response->code    = $result['response']['code'];
			$response->message = $result['response']['message'];
			$response->headers = $result['headers'];
			$response->body    = $result['body'];
		}

		return $response;
	}
}

