<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;

/**
 * Secndary Citation model
 *
 * @uses \Hubzero\Database\Relational
 */
class Secondary extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'citations';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__citations_secondary';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 **/
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'cid' => 'positive|nonzero'
	);

	/**
	 * Defines the inverse relationship between a record and a task
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 **/
	public function citation()
	{
		return $this->belongsToOne('Citation', 'cid', 'id');
	}
}
