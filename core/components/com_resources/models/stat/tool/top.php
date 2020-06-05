<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models\Stat\Tool;

use Hubzero\Database\Relational;

/**
 * Resource stats tools model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Top extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource_stats_tools';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'top';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'top';

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
}
