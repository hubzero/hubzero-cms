<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses grade book table
 */
class GradePolicies extends Table
{
	/**
	 * Constructor
	 *
	 * @param      object &$db Database
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses_grade_policies', 'id', $db);
	}
}
