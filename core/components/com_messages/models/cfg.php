<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Models;

use Hubzero\Database\Relational;
use User;

/**
 * Model class for a message
 */
class Cfg extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'messages';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__messages_cfg';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'cfg_name';

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
		'cfg_name' => 'notempty',
		'cfg_value' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'user_id'
	);

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 * @since   2.0.0
	 **/
	public function automaticUserId($data)
	{
		return (isset($data['user_id']) && $data['user_id'] ? (int)$data['user_id'] : (int)User::get('id'));
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string  $path       The path to load by
	 * @param   string  $extension  The extnsion type
	 * @return  object
	 */
	public static function oneByUserAndName($user_id, $name)
	{
		return self::blank()
			->whereEquals('user_id', $user_id)
			->whereEquals('cfg_name', $name)
			->row();
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}
}
