<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Relational;

/**
 * One person's judgement of one moderation
 *
 * Kept after reconciliation rather than discarded. The row is what lets a
 * fairness counter be rebuilt if it drifts, and what lets somebody ask later
 * why a moderation was resolved the way it was — the same argument that keeps
 * the karma ledger append-only.
 */
class Review extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'moderation';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'created';

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
		'log_id'  => 'positive|nonzero',
		'user_id' => 'positive|nonzero'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created'
	);

	/**
	 * The moderation being judged
	 *
	 * @return  object
	 */
	public function log()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Log', 'log_id');
	}

	/**
	 * Whether this judgement said the moderation was fair
	 *
	 * @return  boolean
	 */
	public function isFair()
	{
		return ((int) $this->get('value') > 0);
	}

	/**
	 * Whether it still counts
	 *
	 * A judgement is set inactive rather than deleted when it has to be
	 * discounted, so the record of who said what survives.
	 *
	 * @return  boolean
	 */
	public function isActive()
	{
		return (bool) $this->get('active');
	}

	/**
	 * The judgements cast on one moderation
	 *
	 * @param   integer  $logId
	 * @return  object
	 */
	public static function forLog($logId)
	{
		return self::all()
			->whereEquals('log_id', (int) $logId)
			->whereEquals('active', 1);
	}

	/**
	 * How one moderation was judged
	 *
	 * Returns the count each way and the fraction that called it fair, which
	 * is what the consequences table is keyed on.
	 *
	 * @param   integer  $logId
	 * @return  object
	 */
	public static function tallyFor($logId)
	{
		$fair   = 0;
		$unfair = 0;

		foreach (self::forLog($logId)->rows() as $row)
		{
			$row->isFair() ? $fair++ : $unfair++;
		}

		$total = $fair + $unfair;

		return (object) array(
			'fair'     => $fair,
			'unfair'   => $unfair,
			'total'    => $total,
			'fraction' => $total ? ($fair / $total) : 0.0
		);
	}
}
