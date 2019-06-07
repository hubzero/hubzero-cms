<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;

/**
 * Table class for project types
 */
class Type extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_types', 'id', $db);
	}

	/**
	 * Get params
	 *
	 * @param   integer  $type
	 * @return  string   or null
	 */
	public function getParams($type = 1)
	{
		$this->_db->setQuery("SELECT params FROM $this->_tbl WHERE id=$type");
		return $this->_db->loadResult();
	}

	/**
	 * Get types
	 *
	 * @return  object  or null
	 */
	public function getTypes()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl");
		return $this->_db->loadObjectList();
	}

	/**
	 * Get type title
	 *
	 * @param   integer  $id
	 * @return  string   or null
	 */
	public function getTypeTitle($id = 0)
	{
		$this->_db->setQuery("SELECT type FROM $this->_tbl WHERE id=" . $this->_db->quote($id));
		return $this->_db->loadResult();
	}

	/**
	 * Get ID by type title
	 *
	 * @param   string  $type
	 * @return  string  or null
	 */
	public function getIdByTitle($type = '')
	{
		$this->_db->setQuery("SELECT id FROM $this->_tbl WHERE type=" . $this->_db->quote($type));
		return $this->_db->loadResult();
	}
}
