<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Exception;

/**
 * Search result SQL
 */
class Sql extends SearchResult
{
	/**
	 * Constructor
	 *
	 * @param   string  $sql
	 * @return  void
	 */
	public function __construct($sql = null)
	{
		$this->sql = $sql;
	}

	/**
	 * Get the SQL
	 *
	 * @return  string
	 */
	public function get_sql()
	{
		return $this->sql;
	}

	/**
	 * Return results as associative array
	 *
	 * @return  object
	 * @throws  SearchPluginError
	 */
	public function to_associative()
	{
		$db = \App::get('db');
		$db->setQuery($this->sql);

		if (!($rows = $db->loadAssocList()))
		{
			if ($error = $db->getErrorMsg())
			{
				throw new Exception('Invalid SQL in ' . $this->sql . ': ' . $error);
			}

			$rows = array();
		}

		return new AssocList($rows, $this->get_plugin());
	}
}
