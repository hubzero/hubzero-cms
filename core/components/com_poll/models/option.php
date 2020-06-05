<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Models;

use Hubzero\Database\Relational;

/**
 * Poll option model
 */
class Option extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'poll';

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
		'poll_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between option and poll
	 *
	 * @return  object
	 */
	public function poll()
	{
		return $this->belongsToOne('Poll', 'poll_id');
	}
}
