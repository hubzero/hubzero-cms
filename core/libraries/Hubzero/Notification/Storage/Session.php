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

namespace Hubzero\Notification\Storage;

use Hubzero\Notification\MessageStore;

/**
 * Session storage handler.
 */
class Session implements MessageStore
{
	/**
	 * Session handler
	 *
	 * @var  object
	 */
	private $session;

	/**
	 * Constructor
	 *
	 * @param   object  $session
	 * @return  void
	 */
	public function __construct($session)
	{
		$this->session = $session;
	}

	/**
	 * Store a message
	 *
	 * @param   array   $data
	 * @param   string  $domain
	 * @return  void
	 */
	public function store($data, $domain)
	{
		$messages   = (array) $this->retrieve($domain);
		$messages[] = $data;

		$this->session->set($this->key($domain), $messages);
	}

	/**
	 * Return a list of messages
	 *
	 * @param   array   $data
	 * @param   string  $domain
	 * @return  array
	 */
	public function retrieve($domain)
	{
		$messages = $this->session->get($this->key($domain), array());

		if (count($messages))
		{
			$this->clear($domain);
		}

		return $messages;
	}

	/**
	 * Clear all messages
	 *
	 * @param   string  $domain
	 * @return  void
	 */
	public function clear($domain)
	{
		$this->session->set($this->key($domain), null);
	}

	/**
	 * Return a count of messages
	 *
	 * @param   string  $domain
	 * @return  integer
	 */
	public function total($domain)
	{
		$messages = $this->session->get($this->key($domain), array());

		return count($messages);
	}

	/**
	 * Get the storage key
	 *
	 * @param   string  $domain
	 * @return  string
	 */
	private function key($domain)
	{
		$domain = (!$domain ? '' : $domain . '.');

		return $domain . 'application.queue';
	}
} 