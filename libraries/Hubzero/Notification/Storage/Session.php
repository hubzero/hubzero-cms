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
	 * Get the storage key
	 *
	 * @param   string  $domain
	 * @return  string
	 */
	private function key($domain)
	{
		$domain = !$domain ?: $domain . '.';

		return $domain . 'application.queue';
	}
} 