<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Publications\Reviews\Models;

use Hubzero\Database\Relational;
use Request;
use User;
use Lang;
use Date;

/**
 * Wishlist model class for a vote
 */
class Vote extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'vote';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__vote_log';

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
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'referenceid' => 'positive|nonzero',
		'category'    => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'voted',
		'voter',
		'ip'
	);

	/**
	 * Generates automatic voted field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticVoted($data)
	{
		return (isset($data['voted']) && $data['voted'] ? $data['voted'] : Date::toSql());
	}

	/**
	 * Generates automatic userid field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 **/
	public function automaticVoter($data)
	{
		return (isset($data['voter']) && $data['voter'] ? (int)$data['voter'] : (int)User::get('id'));
	}

	/**
	 * Generates automatic userid field value
	 *
	 * @param   array  $data  The data being saved
	 * @return  int
	 **/
	public function automaticIp($data)
	{
		return (isset($data['ip']) && $data['ip'] ? $data['ip'] : Request::ip());
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function voter()
	{
		return $this->belongsToOne('Hubzero\User\User', 'voter');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $rtrn  What data to return
	 * @return  string
	 */
	public function voted($rtrn='')
	{
		$rtrn = strtolower($rtrn);

		if ($rtrn == 'date')
		{
			return Date::of($this->get('voted'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($rtrn == 'time')
		{
			return Date::of($this->get('voted'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('voted');
	}

	/**
	 * Load a record by user and publication
	 *
	 * @param   integer  $voter
	 * @param   integer  $referenceid
	 * @return  object
	 */
	public static function oneByUserAndPublication($voter, $referenceid)
	{
		return self::all()
			->whereEquals('voter', $voter)
			->whereEquals('referenceid', $referenceid)
			->whereEquals('category', 'pubreview')
			->row();
	}
}
