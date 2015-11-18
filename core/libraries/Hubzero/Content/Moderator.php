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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content;

use App;
use Config;

/**
 * Content negotiator for files application
 */
class Moderator
{
	/**
	 * The path to the content being served
	 *
	 * @var  string
	 **/
	private $path = null;

	/**
	 * The token identifier
	 *
	 * @var  string
	 **/
	private $token = null;

	/**
	 * The session identifier
	 *
	 * @var  string
	 **/
	private $session_id = null;

	/**
	 * The site secret key
	 *
	 * @var  string
	 **/
	private $secret = null;

	/**
	 * Constructs the moderator
	 *
	 * @param   string  $identifier  The entity identifier, either a path or a token hash
	 * @param   string  $session_id  The PHP session id to use when creating the token
	 * @param   string  $secret      The site secret used when creating the token
	 * @return  void
	 **/
	public function __construct($identifier = null, $session_id = null, $secret = null)
	{
		if (is_file($identifier))
		{
			$this->path = $identifier;
		}
		else
		{
			$this->decompose($identifier);
		}

		// We assume that if session_id and secret aren't included, that we're
		// in an environement where we can easily grab them.
		$this->session_id = $session_id ?: App::get('session')->getId();
		$this->secret     = $secret     ?: Config::get('secret');
	}

	/**
	 * Builds the url identifier to the content
	 *
	 * @return  string
	 **/
	public function getUrl()
	{
		return '/files/' . $this->getIdentifier();
	}

	/**
	 * Gets the file path
	 *
	 * @return  string
	 **/
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Validates the given token against the session date
	 *
	 * @return  bool
	 **/
	public function validateToken()
	{
		return $this->token === $this->getToken();
	}

	/**
	 * Generates the url string identifier
	 *
	 * @return  string
	 **/
	private function getIdentifier()
	{
		return base64_encode($this->getToken() . ':' . $this->path);
	}

	/**
	 * Generates the request token
	 *
	 * @return  string
	 **/
	private function getToken()
	{
		return hash('sha256', $this->session_id . ':' . $this->secret);
	}

	/**
	 * Unpacks the token into meaninful bits
	 *
	 * @param   string  $identifier  The identifier to process
	 * @return  string
	 **/
	private function decompose($identifier)
	{
		$identifier = base64_decode($identifier);

		list($token, $path) = explode(':', $identifier, 2);

		$this->token = $token;
		$this->path  = $path;
	}
}
