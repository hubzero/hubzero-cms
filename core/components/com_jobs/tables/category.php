<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job category
 */
class JobCategory extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_categories', 'id', $db);
	}

	/**
	 * Get all records
	 *
	 * @param   string   $sortby     Field to sort by
	 * @param   string   $sortdir    Sort direction (ASC/DESC)
	 * @param   integer  $getobject  Return records as objects?
	 * @return  array
	 */
	public function getCats($sortby = 'ordernum', $sortdir = 'ASC', $getobject = 0)
	{
		$cats = array();

		$query  = $getobject ? "SELECT * " : "SELECT id, category ";
		$query .= "FROM `$this->_tbl` ORDER BY $sortby $sortdir";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($getobject)
		{
			return $result;
		}

		if ($result)
		{
			foreach ($result as $r)
			{
				$cats[$r->id] = $r->category;
			}
		}

		return $cats;
	}

	/**
	 * Get a category
	 *
	 * @param   itneger  $id       Category ID
	 * @param   string   $default  Default value if no record found
	 * @return  mixed    False if errors, String upon success
	 */
	public function getCat($id = null, $default = 'Unspecified')
	{
		if ($id === null)
		{
			 return false;
		}
		if ($id == 0)
		{
			return $default;
		}

		$query  = "SELECT category FROM `$this->_tbl` WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Update the ordering of records
	 *
	 * @param   integer  $id        Category ID
	 * @param   integer  $ordernum  ORder number to make it
	 * @return  boolean  True upon success
	 */
	public function updateOrder($id = null, $ordernum = 1)
	{
		if ($id == null or !intval($ordernum))
		{
			 return false;
		}

		$query  = "UPDATE `$this->_tbl` SET ordernum=" . $this->_db->quote($ordernum) . " WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
