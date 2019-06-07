<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project databases
 */
class Database extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_databases', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   string  $identifier  database name or id
	 * @return  mixed   object or false
	 */
	public function loadRecord($identifier = null)
	{
		if ($identifier === null)
		{
			return false;
		}
		$name = is_numeric($identifier) ? 'id' : 'database_name';

		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE $name=" . $this->_db->quote($identifier) . " LIMIT 1");
		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		else
		{
			return false;
		}
	}

	/**
	 * Get items
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @param   boolean  $skipPublished
	 * @return  mixed    object, integer or null
	 */
	public function getItems($projectid = null, $filters = array(), $skipPublished = false)
	{
		if ($projectid == null)
		{
			return false;
		}

		$count  = isset($filters['count']) ? $filters['count'] : 0;

		$query  = "SELECT ";
		$query .= $count ? " COUNT(*) " : "*";
		$query .= " FROM $this->_tbl ";
		$query .= " WHERE project = " . $this->_db->quote($projectid);
		$query .= $skipPublished ? " AND revision IS null" : "";

		$this->_db->setQuery($query);
		return $count ? $this->_db->loadResult() :  $this->_db->loadObjectList();
	}

	/**
	 * Get items
	 *
	 * @param   integer  $projectid
	 * @param   array    $filters
	 * @return  mixed    object, integer or null
	 */
	public function getResource($projectid = null, $id = null)
	{
		if ($projectid == null || $id == null)
		{
			return false;
		}

		$query = "SELECT database_name, title FROM $this->_tbl WHERE id="
			. $this->_db->quote($id) . " AND project=" . $this->_db->quote($projectid);

		$this->_db->setQuery($query);
		return $this->_db->loadAssoc();
	}

	/**
	 * Get database list
	 *
	 * @param   integer  $projectid
	 * @return  mixed    object, integer or null
	 */
	public function getList($projectid = null)
	{
		if ($projectid == null)
		{
			return false;
		}

		$query = "SELECT db.id, db.project, db.database_name, db.title, db.source_file,
				db.source_dir, db.source_revision, db.description, db.data_definition,
				db.revision, db.created, db.created_by, c.name
				FROM $this->_tbl AS db LEFT JOIN `#__users` AS c ON (c.id=db.created_by)
				WHERE project = " . $this->_db->quote($projectid) . " ORDER BY db.created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadAssocList();
	}

	/**
	 * Get used items
	 *
	 * @param   integer  $projectid
	 * @return  mixed    object, integer or null
	 */
	public function getUsedItems($projectid = null)
	{
		if ($projectid == null)
		{
			return false;
		}

		$query = "SELECT DISTINCT IF(source_dir != '', CONCAT(source_dir, '/', source_file), source_file) as file
				  FROM $this->_tbl
				  WHERE project = " . $this->_db->quote($projectid);

		$this->_db->setQuery($query);
		return $this->_db->loadColumn();
	}
}
