<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Database\Query;
use Hubzero\Utility\Date;
use Hubzero\Karma\Karma;

/**
 * Judging whether a moderation was fair
 *
 * The second loop: moderation decides what a comment is worth, and review
 * decides whether the moderator was right. It exists because the first loop
 * has no other check on it — a moderator who sinks everything they disagree
 * with is otherwise invisible.
 *
 * It also has a volume problem, and the problem is not subtle. A judgement
 * needs several independent people to mean anything, and a fairness ratio
 * needs several judgements in each direction before it is more than noise. A
 * hub producing a handful of moderations a week can satisfy neither, and the
 * failure is silent: moderations sit unresolved forever and the counters never
 * populate. Hence Reviewer::hasVolumeFor(), and hence review shipping off.
 */
class Reviewer
{
	/**
	 * Whose judgement this is
	 *
	 * @var  integer
	 */
	protected $userId = 0;

	/**
	 * Judgements made but not yet written
	 *
	 * @var  array
	 */
	protected $pending = array();

	/**
	 * Why the last question was answered no
	 *
	 * @var  string
	 */
	protected $reason = '';

	/**
	 * A judgement that the moderation was fair
	 */
	const FAIR = 1;

	/**
	 * A judgement that it was not
	 */
	const UNFAIR = -1;

	/**
	 * Constructor
	 *
	 * @param   integer  $userId
	 * @return  void
	 */
	public function __construct($userId)
	{
		$this->userId = (int) $userId;
	}

	/**
	 * Why the last refusal happened
	 *
	 * @return  string
	 */
	public function why()
	{
		return $this->reason;
	}

	/**
	 * Refuse, and remember why
	 *
	 * @param   string  $code
	 * @return  boolean
	 */
	protected function refuse($code)
	{
		$this->reason = $code;

		return false;
	}

	/**
	 * Whether this member may be asked to judge anything
	 *
	 * @param   string  $itemType
	 * @param   array   $config
	 * @return  boolean
	 */
	public function isEligible($itemType, array $config = array())
	{
		$this->reason = '';

		if (!$this->userId)
		{
			return $this->refuse('nobody');
		}

		$minKarma = isset($config['review_min_karma']) ? (float) $config['review_min_karma'] : 0;
		$scale    = isset($config['karma_scale']) ? $config['karma_scale'] : 'global';

		if ($minKarma && Karma::of($this->userId, $scale) < $minKarma)
		{
			return $this->refuse('karma');
		}

		$wallet = Wallet::oneOrNew($this->userId, $itemType);

		// Somebody who has been judged unfair more often than fair is not the
		// person to be judging others. The threshold is deliberately generous:
		// this is meant to catch a pattern, not a bad afternoon.
		$fair   = (int) $wallet->get('reviews_fair');
		$unfair = (int) $wallet->get('reviews_unfair');

		if (($fair + $unfair) >= 10 && $unfair > $fair)
		{
			return $this->refuse('record');
		}

		$hours = isset($config['review_interval_hours']) ? (int) $config['review_interval_hours'] : 24;

		if ($hours && $wallet->get('last_review'))
		{
			$since = (time() - Date::of($wallet->get('last_review'))->toUnix()) / 3600;

			if ($since < $hours)
			{
				return $this->refuse('too_soon');
			}
		}

		return true;
	}

	/**
	 * Pick moderations for this member to judge
	 *
	 * Excluded: their own moderations, moderations of their own words, and
	 * anything they have judged already. The first two are the obvious
	 * conflicts; the third is what stops one person reaching consensus alone.
	 *
	 * Older unresolved moderations are preferred, because the ones at risk of
	 * never reaching consensus are the ones that have been waiting longest.
	 *
	 * @param   string   $itemType
	 * @param   integer  $count
	 * @return  array
	 */
	public function deal($itemType, $count = 10)
	{
		$count = max(1, (int) $count);

		$rows = Log::all()
			->whereEquals('item_type', $itemType)
			->whereEquals('review_status', Log::REVIEW_PENDING)
			->where('reviews_needed', '>', 0)
			->where('user_id', '!=', $this->userId)
			->where('author_id', '!=', $this->userId)
			->order('created', 'asc')
			->limit($count * 4)
			->rows();

		$judged = $this->alreadyJudged();
		$out    = array();

		foreach ($rows as $row)
		{
			if (in_array((int) $row->get('id'), $judged))
			{
				continue;
			}

			$out[] = $row;

			if (count($out) >= $count)
			{
				break;
			}
		}

		return $out;
	}

