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
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Redis;

use Predis\Client;

/**
 * Redis Database helper
 */
class Database
{
	/**
	 * The host address of the database.
	 *
	 * @var  array
	 */
	protected $clients;

	/**
	 * Hold server vars
	 * 
	 * @var  array
	 */
	protected $servers;

	/**
	 * Hold Client options
	 * 
	 * @var  array
	 */
	protected $options;

	/**
	 * Create a new Redis connection instance.
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->servers = (array) \Config::get('redis_server', array());
		$this->options = (array) \Config::get('redis_server_options', array());

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
	 * @return  array
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
	 * @return  array
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
	 * @param   string  $name
	 * @return  object
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
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function command($method, array $parameters = array())
	{
		return call_user_func_array(array($this->clients['default'], $method), $parameters);
	}

	/**
	 * Dynamically make a Redis command.
	 *
	 * @param   string  $method
	 * @param   array   $parameters
	 * @return  mixed
	 */
	public function __call($method, $parameters)
	{
		return $this->command($method, $parameters);
	}
}