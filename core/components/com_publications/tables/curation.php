<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Tables;

use Hubzero\Database\Table;

/**
 * Table class for publication curation flow
 */
class Curation extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__publication_curation', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (!$this->publication_id)
		{
			$this->setError(Lang::txt('Must have a publication ID.'));
			return false;
		}

		if (!$this->publication_version_id)
		{
			$this->setError(Lang::txt('Must have a publication version ID.'));
			return false;
		}

		return true;
	}

	/**
	 * Get curation record
	 *
	 * @param   integer  $vid  Publication Version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getRecords($vid = null)
	{
		if (!intval($vid))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_version_id=" . $this->_db->quote($vid);
		$query.= " ORDER BY step ASC, element ASC ";
		$this->_db->setQuery($query);

		return $this->_db->loadObjectList();
	}

	/**
	 * Load record
	 *
	 * @param   integer  $pid  Publication ID
	 * @param   integer  $vid  Publication Version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function getRecord($pid = null, $vid = null, $block = null, $step = 0, $element = null)
	{
		if (!$pid || !$vid || !$block || !intval($step))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->quote($pid);
		$query.= " AND publication_version_id=" . $this->_db->quote($vid);
		$query.= " AND block=" . $this->_db->quote($block);
		$query.= " AND step=" . $this->_db->quote($step);
		$query.= $element ? " AND element=" . $this->_db->quote($element) : " AND (element IS null OR element=0)";
		$query.= " ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery($query);
		$results = $this->_db->loadObjectList();
		return $results ? $results[0] : null;
	}

	/**
	 * Load record
	 *
	 * @param   integer  $pid  Publication ID
	 * @param   integer  $vid  Publication Version ID
	 * @return  mixed    False if error, Object on success
	 */
	public function loadRecord($pid = null, $vid = null, $block = null, $step = 0, $element = null)
	{
		if (!$pid || !$vid || !$block || !intval($step))
		{
			return false;
		}

		$query = "SELECT * FROM $this->_tbl WHERE publication_id=" . $this->_db->quote($pid);
		$query.= " AND publication_version_id=" . $this->_db->quote($vid);
		$query.= " AND block=" . $this->_db->quote($block);
		$query.= " AND step=" . $this->_db->quote($step);
		$query.= $element ? " AND element=" . $this->_db->quote($element) : " AND (element IS null OR element=0)";
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
