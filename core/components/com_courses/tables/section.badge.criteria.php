<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses section badge criteria table
 */
class SectionBadgeCriteria extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_offering_section_badge_criteria', 'id', $db);
	}
}
