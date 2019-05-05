<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Lang;

/**
 * Events table class for category
 */
class Category extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->title) == '')
		{
			$this->_error = Lang::txt('EVENTS_CATEGORY_MUST_HAVE_TITLE');
			return false;
		}
		return true;
	}

	/**
	 * Update the count of an entry
	 *
	 * @param   integer  $oid  Category ID
	 * @return  void
	 */
	public function updateCount($oid=null)
	{
		if ($oid == null)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET count = count-1 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Set en entry to unpublished
	 *
	 * @param   integer  $oid     Category ID
	 * @param   integer  $state
	 * @param   integer  $userId
	 * @return  void
	 */
	public function publish($oid = null, $state = 1, $userId = 0)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET published=1 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Set an entry to published
	 *
	 * @param   integer  $oid  Category ID
	 * @return  void
	 */
	public function unpublish($oid=null)
	{
		if (!$oid)
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET published=0 WHERE id=" . $this->_db->quote($oid));
		$this->_db->query();
	}

	/**
	 * Get a count of categories in a section
	 *
	 * @param   integer  $section  Section ID
	 * @return  integer
	 */
	public function getCategoryCount($section=null)
	{
		if (!$section)
		{
			$section = $this->section;
		}
		$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE extension=" . $this->_db->quote($section));
		return $this->_db->loadResult();
	}
}
