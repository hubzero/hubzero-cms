<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

use Hubzero\Database\Relational;

/**
 * A user's current standing on one scale
 *
 * Wholly derived from the ledger, and rebuildable from it. Two totals are
 * kept: `raw` accumulates unclamped so that somebody who earned their way
 * well past the ceiling does not fall below it on a single downmod, and
 * `karma` is that total clamped to the scale, which is what everything reads.
 */
class Balance extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'karma';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'karma';

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
		'user_id'  => 'positive|nonzero',
		'scale_id' => 'positive|nonzero'
	);

	/**
	 * Load the balance for one user on one scale
	 *
	 * @param   integer  $userId
	 * @param   integer  $scaleId
	 * @return  object
	 */
	public static function oneByUserAndScale($userId, $scaleId)
	{
		return self::all()
			->whereEquals('user_id', (int) $userId)
			->whereEquals('scale_id', (int) $scaleId)
			->row();
	}

	/**
	 * Load the balance for one user on one scale, or a blank one carrying
	 * the scale's initial value
	 *
	 * @param   integer  $userId
	 * @param   object   $scale
	 * @return  object
	 */
	public static function oneOrNewForScale($userId, Scale $scale)
	{
		$balance = self::oneByUserAndScale($userId, $scale->get('id'));

		if (!$balance->get('id'))
		{
			$balance->set(array(
				'user_id'        => (int) $userId,
				'scale_id'       => (int) $scale->get('id'),
				'raw'            => (float) $scale->get('initial'),
				'karma'          => $scale->clamp($scale->get('initial')),
				'positive_count' => 0,
				'negative_count' => 0
			));
		}

		return $balance;
	}

	/**
	 * Defines a belongs to one relationship with a scale
	 *
	 * @return  object
	 */
	public function scale()
	{
		return $this->belongsToOne('Hubzero\Karma\Scale', 'scale_id');
	}

	/**
	 * Defines a belongs to one relationship with a user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}
}
