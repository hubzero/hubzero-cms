<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Message;

use Hubzero\Database\Relational;

/**
 * Model class for message notification
 */
class Notify extends Relational
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
	protected $table = '#__xmessage_notify';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'priority';

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
		'uid' => 'positive|nonzero'
	);

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
	 * Get records for a user
	 *
	 * @param   integer  $uid   User ID
	 * @param   string   $type  Record type
	 * @return  mixed    False if errors, array on success
	 */
	public function getRecords($uid, $type=null)
	{
		$entries = self::all()
			->whereEquals('uid', $uid);

		if ($type)
		{
			$entries->whereEquals('type', $type);
		}

		return $entries
			->order('priority', 'asc')
			->rows();
	}

	/**
	 * Clear all entries for a user
	 *
	 * @param   integer  $uid  User ID
	 * @return  boolean  True on success
	 */
	public function deleteByUser($uid)
	{
		return $this->delete($this->getTableName())
			->whereEquals('uid', $uid)
			->execute();
	}

	/**
	 * Delete notifications for action
	 *
	 * @param   string   $type
	 * @return  boolean  True on success, False on error
	 */
	public function deleteByType($type)
	{
		return $this->delete($this->getTableName())
			->whereEquals('type', $type)
			->execute();
	}
}
