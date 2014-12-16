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

namespace Plugins\Content\Akismet\Service;

use Plugins\Content\Akismet\Service\SocketWriteRead;
use Hubzero\Antispam\Adapter\AbstractAdapter;
use Exception;

require_once __DIR__ . '/SocketWriteRead.php';

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
 * @package		akismet
 * @author		Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @version		0.4
 * @copyright	Alex Potsides, {@link http://www.achingbrain.net http://www.achingbrain.net}
 * @license		http://www.opensource.org/licenses/bsd-license.php BSD License
 */
class Provider extends AbstractAdapter
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
		// Check to see if the key is valid
		$response = $this->_sendRequest('key=' . $this->apiKey . '&blog=' . $this->url, $this->akismetServer, '/' . $this->akismetVersion . '/verify-key');
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
	 *	Tests for spam.
	 *	Uses the web service provided by {@link http://www.akismet.com Akismet} to see whether or not the submitted comment is spam.  Returns a boolean value.
	 *
	 * @param    string $value Conent to test
	 * @return   bool True if the comment is spam, false if not
	 * @throws   Will throw an exception if the API key passed to the constructor is invalid.
	 */
	public function isSpam($value = null)
	{
		if ($value)
		{
			$this->setValue($value);
		}

		if (!$this->getValue())
		{
			return false;
		}

		$response = $this->_sendRequest($this->_getQueryString(), $this->apiKey . '.rest.akismet.com', '/' . $this->akismetVersion . '/comment-check');

		if ($response[1] == 'invalid' && !$this->isKeyValid())
		{
			throw new Exception('The Wordpress API key passed to the Akismet adapter is invalid. Please obtain a valid one from http://wordpress.com/api-keys/');
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
