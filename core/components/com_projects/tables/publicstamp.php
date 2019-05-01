<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Tables;

use Hubzero\Database\Table;
use Date;
use User;

/**
 * Table class for project public links
 */
class Stamp extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__project_public_stamps', 'id', $db);
	}

	/**
	 * Load item
	 *
	 * @param   integer  $projectid  Project ID
	 * @return  mixed    False if error, Object on success
	 */
	public function loadItem($stamp = '')
	{
		if (!$stamp)
		{
			return false;
		}
		$now = Date::toSql();

		$query  = "SELECT * FROM $this->_tbl WHERE stamp=" . $this->_db->quote($stamp);
		$query .= " LIMIT 1";

		$this->_db->setQuery($query);
		if ($result = $this->_db->loadAssoc())
		{
			$this->bind($result);
			if ($this->expires && $this->expires != '0000-00-00 00:00:00' &&  $this->expires < $now)
			{
				// Clean up expired value
				$this->delete();
				return false;
			}
			else
			{
				return $this;
			}
		}
		else
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}

	/**
	 * Get listed items
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $type
	 * @return  object   array
	 */
	public function getPubList($projectid = 0, $type = '')
	{
		if (!$projectid)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE projectid=" . $this->_db->quote($projectid);
		$query .= " AND type=" . $this->_db->quote($type)
				. " AND listed=1 ORDER BY created DESC";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Check if stamp exists
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $reference  Reference string to object (JSON)
	 * @param   string   $type
	 * @return  mixed    False if error, Object on success
	 */
	public function checkStamp($projectid = 0, $reference = '', $type = '')
	{
		if (!$projectid || !$reference)
		{
			return false;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE projectid=" . $this->_db->quote($projectid);
		$query .= " AND reference=" . $this->_db->quote($reference)
				. " AND type= " . $this->_db->quote($type) . " LIMIT 1";

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

	/**
	 * Register stamp
	 *
	 * @param   integer  $projectid  Project ID
	 * @param   string   $reference  Reference string to object (JSON)
	 * @param   string   $type
	 * @param   integer  $listed
	 * @param   boolean  $expires
	 * @return  mixed    False if error, Object on success
	 */
	public function registerStamp($projectid = 0, $reference = '', $type = 'files', $listed = 0, $expires = null)
	{
		if (!$projectid || !$reference)
		{
			return false;
		}

		$now = Date::toSql();

		$obj = new self($this->_db);
		$obj->checkStamp($projectid, $reference, $type);

		// Load record
		if ($obj->id)
		{
			if ($obj->expires && $obj->expires != '0000-00-00 00:00:00' && $obj->expires < $now)
			{
				// Expired
				$obj->delete();
				return $this->registerStamp($projectid, $reference, $type, $listed, $expires);
			}
			else
			{
				if ($listed === null && $expires === null)
				{
					return $obj->stamp;
				}

				// These values may be updated
				$obj->listed  = $listed === null ? $obj->listed : $listed;
				$obj->expires = $expires === null ? $obj->expires : $expires;
				$obj->store();

				return $obj->stamp;
			}
		}

		// Make new entry
		$created = Date::toSql();
		$created_by = User::get('id');

		// Generate stamp
		require_once dirname(__DIR__) . DS . 'helpers' . DS . 'html.php';
		$stamp = \Components\Projects\Helpers\Html::generateCode(20, 20, 0, 1, 1);

		$query = "INSERT INTO $this->_tbl (stamp, projectid, listed, type, reference, expires, created, created_by)
				 VALUES ('$stamp', '$projectid', '$listed', '$type', '$reference', '$expires' , '$created', '$created_by')";

		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return $stamp;
	}
}