	/**
	 * The moderations this member has already judged
	 *
	 * @return  array
	 */
	protected function alreadyJudged()
	{
		$out = array();

		foreach (Review::all()->whereEquals('user_id', $this->userId)->rows() as $row)
		{
			$out[] = (int) $row->get('log_id');
		}

		return $out;
	}

	/**
	 * Remember a judgement, without writing it yet
	 *
	 * Held until commit() so that a member works through a dealt batch and
	 * submits it as one act. Judging one at a time would let somebody watch
	 * the consensus move and then place themselves on the winning side.
	 *
	 * @param   integer  $logId
	 * @param   integer  $value
	 * @return  $this
	 */
	public function record($logId, $value)
	{
		$value = ($value >= 0) ? self::FAIR : self::UNFAIR;

		$this->pending[(int) $logId] = $value;

		return $this;
	}

	/**
	 * What is waiting to be written
	 *
	 * @return  array
	 */
	public function pending()
	{
		return $this->pending;
	}

	/**
	 * Write the batch
	 *
	 * Reconciliation is deliberately not done here. A judgement that tips a
	 * moderation over the consensus line should not make the person who
	 * happened to cast it wait for the arithmetic, and doing the sums in cron
	 * means one pass handles every moderation that matured since the last one.
	 *
	 * @return  integer  how many judgements were written
	 */
	public function commit()
	{
		if (!$this->pending || !$this->userId)
		{
			return 0;
		}

		$judged  = $this->alreadyJudged();
		$written = 0;

		foreach ($this->pending as $logId => $value)
		{
			if (in_array((int) $logId, $judged))
			{
				continue;
			}

			$log = Log::oneOrNew((int) $logId);

			if (!$log->get('id')
			 || $log->get('review_status') != Log::REVIEW_PENDING
			 || (int) $log->get('user_id') === $this->userId
			 || (int) $log->get('author_id') === $this->userId)
			{
				continue;
			}

			$review = Review::blank();
			$review->set(array(
				'log_id'  => (int) $logId,
				'user_id' => $this->userId,
				'value'   => (int) $value,
				'active'  => 1,
				'created' => Date::of('now')->toSql()
			));

			if (!$review->save())
			{
				continue;
			}

			$log->set('review_count', (int) $log->get('review_count') + 1);
			$log->save();

			$written++;
		}

		if ($written)
		{
			$wallet = Wallet::oneOrNew($this->userId, $this->itemTypeOf(array_keys($this->pending)));
			$wallet->set('last_review', Date::of('now')->toSql());
			$wallet->save();

			Query::purgeCache();
		}

		$this->pending = array();

		return $written;
	}

	/**
	 * The item type a batch of log ids belongs to
	 *
	 * @param   array  $logIds
	 * @return  string
	 */
	protected function itemTypeOf(array $logIds)
	{
		foreach ($logIds as $id)
		{
			$log = Log::oneOrNew((int) $id);

			if ($log->get('id'))
			{
				return $log->get('item_type');
			}
		}

		return '';
	}

	/**
	 * Whether a hub is producing enough moderation for review to work
	 *
	 * The check that keeps review from being switched on where it cannot
	 * function. A review system that never reaches consensus is worse than
	 * none at all: it leaves moderations unresolved indefinitely, the fairness
	 * counters stay empty, and the eligibility rules that depend on them
	 * quietly stop meaning anything.
	 *
	 * @param   string   $itemType
	 * @param   integer  $consensus  votes needed to resolve one moderation
	 * @param   integer  $days       how far back to look
	 * @return  boolean
	 */
	public static function hasVolumeFor($itemType, $consensus = 9, $days = 30)
	{
		$since = Date::of('now')->subtract($days . ' days')->toSql();

		$produced = Log::all()
			->whereEquals('item_type', $itemType)
			->where('created', '>=', $since)
			->total();

		return ($produced >= ($consensus * 10));
	}
}
