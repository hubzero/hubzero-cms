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
	 * Rebuild one user's balance on one scale from the ledger
	 *
	 * The balance is a cache, and this is what makes saying so true. Safe to
	 * run at any time: it reads the ledger, compares, and only writes when
	 * the cached figures actually disagree.
	 *
	 * @param   integer  $userId
	 * @param   object   $scale
	 * @param   string   $now     SQL datetime to stamp the recalculation with
	 * @return  bool     Whether anything changed
	 */
	public static function rebuild($userId, Scale $scale, $now = null)
	{
		$entries = Ledger::all()
			->whereEquals('subject_id', (int) $userId)
			->whereEquals('scale_id', (int) $scale->get('id'))
			->whereEquals('state', Ledger::STATE_ACTIVE)
			->rows();

		$raw      = (float) $scale->get('initial');
		$positive = 0;
		$negative = 0;
		$last     = null;

		foreach ($entries as $entry)
		{
			$delta = (float) $entry->get('delta');
			$raw  += $delta;

			if ($delta > 0)
			{
				$positive++;
			}
			else
			{
				$negative++;
			}

			if (is_null($last) || $entry->get('created') > $last)
			{
				$last = $entry->get('created');
			}
		}

		$balance = self::oneOrNewForScale($userId, $scale);
		$karma   = $scale->clamp($raw);

		$changed = ((float) $balance->get('raw') != $raw
			|| (float) $balance->get('karma') != $karma
			|| (int) $balance->get('positive_count') != $positive
			|| (int) $balance->get('negative_count') != $negative);

		if (!$changed && $balance->get('id'))
		{
			return false;
		}

		$balance->set(array(
			'raw'               => $raw,
			'karma'             => $karma,
			'positive_count'    => $positive,
			'negative_count'    => $negative,
			'last_event'        => $last,
			'last_recalculated' => $now
		));

		return (bool) $balance->save();
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
