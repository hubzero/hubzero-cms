<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication category
 */
class Category extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_categories', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->name) == '')
		{
			$this->setError(Lang::txt('Your publication category name must contain text.'));
			return false;
		}
		if (trim($this->alias) == '')
		{
			$this->setError(Lang::txt('Your publication category alias must contain text.'));
			return false;
		}
		if (trim($this->url_alias) == '')
		{
			$this->setError(Lang::txt('Your publication url alias name must contain text.'));
			return false;
		}
		return true;
	}

	/**
	 * Get record count
	 *
	 * @param   array   $filters
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT count(*) FROM $this->_tbl";
		$query .= isset($filters['state']) && $filters['state'] == 'all' ? '' : " WHERE state=1";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array   $filters
	 * @return  object
	 */
	public function getCategories($filters = array())
	{
		$query  = "SELECT * ";

		if (isset($filters['itemCount']) && $filters['itemCount'] == 1)
		{
			$query .= ", (SELECT COUNT(*) FROM #__publications as P
						JOIN #__publication_versions as V ON V.publication_id = P.id
						AND V.main=1 WHERE P.category = C.id AND V.state=1) AS itemCount ";
		}
		$query .= " FROM $this->_tbl as C ";

		if (isset($filters['state']) && $filters['state'] == 'all')
		{
			// don't limit by state
		}
		else
		{
			$query .= isset($filters['state']) && intval($filters['state']) > 0
					? 'WHERE C.state=' . $this->_db->quote($filters['state']) : " WHERE C.state=1 ";
		}

		$orderby  = isset($filters['sort']) ? $filters['sort'] : "name";
		$orderDir = isset($filters['sort_Dir']) && strtoupper($filters['sort_Dir']) == 'DESC' ? 'DESC' : 'ASC';
		switch ($orderby)
		{
			case 'name':
			default:
				$query .= ' ORDER BY C.name ' . $orderDir;

				break;
			case 'id':
				$query .= ' ORDER BY C.id ' . $orderDir;
				break;
			case 'state':
				$query .= ' ORDER BY C.state ' . $orderDir;
				break;
			case 'contributable':
				$query .= ' ORDER BY C.contributable ' . $orderDir;
				break;
		}

		if (isset($filters['start']) && isset($filters['limit']))
		{
			$query .= " LIMIT " . $filters['start'] . "," . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get record by alias name
	 *
	 * @param   string  $alias
	 * @return  mixed   object or false
	 */
	public function getCategory($alias = '')
	{
		if (!$alias)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->quote($alias)  . " LIMIT 1");
		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : false;
	}

	/**
	 * Get record ID by alias name
	 *
	 * @param   string  $alias
	 * @return  mixed   integer or null
	 */
	public function getCatId($alias='')
	{
		if (!$alias)
		{
			return false;
		}
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE alias=" . $this->_db->quote($alias)  . " LIMIT 1");
		return $this->_db->loadResult();
	}

	/**
	 * Get contributable categories
	 *
	 * @return  object
	 */
	public function getContribCategories()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE contributable=1 ORDER BY name");
		return $this->_db->loadObjectList();
	}

	/**
	 * Check usage by category
	 *
	 * @param   integer  $id
	 * @return  mixed    integer or null
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

		$this->_db->setQuery("SELECT count(*) FROM `#__publications` WHERE category=" . $this->_db->quote($id));
		return $this->_db->loadResult();
	}
}
