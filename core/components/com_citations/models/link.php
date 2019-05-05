<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;

/**
 * Link model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Link extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'citations';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'url' => 'notempty',
		'citation_id' => 'positive|nonzero'
	);

	/**
	 * Defines the inverse relationship between a record and a task
	 *
	 * @return  \Hubzero\Database\Relationship\belongsToOne
	 */
	public function citation()
	{
		return $this->belongsToOne('Citation', 'citation_id', 'id');
	}
}
