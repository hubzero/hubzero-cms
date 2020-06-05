<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job prefs
 */
class Prefs extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_prefs', 'id', $db);
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $uid       User ID
	 * @param   string   $category  Category
	 * @return  boolean  True upon success
	 */
	public function loadPrefs($uid, $category = 'resume')
	{
		if ($uid === null)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM `$this->_tbl` WHERE uid=" . $this->_db->quote($uid) . " AND category=" . $this->_db->quote($category) . " LIMIT 1");

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}
}
