<?php 
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Redis;
use Predis\Client;

class Database
{
	/**
	 * The host address of the database.
	 *
	 * @var array
	 */
	protected $clients;

	/**
	 * Hold server vars
	 * 
	 * @var [type]
	 */
	protected $servers;

	/**
	 * Hold Client options
	 * 
	 * @var [type]
	 */
	protected $options;

	/**
	 * Create a new Redis connection instance.
	 *
	 * @param  array  $servers
	 * @return void
	 */
	public function __construct()
	{
		$config        = \JFactory::getConfig();
		$this->servers = (array) $config->get('redis_server', array());
		$this->options = (array) $config->get('redis_server_options', array());

		if (isset($this->options['cluster']) && $this->options['cluster'])
		{
			$this->clients = $this->createAggregateClient();
		}
		else
		{
			$this->clients = $this->createSingleClients();
		}
	}

	/**
	 * Create a new aggregate client supporting sharding.
	 *
	 * @param  array  $servers
	 * @return array
	 */
	protected function createAggregateClient()
	{
		$servers = array_values($this->servers);
		foreach ($servers as $k => $server)
		{
			$servers[$k] = (array) $server;
		}

		return array('default' => new Client($servers, $this->options));
	}

	/**
	 * Create an array of single connection clients.
	 *
	 * @param  array  $servers
	 * @return array
	 */
	protected function createSingleClients()
	{
		$clients = array();

		foreach ($this->servers as $key => $server)
		{
			$clients[$key] = new Client((array) $server, $this->options);
		}

		return $clients;
	}

	/**
	 * Get a specific Redis connection instance.
	 *
	 * @param  string  $name
	 * @return \Predis\Connection\SingleConnectionInterface
	 */
	public static function connect($name = 'default')
	{
		// create new instance of this class
		$self = new self;
		return $self->clients[$name ?: 'default'];
	}

	/**
	 * Run a command against the Redis database.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function command($method, array $parameters = array())
	{
		return call_user_func_array(array($this->clients['default'], $method), $parameters);
	}

	/**
	 * Dynamically make a Redis command.
	 *
	 * @param  string  $method
	 * @param  array   $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return $this->command($method, $parameters);
	}
}