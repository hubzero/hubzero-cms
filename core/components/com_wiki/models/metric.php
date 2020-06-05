<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;

/**
 * Wiki model for page metrics
 */
class Metric extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'wiki';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'page_id';

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
		'page_id' => 'positive|nonzero',
		'scope'   => 'notempty'
	);

	/**
	 * Defines a belongs to one relationship between task and liaison
	 *
	 * @return  object
	 */
	public function page()
	{
		return $this->belongsToOne('Page', 'page_id');
	}
}
