<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;

/**
 * Model class for recording if a user has viewed a message
 */
class Seen extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xmessage';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__xmessage_seen';

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
		'mid' => 'positive|nonzero',
		'uid' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between entry and message
	 *
	 * @return  object
	 */
	public function message()
	{
		return $this->belongsToOne('Message', 'mid');
	}

	/**
	 * Defines a belongs to one relationship between entry and user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uid');
	}

	/**
	 * Load a record by message ID and user ID and bind to $this
	 *
	 * @param   integer  $mid  Message ID
	 * @param   integer  $uid  User ID
	 * @return  object
	 */
	public static function oneByMessageAndUser($mid, $uid)
	{
		return self::all()
			->whereEquals('mid', $mid)
			->whereEquals('uid', $uid)
			->row();
	}
}
