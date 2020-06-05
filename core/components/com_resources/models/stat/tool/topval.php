<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Stat\Tool;

use Hubzero\Database\Relational;

/**
 * Resource stats tools topvals model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Topval extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_stats_tools';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'name' => 'notempty'
	);

	/**
	 * Defines a one to one relationship between entry and Top
	 *
	 * @return  object  \Hubzero\Database\Relationship\BelongsToOne
	 */
	public function top()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Top', 'top');
	}
}
