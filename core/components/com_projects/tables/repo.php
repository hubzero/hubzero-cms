<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project repos
 */
class Repo extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_repos', 'id', $db);
	}

	/**
	 * Get repos
	 *
	 * @param   integer  $projectid
	 * @return  mixed    object or null
	 */
	public function getRepos($projectid)
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE project_id=" . $this->_db->quote($projectid));
		return $this->_db->loadObjectList();
	}

	/**
	 * Load project repo
	 *
	 * @param   integer  $projectid
	 * @param   string   $name
	 * @return  mixed    object or false
	 */
	public function loadRepo($projectid = null, $name = null)
	{
		if ($projectid === null || $name === null)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE project_id=" . $this->_db->quote($projectid) . " AND name=" . $this->_db->quote($name) . " LIMIT 1";

		$this->_db->setQuery($query);
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
}
