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

namespace Hubzero\Notification\Storage;

use Hubzero\Notification\MessageStore;
use Hubzero\Utility\Cookie;

/**
 * Cookie storage handler.
 */
class Cookie implements MessageStore
{
	/**
	 * Lifetime of the cookie (in minutes)
	 *
	 * @var  itneger
	 */
	private $lifetime;

	/**
	 * Constructor
	 *
	 * @param   integer  lifetime
	 * @return  void
	 */
	public function __construct($lifetime)
	{
		$this->lifetime = $lifetime;
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

		Cookie::bake($this->key($domain), $this->expires($this->lifetime), $messages);
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
		if (!($messages = Cookie::eat($this->key($domain)))
		{
			$messages = array();
		}

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
		Cookie::bake($this->key($domain), $this->expires(0), array())
	}

	/**
	 * Return a count of messages
	 *
	 * @param   string  $domain
	 * @return  integer
	 */
	public function total($domain)
	{
		return count($this->retrieve($domain));
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

		return md5($domain . 'application.queue');
	}

	/**
	 * Get the expiration time a # of minutes from now
	 *
	 * @param   itneger  $minutes
	 * @return  integer
	 */
	private function expires($minutes)
	{
		return time() + 60 * $minutes;
	}
}