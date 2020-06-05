<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tags\Models;

use Hubzero\Database\Relational;
use Date;
use User;

/**
 * Tag log
 */
class Log extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'tags';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__tags_log';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'action'  => 'notempty',
		'tag_id'  => 'positive|nonzero',
		'user_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'timestamp',
		'user_id',
		'actorid'
	);

	/**
	 * Generates automatic timestamp field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticTimestamp($data)
	{
		return Date::toSql();
	}

	/**
	 * Generates automatic user_id field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticUserId($data)
	{
		if (empty($data['user_id']))
		{
			$data['user_id'] = User::get('id');
		}
		return $data['user_id'];
	}

	/**
	 * Generates automatic actorid field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticActorid($data)
	{
		return User::get('id');
	}

	/**
	 * Get parent tag
	 *
	 * @return  object
	 */
	public function tag()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Tag', 'tag_id');
	}

	/**
	 * Actor profile
	 *
	 * @return  object
	 */
	public function actor()
	{
		return $this->belongsToOne('Hubzero\User\User', 'actorid');
	}
}
