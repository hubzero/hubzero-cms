<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models\Mailinglist;

use Hubzero\Database\Relational;

/**
 * Newsletter model for a mailinglist email
 */
class Email extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter_mailinglist';

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
		'email' => 'notempty',
		'mid'   => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between mailinglist and mailing
	 *
	 * @return  object
	 */
	public function mailinglist()
	{
		return $this->belongsToOne('Components\\Newsletter\\Models\\Mailinglist', 'mid');
	}

	/**
	 * Defines a belongs to one relationship between email and unsubscribe
	 *
	 * @return  object
	 */
	public function unsubscribe()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Unsubscribe', 'email', 'email');
	}
}
