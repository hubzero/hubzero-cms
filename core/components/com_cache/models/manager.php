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

namespace Components\Cache\Models;

use Hubzero\Base\ClientManager;
use Hubzero\Base\Object;
use Request;
use App;

/**
 * Cache Model
 */
class Manager extends Object
{
	/**
	 * Database connection
	 *
	 * @var  object
	 */
	protected $db;

	/**
	 * A state object
	 *
	 * @var  object
	 */
	protected $state;

	/**
	 * Indicates if the internal state has been set
	 *
	 * @var  boolean
	 */
	protected $__state_set = null;

	/**
	 * An array of Cache Items indexed by cache group ID
	 *
	 * @var  array
	 */
	protected $data = array();

	/**
	 * Group total
	 *
	 * @var  integer
	 */
	protected $total = null;

	/**
	 * Pagination object
	 *
	 * @var  object
	 */
	protected $pagination = null;

	/**
	 * Constructor
	 *
	 * @param   array  $config  An array of configuration options
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct();

		// Set the model state
		if (array_key_exists('state', $config))
		{
			$this->state = $config['state'];
		}
		else
		{
			$this->state = new Object;
		}

		// Set the model dbo
		if (array_key_exists('dbo', $config))
		{
			$this->db = $config['dbo'];
		}
		else
		{
			$this->db = App::get('db');
		}

		// Set the internal state marker - used to ignore setting state from the request
		if (!empty($config['ignore_request']))
		{
			$this->__state_set = true;
		}
	}

	/**
	 * Method to get model state variables
	 *
	 * @param   string  $property  Optional parameter name
	 * @param   mixed   $default   Optional default value
	 * @return  object  The property where specified, the state object where omitted
	 */
	public function state($property = null, $default = null)
	{
		if (!$this->__state_set)
		{
			// Client
			$value = Request::getState(
				'com_cache.filter.client_id',
				'filter_client_id',
				0,
				'int'
			);
			$this->state->set('clientId', $value);

			$client = ClientManager::client($value);
			$this->state->set('client', $client);

			// Limit
			$value = Request::getState(
				'global.list.limit',
				'limit',
				App::get('config')->get('list_limit'),
				'int'
			);
			$limit = $value;
			$this->state->set('list.limit', $limit);

			$value = Request::getState(
				'com_cache.limitstart',
				'limitstart',
				0,
				'int'
			);
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->state->set('list.start', $limitstart);

			// Ordering
			$value = Request::getState(
				'com_cache.list.ordering',
				'filter_order',
				'group'
			);
			if (!in_array($value, array('group', 'count', 'size')))
			{
				$value = 'group';
			}
			$this->state->set('list.ordering', $value);

			// Order direction
			$value = Request::getState(
				'com_cache.list.direction',
				'filter_order_Dir',
				'asc'
			);
			if (!in_array(strtoupper($value), array('ASC', 'DESC', '')))
			{
				$value = 'asc';
			}
			$this->state->set('list.direction', $value);

			// Set the model state set flag to true.
			$this->__state_set = true;
		}

		return $property === null ? $this->state : $this->state->get($property, $default);
	}

	/**
	 * Method to get cache data
	 *
	 * @return  array
	 */
	public function data()
	{
		if (empty($this->data))
		{
			$this->data = array();

			$cache = $this->cache();
			$data  = $cache->all();

			if ($data != false)
			{
				$this->data  = $data;
				$this->total = count($data);

				if ($this->total)
				{
					// Apply custom ordering
					$ordering  = $this->state('list.ordering');
					$direction = ($this->state('list.direction') == 'asc') ? 1 : -1;

					$this->data = \Hubzero\Utility\Arr::sortObjects($data, $ordering, $direction);

					// Apply custom pagination
					if ($this->total > $this->state('list.limit') && $this->state('list.limit'))
					{
						$this->data = array_slice($this->data, $this->state('list.start'), $this->state('list.limit'));
					}
				}
			}
		}

		return $this->data;
	}

	/**
	 * Method to get cache instance
	 *
	 * @return  object
	 */
	public function cache()
	{
		$handler = App::get('config')->get('cache_handler');
		$client  = ClientManager::client($this->state('clientId'));

		App::get('config')->set($handler, array(
			'cachebase' => PATH_APP . '/cache/' . (isset($client->alias) ? $client->alias : $client->name) //($this->state('clientId') == 1 ? 'admin' : 'site')
		));

		$cache = new \Hubzero\Cache\Manager(\App::getRoot());
		$cache->storage($handler);

		return $cache;
	}

	/**
	 * Method to get client data
	 *
	 * @return  array
	 */
	public function client()
	{
		return $this->state('client');
	}

	/**
	 * Get the number of current Cache Groups
	 *
	 * @return  integer
	 */
	public function total()
	{
		if (empty($this->total))
		{
			$this->total = count($this->getData());
		}

		return $this->total;
	}

	/**
	 * Method to get a pagination object for the cache
	 *
	 * @return  integer
	 */
	public function pagination()
	{
		if (empty($this->pagination))
		{
			$this->pagination = new \Hubzero\Pagination\Paginator(
				$this->total(),
				$this->state('list.start'),
				$this->state('list.limit')
			);
		}

		return $this->pagination;
	}

	/**
	 * Clean out a cache group as named by param.
	 * If no param is passed clean all cache groups.
	 *
	 * @param   string  $group
	 * @return  void
	 */
	public function clean($group = '')
	{
		$this->cache()->clean($group);
	}

	/**
	 * Clean an array
	 *
	 * @param   array  $array
	 * @return  void
	 */
	public function cleanlist($array)
	{
		foreach ($array as $group)
		{
			$this->clean($group);
		}
	}

	/**
	 * Purge cache
	 *
	 * @return  boolean
	 */
	public function purge()
	{
		return $this->cache()->gc();
	}
}
