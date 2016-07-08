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

namespace Hubzero\Notification;

/**
 * Flash message handler
 */
class Handler
{
	/**
	 * The storage handler.
	 *
	 * @var  object
	 */
	private $storage;

	/**
	 * Create a new flash notifier instance.
	 *
	 * @param   object  $storage
	 * @return  void
	 */
	public function __construct(MessageStore $storage)
	{
		$this->storage = $storage;
	}

	/**
	 * Flash an information message.
	 *
	 * @param   string  $message
	 * @param   string  $domain
	 * @return  object
	 */
	public function info($message, $domain = null)
	{
		$this->message($message, 'info', $domain);

		return $this;
	}

	/**
	 * Flash a success message.
	 *
	 * @param   string  $message
	 * @param   string  $domain
	 * @return  object
	 */
	public function success($message, $domain = null)
	{
		$this->message($message, 'success', $domain);

		return $this;
	}

	/**
	 * Flash an error message.
	 *
	 * @param   string  $message
	 * @param   string  $domain
	 * @return  object
	 */
	public function error($message, $domain = null)
	{
		$this->message($message, 'error', $domain);

		return $this;
	}

	/**
	 * Flash a warning message.
	 *
	 * @param   string  $message
	 * @param   string  $domain
	 * @return  object
	 */
	public function warning($message, $domain = null)
	{
		$this->message($message, 'warning', $domain);

		return $this;
	}

	/**
	 * Flash a general message.
	 *
	 * @param   string  $message
	 * @param   string  $type
	 * @param   string  $domain
	 * @return  $this
	 */
	public function message($message, $type = 'info', $domain = null)
	{
		$messages = $this->storage->retrieve($domain);

		$duplicate = false;

		foreach ($messages as $m)
		{
			// If all the data is the same,
			// it's a duplicate message. Skip.
			if ($m['message'] == $message
			 && $m['type'] == $type)
			{
				$duplicate = true;
			}
		}

		if (!$duplicate)
		{
			$messages[] = array(
				'message' => $message,
				'type'    => $type
			);
		}

		foreach ($messages as $m)
		{
			$this->storage->store($m, $domain);
		}

		return $this;
	}

	/**
	 * Check if there are any messages
	 *
	 * @param   string  $domain
	 * @return  boolean
	 */
	public function isEmpty($domain = null)
	{
		return !$this->any($domain);
	}

	/**
	 * Check if there are any messages
	 *
	 * @param   string  $domain
	 * @return  boolean
	 */
	public function any($domain = null)
	{
		return ($this->storage->total($domain) > 0);
	}

	/**
	 * Get all messages
	 *
	 * @param   string  $domain
	 * @return  array
	 */
	public function messages($domain = null)
	{
		return $this->storage->retrieve($domain);
	}

	/**
	 * Clear all messages
	 *
	 * @param   string  $domain
	 * @return  object
	 */
	public function clear($domain = null)
	{
		$this->storage->retrieve($domain);

		return $this;
	}

	/**
	 * Get the instance as an array.
	 *
	 * @param   string  $domain
	 * @return  array
	 */
	public function toArray($domain = null)
	{
		return $this->messages($domain);
	}

	/**
	 * Convert the object to its JSON representation.
	 *
	 * @param   integer  $options
	 * @param   string   $domain
	 * @return  string
	 */
	public function toJson($options = 0, $domain = null)
	{
		return json_encode($this->toArray($domain), $options);
	}

	/**
	 * Convert the message bag to its string representation.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->toJson();
	}
}
