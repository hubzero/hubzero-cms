<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\BlackList\Models;

use Hubzero\Database\Relational;

/**
 * Antispam word blacklist
 */
class Word extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'antispam';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'word';

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
		'word' => 'notempty'
	);
}
