<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Bayesian\Models;

use Hubzero\Database\Relational;

/**
 * Antispam message hash
 */
class MessageHash extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'antispam_message';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__antispam_message_hashes';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'hash';

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
		'hash' => 'notempty'
	);

	/**
	 * Load a record by hash
	 *
	 * @param   string  $hash
	 * @return  object
	 */
	public static function oneByHash($hash)
	{
		return self::all()
			->whereEquals('hash', $hash)
			->row();
	}
}
