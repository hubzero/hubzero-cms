<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Messages\Models;

use Hubzero\Database\Relational;
use User;
use Date;

/**
 * Model class for a message
 */
class Message extends Relational
{
	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'date_time';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'message_id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'subject' => 'notempty',
		'message' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'user_id_from',
		'date_time'
	);

	/**
	 * Generates automatic created by field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 * @since   2.0.0
	 **/
	public function automaticUserIdFrom($data)
	{
		return (isset($data['user_id_from']) && $data['user_id_from'] ? (int)$data['user_id_from'] : (int)User::get('id'));
	}

	/**
	 * Generates automatic created field value
	 *
	 * @return  string
	 */
	public function automaticDateTime()
	{
		return (isset($data['date_time']) && $data['date_time'] ? $data['date_time'] : Date::toSql());
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function from()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id_from');
	}

	/**
	 * Get the recipient of this entry
	 *
	 * @return  object
	 */
	public function to()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id_to');
	}

	/**
	 * Return a formatted timestamp for the created time
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		$as = strtolower($as);
		$dt = $this->get('date_time');

		if ($as == 'date')
		{
			return Date::of($dt)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($as == 'time')
		{
			return Date::of($dt)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($dt)->toLocal($as);
		}

		return $dt;
	}
}
