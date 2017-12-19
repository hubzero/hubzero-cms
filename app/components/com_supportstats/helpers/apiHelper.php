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
 * @author    Anthony Fuentes <fuentesa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Supportstats\Helpers;

use GuzzleHttp\Client;

/**
 * Handles sending HTTP requests
 */
class ApiHelper
{

	/**
	 * HTTP client used to make requests
	 *
	 * @var object
	 */
	protected static $httpClient = null;

	/**
	 * Sends a GET request to the provided URL
	 *
	 * @param   string   $url  URL to send request to
	 * @return  array
	 */
	public static function get($url)
	{
		return self::_sendRequest('get', $url);
	}

	/**
	 * Sends a POST request to the provided URL
	 *
	 * @param   string   $url  		URL to send request to
	 * @param   array    $params  Data to post
	 * @return  array
	 */
	public static function post($url, $params)
	{
		return self::_sendRequest('post', $url, $params);
	}

	/**
	 * Sends specified request type to the provided URL
	 *
	 * @param   string   $requestMethod  	Request method to use
	 * @param   string   $url  						URL to send request to
	 * @param   array    $params  				Data to post
	 * @return  array
	 */
	protected static function _sendRequest($requestMethod, $url, $params = array())
	{
		$httpClient = self::_getHttpClient();

		$response = $httpClient->$requestMethod($url, $params);

		return $response->json();
	}

	/**
	 * Get the HTTP client
	 *
	 * @param   array   $params  	Configuration data for the client
	 * @return  object
	 */
	protected static function _getHttpClient($params = array(
		'defaults' => array('verify' => false)
	))
	{
		if (!self::$httpClient)
		{
			self::$httpClient = new Client($params);
		}

		return self::$httpClient;
	}

}
