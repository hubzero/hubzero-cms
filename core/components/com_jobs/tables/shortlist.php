<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Jobs\Tables;

use Hubzero\Database\Table;

/**
 * Table class for job shortlist
 */
class Shortlist extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__jobs_shortlist', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (intval($this->emp) == 0)
		{
			$this->setError(Lang::txt('ERROR_MISSING_EMPLOYER_ID'));
			return false;
		}

		if (trim($this->seeker) == 0)
		{
			$this->setError(Lang::txt('ERROR_MISSING_JOB_SEEKER_ID'));
			return false;
		}

		return true;
	}

	/**
	 * Load a record and bind to $this
	 *
	 * @param   integer  $emp       Employer ID
	 * @param   integer  $seeker    Seeker ID
	 * @param   string   $category  Category
	 * @return  boolean  True upon success
	 */
	public function loadEntry($emp, $seeker, $category = 'resume')
	{
		if ($emp === null or $seeker === null)
		{
			return false;
		}

		$this->_db->setQuery("SELECT * FROM `$this->_tbl` WHERE emp=" . $this->_db->quote($emp) . " AND seeker=" . $this->_db->quote($seeker) . " AND category=" . $this->_db->quote($category) . " LIMIT 1");

		if ($result = $this->_db->loadAssoc())
		{
			return $this->bind($result);
		}
		return false;
	}
}
