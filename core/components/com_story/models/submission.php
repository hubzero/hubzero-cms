<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Story\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Hubzero\Utility\Date;
use Hubzero\Item\Vote;
use User;

/**
 * Something a reader thinks should be a story
 *
 * The queue is ranked rather than ordered by arrival, because a queue ordered
 * by arrival is one an editor stops reading at the third entry. What lifts an
 * entry is other readers, weighted by their standing; what sinks it is time.
 */
class Submission extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'story';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'popularity';

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
		'subject' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Where a submission stands
	 *
	 * Held is not a soft rejection: it means an editor has looked and wants
	 * it kept, usually because it needs something the submitter has not
	 * supplied yet.
	 */
	const STATE_PENDING  = 'pending';
	const STATE_HOLD     = 'hold';
	const STATE_ACCEPTED = 'accepted';
	const STATE_REJECTED = 'rejected';
	const STATE_SPAM     = 'spam';

	/**
	 * What this component calls a submission when it votes on one
	 *
	 * Hyphens rather than the dotted form the rest of the component uses,
	 * because Hubzero\Item\Vote normalises item types on the way in — it
	 * strips everything but letters, digits and hyphens and lowercases the
	 * rest. A dotted name would be stored as something else entirely and
	 * every query for it would quietly match nothing. This spelling survives
	 * that filter unchanged, so what is written is what is read back.
	 *
	 * @var  string
	 */
	const VOTE_TYPE = 'com-story-submission';

	/**
	 * The states a reader may see in the public queue
	 *
	 * @return  array
	 */
	public static function openStates()
	{
		return array(self::STATE_PENDING, self::STATE_HOLD);
	}

	/**
	 * Transform params into a Registry
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$this->params = new Registry($this->get('params'));
		}

		return $this->params;
	}

	/**
	 * Its votes
	 *
	 * @return  object
	 */
	public function votes()
	{
		return Vote::all()
			->whereEquals('item_type', self::VOTE_TYPE)
			->whereEquals('item_id', (int) $this->get('id'));
	}

	/**
	 * What one voter's opinion is worth
	 *
	 * Standing is the clout. Bounded at both ends on purpose: a voter with no
	 * standing still counts for something at the bottom, and the best-regarded
	 * member on the hub is worth a few ordinary readers rather than a hundred.
	 * An unbounded weight is how a queue becomes one person's opinion.
	 *
	 * @param   integer  $userId
	 * @param   object   $config
	 * @return  float
	 */
	public static function weightOf($userId, $config = null)
	{
		$config = $config ?: new Registry();
		$bands  = $config->get('vote_weight_by_karma', '-5=0|0=1|10=2|99999=3');

		if (!$userId)
		{
			return 0;
		}

		$karma = 0;

		// A hub with no karma installed still has a queue, and it should rank
		// by raw votes rather than fall over. Standing it cannot read is
		// standing of zero, which lands every voter on the neutral band.
		if (class_exists('\\Hubzero\\Karma\\Karma'))
		{
			try
			{
				$karma = (float) \Hubzero\Karma\Karma::of((int) $userId);
			}
			catch (\Throwable $e)
			{
				$karma = 0;
			}
		}

		return (float) \Hubzero\Karma\Bands::lookup($bands, $karma, 1);
	}

	/**
	 * The score a submission starts from, before anybody votes
	 *
	 * @param   string  $state
	 * @param   object  $config
	 * @return  float
	 */
	public static function baseFor($state, $config = null)
	{
		$config = $config ?: new Registry();
		$bases  = $config->get('popularity_base', 'pending=100|hold=90|accepted=120|rejected=0');

		foreach (explode('|', $bases) as $pair)
		{
			$bits = explode('=', $pair, 2);

			if (count($bits) == 2 && trim($bits[0]) === $state)
			{
				return (float) $bits[1];
			}
		}

		return 0;
	}

	/**
	 * Work out both rankings again from the votes themselves
	 *
	 * Recomputed rather than incremented, so a revoked vote, a karma change
	 * or a direct edit cannot leave the ranking drifting away from what the
	 * votes actually say.
	 *
	 * @param   object  $config
	 * @return  $this
	 */
	public function rescore($config = null)
	{
		$config = $config ?: new Registry();

		$public = self::baseFor($this->get('state'), $config);
		$editor = 0;

		foreach ($this->votes()->rows() as $vote)
		{
			$voter  = (int) $vote->get('created_by');
			$weight = self::weightOf($voter, $config) * (int) $vote->get('vote');

			$public += $weight;

			// Editors signal to each other on a separate tally, so that
			// "I would run this" does not quietly move what readers see.
			if ($voter && self::canPublish($voter))
			{
				$editor += $weight;
			}
		}

		$this->set('popularity', $public);
		$this->set('editor_popularity', $editor);
		$this->set('last_scored', Date::of('now')->toSql());

		return $this;
	}

	/**
	 * Whether a voter is one of the people who could run the thing
	 *
	 * Only the editors' own tally depends on this. If the question cannot be
	 * answered the voter simply does not count toward it — the public ranking,
	 * which is what readers see, is unaffected either way.
	 *
	 * @param   integer  $userId
	 * @return  boolean
	 */
	protected static function canPublish($userId)
	{
		try
		{
			return (bool) User::getInstance($userId)->authorise('story.publish', 'com_story');
		}
		catch (\Throwable $e)
		{
			return false;
		}
	}

	/**
	 * Let time take a submission back toward its resting score
	 *
	 * A three-week-old entry nobody voted for sinks out of the way without
	 * anybody having to reject it, which is the difference between a queue an
	 * editor can work and a queue an editor avoids.
	 *
	 * @param   object  $config
	 * @param   object  $now     for tests; defaults to now
	 * @return  $this
	 */
	public function decay($config = null, $now = null)
	{
		$config   = $config ?: new Registry();
		$halflife = (float) $config->get('popularity_halflife_hours', 168);

		if ($halflife <= 0 || !$this->get('last_scored'))
		{
			return $this;
		}

		$now   = $now ?: Date::of('now');
		$then  = Date::of($this->get('last_scored'));
		$hours = ($now->toUnix() - $then->toUnix()) / 3600;

		if ($hours <= 0)
		{
			return $this;
		}

		$base     = self::baseFor($this->get('state'), $config);
		$distance = (float) $this->get('popularity') - $base;

		// Halve what is left of the distance for every half-life elapsed.
		$remaining = $distance * pow(0.5, $hours / $halflife);

		$this->set('popularity', $base + $remaining);
		$this->set('last_scored', $now->toSql());

		return $this;
	}

	/**
	 * Which display band this sits in
	 *
	 * Computed at render time from a list of cut points rather than stored,
	 * so retuning the bands is a settings change and not a batch job.
	 *
	 * @param   object  $config
	 * @return  integer  1 is the hottest
	 */
	public function band($config = null)
	{
		$config = $config ?: new Registry();
		$points = $config->get('popularity_bands', '200|150|110|90|60');

		$band       = 1;
		$popularity = (float) $this->get('popularity');

		foreach (explode('|', $points) as $point)
		{
			if ($popularity >= (float) $point)
			{
				return $band;
			}

			$band++;
		}

		return $band;
	}

	/**
	 * Whether this reader has already voted on it
	 *
	 * @param   integer  $userId
	 * @return  boolean
	 */
	public function hasVoteFrom($userId)
	{
		if (!$userId)
		{
			return false;
		}

		return (bool) $this->votes()
			->whereEquals('created_by', (int) $userId)
			->total();
	}

	/**
	 * What is still waiting on an editor
	 *
	 * @return  object
	 */
	public static function open()
	{
		return self::all()->whereIn('state', self::openStates());
	}

	/**
	 * Whether an editor has finished with it
	 *
	 * @return  boolean
	 */
	public function isDecided()
	{
		return in_array($this->get('state'), array(
			self::STATE_ACCEPTED,
			self::STATE_REJECTED,
			self::STATE_SPAM
		));
	}
}
