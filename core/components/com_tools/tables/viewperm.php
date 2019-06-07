<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;

/**
 * Table class for middleware view permissions
 */
class Viewperm extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('viewperm', 'sessnum', $db);
	}

	/**
	 * Load database rows
	 *
	 * @param   integer  $sess      Session number
	 * @param   string   $username  User to load for
	 * @return  array
	 */
	public function loadViewperm($sess=null, $username=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}
		$query = "SELECT * FROM $this->_tbl WHERE sessnum=" . $this->_db->quote($sess);
		if ($username)
		{
			$query .=  " AND viewuser=" . $this->_db->quote($username);
		}
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Update View perm
	 *
	 * @return  void
	 */
	public function updateViewPerm()
	{
		if (!isset($this->sessnum) || $this->sessnum === null || $this->sessnum == '')
		{
			return false;
		}

		if (!isset($this->viewuser) || $this->viewuser === null || $this->viewuser == '')
		{
			return false;
		}

		$sql = "UPDATE `viewperm` SET `viewtoken`=" . $this->_db->quote( $this->viewtoken ) . ", `geometry`=" . $this->_db->quote( $this->geometry ) . ", `fwhost`=" . $this->_db->quote( $this->fwhost ) . ", `fwport`=" . $this->_db->quote( $this->fwport ) . ", `vncpass`=" . $this->_db->quote( $this->vncpass ) . ", `readonly`=" . $this->_db->quote( $this->readonly ) . " WHERE `sessnum`=" . $this->_db->quote( $this->sessnum ) . " AND `viewuser`=" . $this->_db->quote( $this->viewuser );
		$this->_db->setQuery( $sql );
		$this->_db->query();
	}

	/**
	 * Delete a record
	 *
	 * @param   integer  $sess      Session number
	 * @param   string   $username  User to delete for
	 * @return  boolean  False if errors, True if success
	 */
	public function deleteViewperm($sess=null, $username=null)
	{
		if ($sess == null)
		{
			$sess = $this->sessnum;
		}
		if ($sess === null)
		{
			return false;
		}
		$query = "DELETE FROM $this->_tbl WHERE sessnum=" . $this->_db->quote($sess);
		if ($username)
		{
			$query .=  " AND viewuser=" . $this->_db->quote($username);
		}
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError(get_class($this) . '::delete failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Update a record
	 *
	 * @param   boolean  $updateNulls  Update null values?
	 * @return  boolean  False if errors, True if success
	 */
	public function update($updateNulls=false)
	{
		$ret = $this->_db->updateObject($this->_tbl, $this, $this->_tbl_key, $updateNulls);

		if (!$ret)
		{
			$this->setError(get_class($this) . '::update failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Insert a new record
	 *
	 * @return  boolean  False if errors, True if success
	 */
	public function insert()
	{
		$ret = $this->_db->insertObject($this->_tbl, $this, $this->_tbl_key);

		if (!$ret)
		{
			$this->setError(get_class($this) . '::insert failed - ' . $this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
