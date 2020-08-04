<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
