<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Checkin\Models;

use JFactory;
use JModelList;

jimport('joomla.application.component.modellist');

/**
 * Checkin Model
 */
class Checkin extends JModelList
{
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering
	 * @param   string  $direction
	 * @return  unknown
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		// List state information.
		parent::populateState('table', 'asc');
	}

	/**
	 * Checks in requested tables
	 *
	 * @param   array    $ids  An array of table names. Optional.
	 * @return  integer  Checked in item count
	 */
	public function checkin($ids = array())
	{
		$db       = $this->_db;
		$nullDate = $db->getNullDate();

		if (!is_array($ids))
		{
			return;
		}

		// this int will hold the checked item count
		$results = 0;

		foreach ($ids as $tn)
		{
			// make sure we get the right tables based on prefix
			if (stripos($tn, Config::get('dbprefix')) !== 0)
			{
				continue;
			}

			$fields = $db->getTableColumns($tn);

			if (!(isset($fields['checked_out']) && isset($fields['checked_out_time'])))
			{
				continue;
			}

			$query = $db->getQuery(true)
				->update($db->quoteName($tn))
				->set('checked_out = 0')
				->set('checked_out_time = '.$db->Quote($nullDate))
				->where('checked_out > 0');
			if (isset($fields[$tn]['editor']))
			{
				$query->set('editor = NULL');
			}

			$db->setQuery($query);
			if ($db->query())
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
	public function getTotal()
	{
		if (!isset($this->total))
		{
			$this->getItems();
		}
		return $this->total;
	}

	/**
	 * Get tables
	 *
	 * @return  array  Checked in table names as keys and checked in item count as values
	 */
	public function getItems()
	{
		if (!isset($this->items))
		{
			$db       = $this->_db;
			$nullDate = $db->getNullDate();
			$tables   = $db->getTableList();

			// this array will hold table name as key and checked in item count as value
			$results = array();

			foreach ($tables as $i => $tn)
			{
				// make sure we get the right tables based on prefix
				if (stripos($tn, Config::get('dbprefix')) !== 0)
				{
					unset($tables[$i]);
					continue;
				}

				if ($this->getState('filter.search') && stripos($tn, $this->getState('filter.search')) === false)
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
				$query = $db->getQuery(true)
					->select('COUNT(*)')
					->from($db->quoteName($tn))
					->where('checked_out > 0');

				$db->setQuery($query);
				if ($db->query())
				{
					$results[$tn] = $db->loadResult();
				}
				else
				{
					continue;
				}
			}

			$this->total = count($results);

			if ($this->getState('list.ordering') == 'table')
			{
				if ($this->getState('list.direction') == 'asc')
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
				if ($this->getState('list.direction') == 'asc')
				{
					asort($results);
				}
				else
				{
					arsort($results);
				}
			}
			$results = array_slice($results, $this->getState('list.start'), $this->getState('list.limit') ? $this->getState('list.limit') : null);
			$this->items = $results;
		}
		return $this->items;
	}
}
