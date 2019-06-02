<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;

/**
 * Support ticket criteria
 */
class Criterion extends Relational
{
	/**
	 * Table name
	 *
	 * @var  string
	 */
	protected $table = '#__support_criteria';

	/**
	 * Execute a condition and return results
	 *
	 * @return  array
	 */
	public function getViolations()
	{
		$violations = array();

		if ($query = $this->get('query'))
		{
			$db = \App::get('db');

			$violations = $db->setQuery($query)
				->execute()
				->loadRowList();
		}

		return $violations;
	}
}
