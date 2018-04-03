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

/**
 * Table class for publication license
 */
class License extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_licenses', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   mixed  $oid  Integer or string (alias)
	 * @return  mixed  False if error, Object on success
	 */
	public function loadLicense($oid = null)
	{
		if ($oid === null)
		{
			return false;
		}

		if (is_numeric($oid))
		{
			return parent::load($oid);
		}

		$oid = trim($oid);

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE name=" . $this->_db->quote($oid));
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
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->text = trim($this->text);

		if (!$this->title)
		{
			$this->title = substr($this->text, 0, 70);
			if (strlen($this->title >= 70))
			{
				$this->title .= '...';
			}
		}

		if (!$this->name)
		{
			$this->name = $this->title;
		}
		$this->name = str_replace(' ', '-', strtolower($this->name));
		$this->name = preg_replace("/[^a-zA-Z0-9\-_]/", '', $this->name);

		return true;
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
		if (isset($filters['state']))
		{
			$where[] = "c.state=" . $this->_db->quote($filters['state']);
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			$where[] = "(LOWER(c.title) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . "
				OR LOWER(c.`text`) LIKE " . $this->_db->quote('%' . strtolower($filters['search']) . '%') . ")";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
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
			$filters['sort'] = 'title';
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
	 * Get licenses
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  object
	 */
	public function getLicenses($filters = array())
	{
		$sortby  = isset($filters['sortby']) && $filters['sortby'] != '' ? $filters['sortby'] : 'ordering';

		$query  = "SELECT * FROM $this->_tbl ";
		$query .= " WHERE active=1 ";
		$query .= " ORDER BY " . $sortby;

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get licenses
	 *
	 * @param   object  $manifest
	 * @param   object  $selected
	 * @return  object
	 */
	public function getBlockLicenses($manifest = null, $selected = null)
	{
		if (!$manifest)
		{
			return false;
		}

		$include = isset($manifest->params->include) ? $manifest->params->include : array();
		$exclude = isset($manifest->params->exclude) ? $manifest->params->exclude : array();

		$query = "SELECT * FROM $this->_tbl WHERE active=1 ";
		if ($include && !empty($include))
		{
			$query .= " AND (";
			$i = 0;
			foreach ($include as $inc)
			{
				$i++;
				$query .= "id=" . $inc;
				$query .= $i == count($include) ? " " : " OR ";
			}
			$query .= ")";
		}
		if ($exclude && !empty($exclude))
		{
			$query .= " AND (";
			$i = 0;
			foreach ($exclude as $ex)
			{
				$i++;
				$query .= "id != " . $ex;
				$query .= $i == count($exclude) ? " " : " AND ";
			}
			$query .= ")";
		}
		if ($selected && !in_array($selected->id, $include))
		{
			$query .= " OR id=" . $selected->id;
		}

		$query .= " ORDER BY ordering";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get default license
	 *
	 * @return  object
	 */
	public function getDefaultLicense()
	{
		$query  = "SELECT * FROM $this->_tbl ";
		$query .= " WHERE main=1 ";
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get license by id
	 *
	 * @param   integer  $id  License ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getLicense ($id = 0)
	{
		if (!$id)
		{
			return false;
		}
		$query  = "SELECT * FROM $this->_tbl ";
		$query .= " WHERE id=" . $this->_db->quote($id);
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
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

	/**
	 * Get license by name
	 *
	 * @param   string  $name  License name
	 * @return  mixed   False if error, Object on success
	 */
	public function getLicenseByName($name = '')
	{
		if (!$name)
		{
			return false;
		}
		$query  = "SELECT * FROM $this->_tbl ";
		$query .= " WHERE name LIKE " . $this->_db->quote($name);
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Get license by title
	 *
	 * @param   string  $title  License title
	 * @return  mixed   False if error, Object on success
	 */
	public function getLicenseByTitle($title = '')
	{
		if (!$title)
		{
			return false;
		}
		$query  = "SELECT * FROM $this->_tbl ";
		$query .= " WHERE title LIKE " . $this->_db->quote($title);
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Get license by pub version id
	 *
	 * @param   integer  $vid  Pub version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getPubLicense($vid = null)
	{
		if (!$vid)
		{
			return false;
		}

		$query = "SELECT L.*, v.license_type, v.license_text FROM $this->_tbl AS L, `#__publication_versions` AS v ";
		$query.= " WHERE v.id=" . $this->_db->quote($vid) . " AND v.license_type=L.id";
		$query.= " LIMIT 1";

		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Make license  not default
	 *
	 * @param   integer  $except  License ID to exclude
	 * @return  boolean
	 */
	public function undefault($except = 0)
	{
		$query  = "UPDATE $this->_tbl SET main=0 ";
		$query .= $except ? " WHERE id != " . $this->_db->quote($except) : '';
		$this->_db->setQuery($query);
		if ($this->_db->query())
		{
			return true;
		}
		return false;
	}
}
