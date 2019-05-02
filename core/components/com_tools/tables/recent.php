<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;

/**
 * Table class for recent tools
 */
class Recent extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__recent_tools', 'id', $db);
	}

	/**
	 * Get a list of recently used tools
	 *
	 * @param   integer  $uid  User ID
	 * @return  array
	 */
	public function getRecords($uid=null)
	{
		if ($uid == null)
		{
			$uid = $this->uid;
		}
		if ($uid === null)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE uid=" . $this->_db->quote($uid) . " ORDER BY created DESC");
		return $this->_db->loadObjectList();
	}
}
