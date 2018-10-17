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

namespace Components\Publications\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Table class for publication master type
 */
class MasterType extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_master_types', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->type) == '')
		{
			$this->setError(Lang::txt('Your publication master type must contain text.'));
			return false;
		}
		if (trim($this->alias) == '')
		{
			$this->setError(Lang::txt('Your publication master type alias must contain text.'));
			return false;
		}
		return true;
	}

	/**
	 * Get record by alias name or ID
	 *
	 * @param   string  $id
	 * @return  mixed   object or false
	 */
	public function getType($id = '')
	{
		if (!$id)
		{
			return false;
		}
		$field = is_numeric($id) ? 'id' : 'alias';

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE $field=" . $this->_db->quote($id) . " LIMIT 1");
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Get record id by alias name
	 *
	 * @param   string  $alias
	 * @return  integer
	 */
	public function getTypeId($alias = '')
	{
		if (!$alias)
		{
			return false;
		}
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE alias=" . $this->_db->quote($alias) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get record alias by id
	 *
	 * @param   integer  $id
	 * @return  integer
	 */
	public function getTypeAlias($id='')
	{
		if (!$id)
		{
			return false;
		}
		$this->_db->setQuery("SELECT alias FROM $this->_tbl WHERE id=" . $this->_db->quote($id) . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get curator groups
	 *
	 * @return  array
	 */
	public function getCuratorGroups()
	{
		$groups = array();

		$query = "SELECT curatorgroup FROM $this->_tbl WHERE contributable=1
				  AND curatorgroup !=0 AND curatorgroup IS NOT null";

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		if ($results)
		{
			foreach ($results as $result)
			{
				if (trim($result->curatorgroup))
				{
					$groups[] = $result->curatorgroup;
				}
			}
		}

		return $groups;
	}

	/**
	 * Get types for which user is authorized (curation)
	 *
	 * @param   array  $usergroups
	 * @param   bool   $authorized
	 * @return  mixed  array or False
	 */
	public function getAuthTypes($usergroups = array(), $authorized = false)
	{
		$types = array();

		if (empty($usergroups))
		{
			return false;
		}
		if ($authorized == 'admin' || $authorized == 'curator')
		{
			// Access to all types
			$query = "SELECT id FROM $this->_tbl WHERE contributable=1";
		}
		else
		{
			$query = "SELECT id FROM $this->_tbl WHERE contributable=1
					  AND curatorgroup !=0 AND curatorgroup IS NOT null ";

			$tquery = '';
			foreach ($usergroups as $g)
			{
				$tquery .= "'" . $g->gidNumber . "',";
			}
			$tquery = substr($tquery, 0, strlen($tquery) - 1);
			$query .= " AND (curatorgroup IN (" . $tquery . ")) ";
		}

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();

		if ($results)
		{
			foreach ($results as $result)
			{
				if (trim($result->id))
				{
					$types[] = $result->id;
				}
			}
		}

		return $types;
	}

	/**
	 * Get records
	 *
	 * @param   string   $select         Select query
	 * @param   integer  $contributable  Contributable?
	 * @param   integer  $supporting     Supporting?
	 * @param   string   $orderby        Order by
	 * @param   string   $config
	 * @return  array
	 */
	public function getTypes($select = '*', $contributable = 0, $supporting = 0, $orderby = 'id', $config = '')
	{
		$query  = "SELECT $select FROM $this->_tbl ";
		if ($contributable)
		{
			$query .= "WHERE contributable=1 ";
		}
		elseif ($supporting)
		{
			$query .= "WHERE supporting=1 ";
		}

		$query .= "ORDER BY ".$orderby;

		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		if ($select == 'alias')
		{
			$types = array();
			if ($results)
			{
				foreach ($results as $result)
				{
					$types[] = $result->alias;
				}
			}
			return $types;
		}
		return $results;
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters = array())
	{
		$query  = "SELECT c.*";
		$query .= $this->_buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort'])
		{
			$filters['sort'] = 'id';
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
	 * Get record counts
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getCount($filters = array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters = array())
	{
		$query  = "FROM $this->_tbl AS c";

		$where = array();

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Check type usage
	 *
	 * @param   integer  $id  type id
	 * @return  integer
	 */
	public function checkUsage($id = null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		if (!$id)
		{
			return false;
		}

		include_once __DIR__ . DS . 'publication.php';

		$p = new \Components\Publications\Tables\Publication($this->_db);

		$this->_db->setQuery("SELECT count(*) FROM $p->_tbl WHERE master_type=" . $this->_db->quote($id));
		return $this->_db->loadResult();
	}

	/**
	 * Load by ordering
	 *
	 * @param   mixed  $ordering  Integer or string (alias)
	 * @return  mixed  False if error, Object on success
	 */
	public function loadByOrder($ordering = null)
	{
		if ($ordering === null)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE ordering=" . $this->_db->quote($ordering) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Change order
	 *
	 * @param   integer  $dir
	 * @return  mixed    False if error, Object on success
	 */
	public function changeOrder($dir)
	{
		$newOrder = $this->ordering + $dir;

		// Load record in prev position
		$old = new self($this->_db);

		if ($old->loadByOrder($newOrder))
		{
			$old->ordering  = $this->ordering;
			$old->store();
		}

		$this->ordering = $newOrder;
		$this->store();

		return true;
	}
}
