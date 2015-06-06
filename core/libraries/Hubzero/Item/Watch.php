<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Hubzero\Item;

/**
 * Table class for item watch
 */
class Watch extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__item_watch', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(\Lang::txt('Missing item ID.'));
			return false;
		}

		$this->item_type = strtolower(preg_replace("/[^a-zA-Z0-9\-]/", '', trim($this->item_type)));
		if (!$this->item_type)
		{
			$this->setError(\Lang::txt('Missing item type.'));
			return false;
		}

		if (!$this->created_by)
		{
			$this->created_by = \User::get('id');
		}
		if (!$this->email)
		{
			$this->email = \User::get('email');
		}

		if (!$this->id)
		{
			$this->created = \Date::toSql();
		}

		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 *
	 * Long description (if any) ...
	 *
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c";

		$where = array();

		if (isset($filters['state']))
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "c.state IN (" . implode(',', $filters['state']) . ")";
			}
			else if ($filters['state'] >= 0)
			{
				$where[] = "c.state=" . $this->_db->Quote(intval($filters['state']));
			}
		}

		if (isset($filters['item_type']) && $filters['item_type'] >= 0)
		{
			$where[] = "c.item_type=" . $this->_db->Quote($filters['item_type']);
		}

		if (isset($filters['item_id']) && $filters['item_id'] >= 0)
		{
			$where[] = "c.item_id=" . $this->_db->Quote($filters['item_id']);
		}

		if (isset($filters['email']) && trim($filters['email']) != '')
		{
			$where[] = "c.email=" . $this->_db->Quote($filters['email']);
		}
		elseif (isset($filters['created_by']) && $filters['created_by'] >= 0)
		{
			$where[] = "c.created_by=" . $this->_db->Quote($filters['created_by']);
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param      array $filters Filters to build query off of
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get an array of records
	 *
	 * @param      array $filters Filters to build query off of
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query  = "SELECT c.*";
		$query .= " " . $this->buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'created';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir'])
		{
			$filters['sort_Dir'] = 'DESC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Is user watching item?
	 *
	 * @param  int $item_id
	 * @param  int $item_type
	 * @param  int $created_by
	 * @return object Return boolean
	 */
	public function isWatching($item_id, $item_type, $created_by)
	{
		if (!$item_id || !$item_type || !$created_by)
		{
			return false;
		}

		$filters = array(
			'state'      => 1,
			'created_by' => $created_by,
			'item_id'    => $item_id,
			'item_type'  => $item_type
		);

		$query  = "SELECT COUNT(*) ";
		$query .= $this->buildQuery($filters);

		$this->_db->setQuery($query);
		if (($total = $this->_db->loadResult()))
		{
			return true;
		}
		return false;
	}

	/**
	 * Load record
	 *
	 * @param  int $item_id
	 * @param  int $item_type
	 * @param  int $created_by
	 * @return object Return boolean
	 */
	public function loadRecord($item_id, $item_type, $created_by, $email = NULL)
	{
		if (!$item_id || !$item_type || (!$created_by && !$email))
		{
			return false;
		}

		$filters = array(
			'item_id'    => $item_id,
			'item_type'  => $item_type
		);
		if ($created_by)
		{
			$filters['created_by'] = $created_by;
		}
		else
		{
			$filters['email'] = $email;
		}

		$query  = "SELECT c.* ";
		$query .= $this->buildQuery($filters);

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind( $result );
		}
		else
		{
			return false;
		}
	}
}
