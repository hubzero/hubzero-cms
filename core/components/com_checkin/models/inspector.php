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

namespace Components\Checkin\Models;

use Hubzero\Base\Object;
use Request;
use App;

/**
 * Checkin Inspector Model
 */
class Inspector extends Object
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
	 * Total items
	 *
	 * @var  integer
	 */
	protected $total;

	/**
	 * List of tables
	 *
	 * @var  array
	 */
	protected $tables;

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
			$this->state->set('filter.search', Request::getState('com_checkin.filter.search', 'filter_search'));

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
				'com_checkin.limitstart',
				'limitstart',
				0,
				'int'
			);
			$limitstart = ($limit != 0 ? (floor($value / $limit) * $limit) : 0);
			$this->state->set('list.start', $limitstart);

			// Ordering
			$value = Request::getState(
				'com_checkin.list.ordering',
				'filter_order',
				'table'
			);
			if (!in_array($value, array('table', 'count')))
			{
				$value = 'table';
			}
			$this->state->set('list.ordering', $value);

			// Order direction
			$value = Request::getState(
				'com_checkin.list.direction',
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
	 * Checks in requested tables
	 *
	 * @param   array    $ids  An array of table names. Optional.
	 * @return  integer  Checked in item count
	 */
	public function checkin($ids = array())
	{
		// This int will hold the checked item count
		$results = 0;

		if (!is_array($ids))
		{
			return $results;
		}

		$db       = $this->db;
		$nullDate = $db->getNullDate();

		foreach ($ids as $tn)
		{
			// make sure we get the right tables based on prefix
			if (stripos($tn, $db->getPrefix()) !== 0)
			{
				continue;
			}

			$fields = $db->getTableColumns($tn);

			if (!(isset($fields['checked_out']) && isset($fields['checked_out_time'])))
			{
				continue;
			}

			$values = array(
				'checked_out' => 0,
				'checked_out_time' => $nullDate
			);

			if (isset($fields[$tn]['editor']))
			{
				$values['editor'] = '';
			}

			$query = $db->getQuery()
				->update($tn)
				->set($values)
				->where('checked_out', '>', '0');

			if ($query->execute())
			{
				$results = $results + $db->getAffectedRows();
			}
		}
		return $results;
	}

	/**
	 * Get total of tables
	 *
	 * @return  integer  Total to check-in tables
	 */
	public function total()
	{
		if (!isset($this->total))
		{
			$this->items();
		}

		return $this->total;
	}

	/**
	 * Get tables
	 *
	 * @return  array  Checked in table names as keys and checked in item count as values
	 */
	public function items()
	{
		if (!isset($this->items))
		{
			$db     = $this->db;
			$tables = $db->getTableList();

			// this array will hold table name as key and checked in item count as value
			$results = array();

			foreach ($tables as $i => $tn)
			{
				// make sure we get the right tables based on prefix
				if (stripos($tn, $db->getPrefix()) !== 0)
				{
					unset($tables[$i]);
					continue;
				}

				if ($this->state('filter.search') && stripos($tn, $this->state('filter.search')) === false)
				{
					unset($tables[$i]);
					continue;
				}

				$fields = $db->getTableColumns($tn);

				if (!(isset($fields['checked_out']) && isset($fields['checked_out_time'])))
				{
					unset($tables[$i]);
					continue;
				}
			}

			foreach ($tables as $tn)
			{
				$query = $db->getQuery()
					->select('COUNT(*)')
					->from($tn)
					->where('checked_out', '>', 0);

				$db->setQuery($query->toString());

				if ($result = $db->loadResult())
				{
					$results[$tn] = $result;
				}
			}

			$this->total = count($results);

			if ($this->state('list.ordering') == 'table')
			{
				if ($this->state('list.direction') == 'asc')
				{
					ksort($results);
				}
				else
				{
					krsort($results);
				}
			}
			else
			{
				if ($this->state('list.direction') == 'asc')
				{
					asort($results);
				}
				else
				{
					arsort($results);
				}
			}

			$results = array_slice(
				$results,
				$this->state('list.start'),
				$this->state('list.limit') ? $this->state('list.limit') : null
			);

			$this->items = $results;
		}

		return $this->items;
	}
}
