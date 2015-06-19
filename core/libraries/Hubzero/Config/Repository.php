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

namespace Hubzero\Config;

/**
 * Repository class
 */
class Repository extends Registry
{
	/**
	 * The current client type (admin, site, api, etc).
	 *
	 * @var  string
	 */
	protected $client;

	/**
	 * The loader implementation.
	 *
	 * @var  object
	 */
	protected $loader;

	/**
	 * Create a new configuration repository.
	 *
	 * @param   object  $loader
	 * @param   string  $client
	 * @return  void
	 */
	public function __construct($loader, $client)
	{
		$this->loader = $loader;
		$this->client = $client;

		$items = $this->load($this->client);

		parent::__construct($items);
	}

	/**
	 * Parse data and bind to this
	 *
	 * @param   mixed  $data
	 * @return  bool
	 * @deprecated  Implemented purely for legacy compatibility
	 */
	/*public function loadObject($data)
	{
		return $this->parse($data);
	}*/

	/**
	 * Parse data and bind to this
	 *
	 * @param   mixed  $data
	 * @return  bool
	 * @deprecated  Implemented purely for legacy compatibility
	 */
	/*public function loadString($data)
	{
		return $this->parse($data);
	}*/

	/**
	 * Parse data and bind to this
	 *
	 * @param   mixed  $data
	 * @return  bool
	 * @deprecated  Implemented purely for legacy compatibility
	 */
	/*public function loadArray($data)
	{
		return $this->parse($data);
	}*/

	/**
	 * Load the configuration for a specified client.
	 *
	 * @param   string  $client
	 * @return  void
	 */
	public function load($client)
	{
		return $this->loader->load($client);
	}

	/**
	 * Get the loader implementation.
	 *
	 * @return  object
	 */
	public function getLoader()
	{
		return $this->loader;
	}

	/**
	 * Set the loader implementation.
	 *
	 * @param   object $loader
	 * @return  void
	 */
	public function setLoader($loader)
	{
		$this->loader = $loader;
	}

	/**
	 * Set the current configuration client.
	 *
	 * @param   string  $client
	 * @return  void
	 */
	public function setClient($client)
	{
		$this->client = (string) $client;
	}

	/**
	 * Get the current configuration client.
	 *
	 * @return  string
	 */
	public function getClient()
	{
		return $this->client;
	}
}
