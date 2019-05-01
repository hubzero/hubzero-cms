<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models\Mailing;

use Hubzero\Database\Relational;

/**
 * Newsletter model for a mailing recipient
 */
class Recipient extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter_mailing';

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
		'email' => 'notempty'
	);

	/**
	 * Defines a belongs to one relationship between mailing and recipient
	 *
	 * @return  object
	 */
	public function mailing()
	{
		return $this->belongsToOne('Components\\Newsletter\\Models\\Mailing', 'mid');
	}
}
