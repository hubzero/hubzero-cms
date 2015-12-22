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

namespace Plugins\Antispam\Mollom\Service\Mollom;

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

		$ch = curl_init();

		foreach ($headers as $name => &$value)
		{
			$value = $name . ': ' . $value;
		}

		// Compose the Mollom endpoint URL.
		$url = $server . '/' . $path;
		if (isset($query) && $method == 'GET')
		{
			$url .= '?' . $query;
		}

		curl_setopt($ch, CURLOPT_URL, $url);
		// Send OAuth + other request headers.
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		// Prevent API calls from taking too long.
		// Under normal operations, API calls may time out for Mollom users without
		// a paid subscription.
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->requestTimeout);
		if ($method == 'POST')
		{
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
		}
		else
		{
			curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, TRUE);
		// Execute the HTTP request.
		if ($raw_response = curl_exec($ch))
		{
			// Split the response headers from the response body.
			list($raw_response_headers, $response_body) = explode("\r\n\r\n", $raw_response, 2);
			// Parse HTTP response headers.
			// @see http_parse_headers()
			$raw_response_headers = str_replace("\r", '', $raw_response_headers);
			$raw_response_headers = explode("\n", $raw_response_headers);
			$message = array_shift($raw_response_headers);
			$response_headers = array();
			foreach ($raw_response_headers as $line)
			{
				list($name, $value) = explode(': ', $line, 2);
				// Mollom::handleRequest() expects response header names in lowercase.
				$response_headers[strtolower($name)] = $value;
			}
			$info = curl_getinfo($ch);
			$response = array(
				'code'    => $info['http_code'],
				'message' => $message,
				'headers' => $response_headers,
				'body'    => $response_body,
			);
		}
		else
		{
			$response = array(
				'code'    => curl_errno($ch),
				'message' => curl_error($ch),
			);
		}
		curl_close($ch);
		$response = (object) $response;

		return $response;
	}
}

