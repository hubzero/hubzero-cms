<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Hubzero\Database\Relational;

/**
 * Model for wiki math conversions
 */
class Formula extends Relational
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
		'inputhash'  => 'notempty',
		'outputhash' => 'notempty'
	);

	/**
	 * Load a record by inputhash and bind to $this
	 *
	 * @param   string  $inputhash  Hash to load
	 * @return  object
	 */
	public static function oneByInputhash($inputhash)
	{
		return self::blank()->whereEquals('inputhash', $inputhash)->row();
	}
}
