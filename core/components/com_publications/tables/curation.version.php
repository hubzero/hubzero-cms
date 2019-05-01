<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication curation history
 */
class CurationVersion extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_curation_versions', 'id', $db);
	}

	/**
	 * Get last record for type
	 *
	 * @param   integer  $type_id  Publication Master Type ID
	 * @param   string   $get
	 * @return  mixed    False if error, Object on success
	 */
	public function getLatest($type_id = '', $get = '*')
	{
		$query = "SELECT $get FROM $this->_tbl WHERE type_id=" . $this->_db->quote($type_id);
		$query.= " ORDER BY id DESC LIMIT 1";

		$this->_db->setQuery($query);

		$result = $this->_db->loadObjectList();
		return $result ? $result[0] : null;
	}

	/**
	 * Load last record for type
	 *
	 * @param   integer  $type_id  Publication Master Type ID
	 * @return  mixed    False if error, Object on success
	 */
	public function loadLatest($type_id = '')
	{
		$query = "SELECT * FROM $this->_tbl WHERE type_id=" . $this->_db->quote($type_id);
		$query.= " ORDER BY id DESC LIMIT 1";
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
