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
		$this->message($message, 'info');

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
		$this->message($message, 'success');

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
		$this->message($message, 'danger');

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
		$this->message($message, 'warning');

		return $this;
	}

	/**
	 * Flash a general message.
	 *
	 * @param   string  $message
	 * @param   string  $type
	 * @return  $this
	 */
	public function message($message, $type = 'info', $domain = null)
	{
		$this->storage->store(
			array(
				'message' => $message,
				'type'    => $type
			),
			$domain
		);

		return $this;
	}

	/**
	 * Check if there are any messages
	 *
	 * @return  boolean
	 */
	public function isEmpty($domain = null)
	{
		return !$this->any($domain);
	}

	/**
	 * Check if there are any messages
	 *
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
	 * Get the instance as an array.
	 *
	 * @param   string   $domain
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
	 * @return string
	 */
	public function __toString()
	{
		return $this->toJson();
	}
}
