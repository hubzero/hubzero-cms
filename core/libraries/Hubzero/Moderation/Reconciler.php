<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Query;
use Hubzero\Karma\Karma;

/**
 * Settling up moderations that have been judged
 *
 * The mechanism lives here rather than in the cron plugin, because when it
 * runs and what it does are separate questions. The plugin answers the first;
 * this answers the second, and can be exercised without a scheduler.
 */
class Reconciler
{
	/**
	 * The type being settled
	 *
	 * @var  string
	 */
	protected $itemType = '';

	/**
	 * The consequences in force
	 *
	 * @var  object
	 */
	protected $table = null;

	/**
	 * Judgements needed before a moderation is settled
	 *
	 * @var  integer
	 */
	protected $consensus = 9;

	/**
	 * The karma scale a moderator answers on
	 *
	 * @var  string
	 */
	protected $scale = 'global';

	/**
	 * Constructor
	 *
	 * @param   string  $itemType
	 * @param   object  $table
	 * @param   array   $config
	 * @return  void
	 */
	public function __construct($itemType, Consequences $table = null, array $config = array())
	{
		$this->itemType  = $itemType;
		$this->table     = $table ?: new Consequences();
		$this->consensus = isset($config['review_consensus']) ? max(1, (int) $config['review_consensus']) : 9;
		$this->scale     = isset($config['karma_scale']) ? $config['karma_scale'] : 'global';
	}

	/**
	 * Settle everything that has been judged enough times
	 *
	 * @return  integer  how many were settled
	 */
	public function run()
	{
		$matured = Log::all()
			->whereEquals('item_type', $this->itemType)
			->whereEquals('review_status', Log::REVIEW_PENDING)
			->where('review_count', '>=', $this->consensus)
			->rows();

		$settled = 0;

		foreach ($matured as $entry)
		{
			if ($this->settle($entry))
			{
				$settled++;
			}
		}

		if ($settled)
		{
			Query::purgeCache();
		}

		return $settled;
	}

	/**
	 * Settle one moderation
	 *
	 * @param   object  $entry
	 * @return  boolean
	 */
	public function settle($entry)
	{
		$tally = Review::tallyFor($entry->get('id'));

		if (!$tally->total)
		{
			return false;
		}

		$outcome = $this->table->forFraction($tally->fraction);
		$fairly  = ($tally->fraction >= 0.5);

		$this->settleModerator($entry, $outcome, $fairly);
		$this->settleReviewers($entry, $outcome, $fairly);

		$entry->set('review_status', Log::REVIEW_RESOLVED);
		$entry->save();

		return true;
	}

	/**
	 * What follows for the moderator
	 *
	 * @param   object   $entry
	 * @param   object   $outcome
	 * @param   boolean  $fairly
	 * @return  void
	 */
	protected function settleModerator($entry, $outcome, $fairly)
	{
		if (!$moderator = (int) $entry->get('user_id'))
		{
			return;
		}

		$wallet = Wallet::oneOrNew($moderator, $this->itemType);

		if ($outcome->moderator_credits)
		{
			$wallet->set('credits', max(0, (int) $wallet->get('credits') + $outcome->moderator_credits));
		}

		// Which way the moderation went decides which counter moves, so that
		// somebody who is only ever unfair downward shows up as exactly that
		// rather than being averaged into one number that hides it.
		$upward = ((float) $entry->get('value') > 0);

		if ($upward)
		{
			$column = $fairly ? 'up_fair' : 'up_unfair';
		}
		else
		{
			$column = $fairly ? 'down_fair' : 'down_unfair';
		}

		$wallet->set($column, (int) $wallet->get($column) + 1);
		$wallet->save();

		if ($outcome->moderator_karma)
		{
			Karma::adjust(
				$moderator,
				$this->scale,
				$outcome->moderator_karma,
				'moderation.reviewed',
				array(
					'source_type' => $this->itemType . '.moderation',
					'source_id'   => (int) $entry->get('id')
				)
			);
		}
	}

	/**
	 * What follows for the people who judged it
	 *
	 * @param   object   $entry
	 * @param   object   $outcome
	 * @param   boolean  $fairly
	 * @return  void
	 */
	protected function settleReviewers($entry, $outcome, $fairly)
	{
		foreach (Review::forLog($entry->get('id'))->rows() as $review)
		{
			$agreed = ($review->isFair() === $fairly);
			$wallet = Wallet::oneOrNew($review->get('user_id'), $this->itemType);

			foreach (array(
				$agreed ? 'reviews_fair' : 'reviews_unfair',
				$agreed ? 'reviews_voted_with' : 'reviews_voted_alone'
			) as $column)
			{
				$wallet->set($column, (int) $wallet->get($column) + 1);
			}

			$credits = $agreed ? $outcome->agreed_credits : $outcome->disagreed_credits;

			if ($credits)
			{
				$wallet->set('credits', max(0, (int) $wallet->get('credits') + $credits));
			}

			$wallet->save();
		}
	}
}
