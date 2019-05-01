<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses member badges table
 */
class MemberBadge extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_member_badges', 'id', $db);
	}

	/**
	 * Load by member id
	 *
	 * @param   integer  $id  member id
	 * @return  boolean
	 */
	public function loadByMemberId($id)
	{
		$query =   "SELECT *
					FROM {$this->_tbl}
					WHERE member_id = " . $this->_db->quote($id);

		$this->_db->setQuery($query);

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}

		$this->setError($this->_db->getErrorMsg());
		return false;
	}
}
