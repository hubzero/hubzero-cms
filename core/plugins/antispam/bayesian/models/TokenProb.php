<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Bayesian\Models;

use Hubzero\Database\Relational;
use Date;

/**
 * Antispam Token Probabilities
 */
class TokenProb extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'antispam_token';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__antispam_token_probs';

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
		'token' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified'
	);

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return Date::toSql();
	}

	/**
	 * Load an entry by token
	 *
	 * @param   string  $token
	 * @return  object
	 */
	public static function oneByToken($token)
	{
		return self::all()
			->whereEquals('token', $token)
			->row();
	}
}
