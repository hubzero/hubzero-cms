<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job types
 */
class JobType extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_types', 'id', $db);
	}

	/**
	 * Get all records
	 *
	 * @param   string  $sortby   Sort by field
	 * @param   string  $sortdir  Sort direction ASC/DESC
	 * @return  array
	 */
	public function getTypes($sortby = 'id', $sortdir = 'ASC')
	{
		$types = array();

		$query  = "SELECT id, category FROM `$this->_tbl` ORDER BY $sortby $sortdir ";
		$this->_db->setQuery($query);
		$result = $this->_db->loadObjectList();
		if ($result)
		{
			foreach ($result as $r)
			{
				$types[$r->id] = $r->category;
			}
		}

		return $types;
	}

	/**
	 * Load a record from the database
	 *
	 * @param   integer  $id       Type ID
	 * @param   string   $default  Default value to return
	 * @return  string
	 */
	public function getType($id = null, $default = 'Unspecified')
	{
		if ($id === null)
		{
			 return false;
		}
		if ($id == 0)
		{
			return $default;
		}

		$query = "SELECT category FROM `$this->_tbl` WHERE id=" . $this->_db->quote($id);
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
