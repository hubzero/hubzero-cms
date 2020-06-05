<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses progress factors table
 */
class ProgressFactors extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_progress_factors', 'id', $db);
		$this->_trackAssets = false;
	}

	/**
	 * Override save method to not create duplicates
	 *
	 * @param   mixed   $src             An associative array or object to bind to the Table instance.
	 * @param   string  $orderingFilter  Filter for the order updating
	 * @param   mixed   $ignore          An optional array or space separated list of properties
	 * @return  boolean  True on success.
	 **/
	public function save($src, $orderingFilter = '', $ignore = '')
	{
		$this->load($src);

		if ($this->get('id'))
		{
			$result = true;
		}
		else
		{
			$result = parent::save($src, $orderingFilter, $ignore);
		}

		return $result;
	}
}
