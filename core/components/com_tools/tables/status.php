<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Tables;

use Hubzero\Database\Table;
use User;
use Lang;

/**
 * Status table for a Tools
 */
class Status extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('session', 'sessnum', $db);
	}

	/**
	 * Get a count of current sessions
	 *
	 * @return integer
	 */
	public function getLastSession()
	{
		$sql = "SELECT sessnum,username,start,accesstime,appname,sessname FROM session ORDER BY sessnum DESC limit 1";

		$this->_db->setQuery($sql);
		return $this->_db->loadObject();
	}

	/**
	 * Get a count of current sessions
	 *
	 * @return integer
	 */
	public function getSessionCount()
	{
		$sql = "SELECT count(*) FROM session";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of used displays
	 *
	 * @return integer
	 */
	public function getUsedDisplayCount()
	{
		$sql = "SELECT count(*) FROM display where status='used'";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of ready displays
	 *
	 * @return integer
	 */
	public function getReadyDisplayCount()
	{
		$sql = "SELECT count(*) FROM display where status='ready'";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of absent displays
	 *
	 * @return integer
	 */
	public function getAbsentDisplayCount()
	{
		$sql = "SELECT count(*) FROM display where status='absent'";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

	/**
	 * Get a count of broken displays
	 *
	 * @return integer
	 */
	public function getBrokenDisplayCount()
	{
		$sql = "SELECT count(*) FROM display where status='broken'";

		$this->_db->setQuery($sql);
		return $this->_db->loadResult();
	}

}
