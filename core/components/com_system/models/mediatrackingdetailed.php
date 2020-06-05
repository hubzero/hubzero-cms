<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Models;

use Hubzero\Database\Relational;

/**
 * media tracking detailed model
 *
 * @uses \Hubzero\Database\Relational
 */
class Mediatrackingdetailed extends Relational
{
	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__media_tracking_detailed';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'session_id'  => 'notempty',
		'ip_address'  => 'notempty',
		'object_id'   => 'positive|nonzero',
		'object_type' => 'notempty'
	);

	/**
	 * Get tracking info for a specific user/object combination
	 *
	 * @param   string  $object_id    Object ID
	 * @param   string  $object_type  Object type
	 * @param   string  $user_id      User ID
	 * @return  object
	 */
	public static function oneByUserAndObject($object_id, $object_type, $user_id = 0)
	{
		$query = self::all()
			->whereEquals('object_id', $object_id)
			->whereEquals('object_type', $object_type);

		if (!$user_id)
		{
			$session_id = \App::get('session')->getId();

			$query->whereEquals('session_id', $session_id);
		}
		else
		{
			$query->whereEquals('user_id', $user_id);
		}

		return $quer->limit(1)->row();
	}
}
