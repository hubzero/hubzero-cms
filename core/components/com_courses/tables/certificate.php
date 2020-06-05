<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Course certificate table class
 */
class Certificate extends Table
{
	/**
	 * Constructor
	 * 
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_certificates', 'id', $db);
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 * 
	 * @param      mixed $oid Unique ID or alias of object to retrieve
	 * @return     boolean True on success
	 */
	public function loadByCourse($course_id)
	{
		return parent::load(array(
			'course_id' => (int) $course_id
		));
	}
}
