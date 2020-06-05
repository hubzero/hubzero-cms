<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Developer\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;

/**
 * Model class for an access token
 */
class Accesstoken extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'developer';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__developer_access_tokens';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'expires';

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
		'access_token'   => 'notempty',
		'application_id' => 'positive|nonzero',
		'uidNumber'      => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'expires'
	);

	/**
	 * Load code details by token
	 * 
	 * @param   string  $token
	 * @return  object
	 */
	public static function oneByToken($token)
	{
		$code = self::all()
			->whereEquals('access_token', $token)
			->row();

		return $code;
	}

	/**
	 * Is this application published
	 * 
	 * @return  bool
	 */
	public function isPublished()
	{
		return $this->get('state') == self::STATE_PUBLISHED;
	}

	/**
	 * Generates automatic expires field
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticExpires($data)
	{
		if (!isset($data['expires']))
		{
			$dt = new Date($data['created']);
			$dt->modify('+1 hour');
			$data['expires'] = $dt->toSql();
		}

		return $data['expires'];
	}

	/**
	 * Return Instance of application for token
	 * 
	 * @return  object
	 */
	public function application()
	{
		return $this->belongsToOne('Application', 'application_id');
	}

	/** 
	 * Expire code
	 * 
	 * @return  bool
	 */
	public function expire()
	{
		$this->set('state', self::STATE_DELETED);
		$this->set('expires', with(new Date('now'))->toSql());

		if (!$this->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function created($as='')
	{
		$check = strtolower($as);

		if ($check == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($check == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('created'))->toLocal($as);
		}

		return $this->get('created');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $as  What format to return
	 * @return  string
	 */
	public function expires($as='')
	{
		$check = strtolower($as);

		if ($check == 'date')
		{
			return Date::of($this->get('expires'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($check == 'time')
		{
			return Date::of($this->get('expires'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		if ($as)
		{
			return Date::of($this->get('expires'))->toLocal($as);
		}

		return $this->get('expires');
	}
}
