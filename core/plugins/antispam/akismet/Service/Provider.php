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

namespace Plugins\Antispam\Akismet\Service;

use Hubzero\Spam\Detector\Service as AbstractService;
use Exception;

require_once __DIR__ . DS . 'SocketWriteRead.php';

/**
 * Akismet anti-comment spam service
 *
 * The class in this package allows use of the {@link http://akismet.com Akismet} anti-comment spam service in any PHP5 application.
 * This service performs a number of checks on submitted data and returns whether or not the data is likely to be spam.
 * Please note that in order to use this class, you must have a vaild {@link http://wordpress.com/api-keys/ WordPress API key}.  They are free for non/small-profit types and getting one will only take a couple of minutes.
 * For commercial use, please {@link http://akismet.com/commercial/ visit the Akismet commercial licensing page}.
 * Please be aware that this class is PHP5 only.  Attempts to run it under PHP4 will most likely fail.
 * See the Akismet class documentation page linked to below for usage information.
 *
 * @package    akismet
 * @author     Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @version    0.4
 * @copyright  Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @license    http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Provider extends AbstractService
{
	/**
	 * User IP
	 *
	 * @var string
	 */
	public $user_ip = '';

	/**
	 * Referrer
	 *
	 * @var string
	 */
	public $referrer = '';

	/**
	 * Permalink
	 *
	 * @var string
	 */
	public $permalink = '';

	/**
	 * Content author
	 *
	 * @var string
	 */
	public $comment_author = '';

	/**
	 * Content author email
	 *
	 * @var string
	 */
	public $comment_author_email = '';

	/**
	 * Content author URL
	 *
	 * @var string
	 */
	public $comment_author_url = '';

	/**
	 * Content
	 *
	 * @var string
	 */
	public $comment_content = '';

	/**
	 * Script version
	 *
	 * @var string
	 */
	public $version = '0.4';

	/**
	 * URL of the site
	 *
	 * @var string
	 */
	public $url = '';

	/**
	 * API port
	 *
	 * @var integer
	 */
	public $apiPort = 0;

	/**
	 * API key
	 *
	 * @var string
	 */
	public $apiKey  = null;

	/**
	 * Akismet server
	 *
	 * @var string
	 */
	public $akismetServer = 'rest.akismet.com';

	/**
	 * Akismet version
	 *
	 * @var string
	 */
	public $akismetVersion = '1.1';

	/**
	 * This prevents some potentially sensitive information from being sent accross the wire.
	 *
	 * @var array
	 */
	public $_ignore = array(
		'server' => array(
			'HTTP_COOKIE',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_FORWARDED_HOST',
			'HTTP_MAX_FORWARDS',
			'HTTP_X_FORWARDED_SERVER',
			'REDIRECT_STATUS',
			'SERVER_PORT',
			'PATH',
			'DOCUMENT_ROOT',
			'SERVER_ADMIN',
			'QUERY_STRING',
			'PHP_SELF'
		),
		'querystring' => array(
			'url',
			'version',
			'apiPort',
			'apiKey',
			'akismetServer',
			'akismetVersion'
		)
	);

	/**
	 * Constructor
	 *
	 * @param    mixed $properties
	 * @return   void
	 */
	public function __construct($properties = null)
	{
		// Set some default values
		$this->set('apiPort', 80);
		$this->set('akismetServer', 'rest.akismet.com');
		$this->set('akismetVersion', '1.1');

		$this->set('user_agent', $_SERVER['HTTP_USER_AGENT']);
		if (isset($_SERVER['HTTP_REFERER']))
		{
			$this->set('referrer', $_SERVER['HTTP_REFERER']);
		}

		// This is necessary if the server PHP5 is running on has been set up to run PHP4 and
		// PHP5 concurently and is actually running through a separate proxy al a these instructions:
		// http://www.schlitt.info/applications/blog/archives/83_How_to_run_PHP4_and_PHP_5_parallel.html
		// and http://wiki.coggeshall.org/37.html
		// Otherwise the user_ip appears as the IP address of the PHP4 server passing the requests to the
		// PHP5 one...
		$this->set('user_ip', ($_SERVER['REMOTE_ADDR'] != getenv('SERVER_ADDR') ? $_SERVER['REMOTE_ADDR'] : getenv('HTTP_X_FORWARDED_FOR')));

		if ($properties !== null)
		{
			$this->setProperties($properties);
		}
	}

	/**
	 * Makes a request to the Akismet service to see if the API key passed to the constructor is valid.
	 * Use this method if you suspect your API key is invalid.
	 *
	 * @return bool	True is if the key is valid, false if not.
	 */
	public function isKeyValid()
	{
		// Key cannot contain spaces
		if (strstr($this->apiKey, ' '))
		{
			return false;
		}

		// Check to see if the key is valid
		$response = $this->_sendRequest('key=' . $this->apiKey . '&blog=' . $this->url, $this->akismetServer, '/' . $this->akismetVersion . '/verify-key');

		if (!$response || !is_array($response) || !isset($response[1]))
		{
			// Service unavailable?
			return false;
		}

		return ($response[1] == 'valid');
	}

	/**
	 * makes a request to the Akismet service
	 *
	 * @param     string $request
	 * @param     string $host
	 * @param     string $path
	 * @return    string
	 */
	private function _sendRequest($request, $host, $path)
	{
		$http_request  = "POST " . $path . " HTTP/1.0\r\n";
		$http_request .= "Host: " . $host . "\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded; charset=utf-8\r\n";
		$http_request .= "Content-Length: " . strlen($request) . "\r\n";
		$http_request .= "User-Agent: Akismet PHP5 Class " . $this->version . " | Akismet/1.11\r\n";
		$http_request .= "\r\n";
		$http_request .= $request;

		$socketWriteRead = new SocketWriteRead($host, $this->apiPort, $http_request);
		$socketWriteRead->send();

		return explode("\r\n\r\n", $socketWriteRead->getResponse(), 2);
	}

	/**
	 * Formats the data for transmission
	 *
	 * @return  string
	 */
	private function _getQueryString()
	{
		$this->set('comment_content', $this->getValue());

		foreach ($_SERVER as $key => $value)
		{
			if (!in_array($key, $this->_ignore['server']))
			{
				if ($key == 'REMOTE_ADDR')
				{
					$this->set($key, $this->get('user_ip'));
				}
				else
				{
					$this->set($key, $value);
				}
			}
		}

		$query_string = '';

		foreach ($this->getProperties() as $key => $data)
		{
			if (!is_array($data) && !in_array($key, $this->_ignore['querystring']))
			{
				$query_string .= $key . '=' . urlencode(stripslashes($data)) . '&';
			}
		}

		return $query_string;
	}

	/**
	 * Tests for spam.
	 * Uses the web service provided by {@link http://www.akismet.com Akismet} to see whether or not the submitted comment is spam.  Returns a boolean value.
	 *
	 * @param    array  $data  Content to test
	 * @return   bool   True if the comment is spam, false if not
	 * @throws   Will throw an exception if the API key passed to the constructor is invalid.
	 */
	public function detect($data)
	{
		$this->setValue($data['text']);

		if (!$this->getValue())
		{
			return false;
		}

		$response = $this->_sendRequest('blog=' . \Request::base() . '&' . $this->_getQueryString(), $this->apiKey . '.rest.akismet.com', '/' . $this->akismetVersion . '/comment-check');

		if (!$response || !is_array($response) || !isset($response[1]))
		{
			// Service unavailable?
			return false;
		}

		if ($response[1] == 'invalid' && !$this->isKeyValid())
		{
			throw new Exception('The API key passed to the Akismet adapter is invalid. Please obtain a valid one from http://wordpress.com/api-keys/');
		}

		return ($response[1] == 'true');
	}

	/**
	 * Submit spam that is incorrectly tagged as ham.
	 * Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
	 *
	 * @return  void
	 */
	public function submitSpam()
	{
		$this->_sendRequest($this->_getQueryString(), $this->apiKey . '.' . $this->akismetServer, '/' . $this->akismetVersion . '/submit-spam');
	}

	/**
	 * Submit ham that is incorrectly tagged as spam.
	 * Using this function will make you a good citizen as it helps Akismet to learn from its mistakes.  This will improve the service for everybody.
	 *
	 * @return  void
	 */
	public function submitHam()
	{
		$this->_sendRequest($this->_getQueryString(), $this->apiKey . '.' . $this->akismetServer, '/' . $this->akismetVersion . '/submit-ham');
	}
}
