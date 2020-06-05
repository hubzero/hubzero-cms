<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;

require_once __DIR__ . DS . 'mailinglist' . DS . 'email.php';
require_once __DIR__ . DS . 'mailinglist' . DS . 'unsubscribe.php';

/**
 * Newsletter model for a mailinglist
 */
class Mailinglist extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter';

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
	 * Get a list of emails
	 *
	 * @return  object
	 */
	public function emails()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Mailinglist\\Email', 'mid');
	}
}
