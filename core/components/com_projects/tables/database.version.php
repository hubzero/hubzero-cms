<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project database versions
 */
class DatabaseVersion extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_database_versions', 'id', $db);
	}

	/**
	 * Get max version number
	 *
	 * @param    string  $dbname
	 * @return   mixed   integer or NULL
	 */
	public function getMaxVersion($dbname = '')
	{
		if ($dbname === null)
		{
			return false;
		}

		$query = "SELECT MAX(version) as version FROM $this->_tbl
					WHERE database_name=" . $this->_db->quote($dbname);

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}
