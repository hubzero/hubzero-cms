<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Languages\Models;

use Hubzero\Database\Relational;

/**
 * Overrider model
 */
class Overrider extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = '';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__overrider';

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
		'constant' => 'notempty',
		'string'   => 'notempty',
		'file'     => 'notempty'
	);
}
