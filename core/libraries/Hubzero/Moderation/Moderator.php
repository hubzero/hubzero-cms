<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Moderation;

use Hubzero\Karma\Karma;
use Hubzero\Utility\Date;
use App;
use Event;

/**
 * One member, doing the moderating
 *
 *     $moderator = new Moderator($userId);
 *     $moderator->credits('com_forum.post');
 *     $moderator->canModerate($item);
 *     $moderator->moderate($item, 'substantive');
 */
class Moderator
{
	/**
	 * Whose credits these are
	 *
	 * @var  integer
	 */
	protected $userId = 0;

	/**
	 * Whether this member may moderate without spending
	 *
	 * @var  bool
	 */
	protected $unlimited = false;

	/**
	 * An explicitly supplied connection, for transactions
	 *
	 * @var  object
	 */
	protected static $connection = null;

	/**
	 * Why the last call refused
	 *
	 * @var  string
	 */
	protected $reason = '';

	/**
	 * Constructor
	 *
	 * @param   integer  $userId
	 * @param   bool     $unlimited  Holds the moderate-without-spending permission
	 * @return  void
	 */
	public function __construct($userId, $unlimited = false)
	{
		$this->userId    = (int) $userId;
		$this->unlimited = (bool) $unlimited;
	}

	/**
	 * Set the connection transactions are opened on
	 *
	 * @param   object  $connection
	 * @return  void
	 */
	public static function setConnection($connection)
	{
		self::$connection = $connection;
	}

	/**
	 * The connection to use
	 *
	 * @return  object
	 */
	protected static function connection()
	{
		return self::$connection ?: App::get('db');
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
	 * How many credits are spendable on this kind of item
	 *
	 * @param   string  $itemType
	 * @return  integer
	 */
	public function credits($itemType)
	{
		if ($this->unlimited)
		{
			return PHP_INT_MAX;
		}

		return Wallet::oneOrNew($this->userId, $itemType)->spendable();
	}

	/**
	 * The reasons that make sense for an item right now
	 *
	 * A reason that would push the score past a bound it is already against
	 * is not offered: there is nothing to be gained by spending a credit on
	 * it, and the moderation would be recorded inert.
	 *
	 * @param   object  $item
	 * @return  array
	 */
	public function reasonsFor(Moderatable $item)
	{
		list($min, $max) = $item->scoreBounds();

		$score  = (float) $item->currentScore();
		$usable = array();

		foreach (Reason::forType($item->itemType()) as $alias => $reason)
		{
			if (!$reason->get('listable'))
			{
				continue;
			}

			$value = (float) $reason->get('value');

			if ($value > 0 && $score >= $max)
			{
				continue;
			}

			if ($value < 0 && $score <= $min)
			{
				continue;
			}

			$usable[$alias] = $reason;
		}

		return $usable;
	}

	/**
	 * May this member moderate this item?
	 *
	 * @param   object  $item
	 * @return  bool
	 */
	public function canModerate(Moderatable $item)
	{
		$this->reason = '';

		if (!$this->userId)
		{
			return $this->refuse('nobody');
		}

		if ($this->userId === (int) $item->authorId())
		{
			return $this->refuse('own');
		}

		if (Log::exists($item->itemType(), $item->itemId(), $this->userId))
		{
			return $this->refuse('already');
		}

		// Having contributed here is disqualifying. This is the single most
		// effective restraint the model has: it stops moderation being a way
		// to win an argument you are already in.
		if ($this->hasContributedTo($item))
		{
			return $this->refuse('participant');
		}

		if (!$this->unlimited && $this->credits($item->itemType()) < 1)
		{
			return $this->refuse('credits');
		}

		return true;
	}

	/**
	 * Moderate
	 *
	 * Returns the log entry, or false. Ask why() when it refuses.
	 *
	 * @param   object  $item
	 * @param   string  $reasonAlias
	 * @param   array   $options      ip
	 * @return  mixed   object|false
	 */
	public function moderate(Moderatable $item, $reasonAlias, array $options = array())
	{
		if (!$this->canModerate($item))
		{
			return false;
		}

		$reason = Reason::oneByTypeAlias($item->itemType(), $reasonAlias);

		if (!$reason)
		{
			return $this->refuse('reason');
		}

		$delta = (float) $reason->get('value');
		$score = (float) $item->currentScore();

		list($min, $max) = $item->scoreBounds();

		// A moderation that cannot move the score still happened, still costs
		// a credit and is still reviewable. It is recorded inert rather than
		// refused, so that a moderator cannot probe the bounds for free.
		$target = $score + $delta;
		$moves  = ($target >= $min && $target <= $max);

		$db = self::connection();
		$db->transactionStart();

		try
		{
			$entry = Log::blank()->set(array(
				'item_type'     => $item->itemType(),
				'item_id'       => $item->itemId(),
				'container_id'  => (int) $item->containerId(),
				'user_id'       => $this->userId,
				'author_id'     => (int) $item->authorId(),
				'reason_id'     => (int) $reason->get('id'),
				'value'         => $delta,
				'credits_spent' => $this->unlimited ? 0 : 1,
				'score_before'  => $score,
				'active'        => $moves ? Log::ACTIVE : Log::INERT,
				'ip'            => isset($options['ip']) ? $options['ip'] : null
			));

			if (!$entry->save())
			{
				throw new \RuntimeException('Moderation: could not write the log: ' . implode('; ', $entry->getErrors()));
			}

			if (!$this->unlimited && !$this->spend($item->itemType()))
			{
				throw new \RuntimeException('Moderation: could not spend a credit');
			}

			if ($moves && !$item->applyScore($delta, $reason->get('id')))
			{
				throw new \RuntimeException('Moderation: the item refused the score');
			}

			$db->transactionCommit();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();

			return $this->refuse('failed');
		}

		\Hubzero\Database\Query::purgeCache();

		if ($moves)
		{
			$this->awardKarma($item, $reason, $entry);
		}

		Event::trigger('moderation.onModerationApplied', array($entry, $item, $reason));

		return $entry;
	}

	/**
	 * Reverse everything this member moderated inside a container
	 *
	 * Called when they contribute to it. Moderating and then joining in is
	 * the same conflict as joining in and then moderating; this resolves it
	 * the same way round either way.
	 *
	 * @param   string   $itemType
	 * @param   integer  $containerId
	 * @param   callable $resolver     Given an item id, returns a Moderatable
	 * @return  integer  moderations reversed
	 */
	public function undoIn($itemType, $containerId, $resolver = null)
	{
		$entries  = Log::inContainer($itemType, $containerId, $this->userId);
		$reversed = 0;

		foreach ($entries as $entry)
		{
			if ($entry->isActive() && is_callable($resolver))
			{
				if ($item = call_user_func($resolver, $entry->get('item_id')))
				{
					$item->applyScore(-(float) $entry->get('value'), $entry->get('reason_id'));
				}
			}

			if ($entry->get('author_id'))
			{
				Karma::revoke($itemType, $entry->get('item_id'), null, $entry->get('author_id'));
			}

			if ($entry->destroy())
			{
				$reversed++;
			}
		}

		if ($reversed)
		{
			\Hubzero\Database\Query::purgeCache();
		}

		return $reversed;
	}

	/**
	 * Has this member contributed to the container the item sits in?
	 *
	 * The library cannot answer this alone — only the component knows what
	 * contributing means. A component registers an answer; absent one, the
	 * restraint is simply not applied.
	 *
	 * @param   object  $item
	 * @return  bool
	 */
	protected function hasContributedTo(Moderatable $item)
	{
		$responses = Event::trigger('moderation.onModerationHasContributed', array(
			$item->itemType(), (int) $item->containerId(), $this->userId
		));

		foreach ((array) $responses as $response)
		{
			if ($response === true)
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Take one credit
	 *
	 * @param   string  $itemType
	 * @return  bool
	 */
	protected function spend($itemType)
	{
		$wallet = Wallet::oneOrNew($this->userId, $itemType);

		if ($wallet->spendable() < 1)
		{
			return false;
		}

		$wallet->set(array(
			'credits'           => (int) $wallet->get('credits') - 1,
			'total_moderations' => (int) $wallet->get('total_moderations', 0) + 1
		));

		return (bool) $wallet->save();
	}

	/**
	 * Move the author's karma, if the reason says to
	 *
	 * The reason names a karma rule rather than an amount, so what a
	 * moderation is worth stays the administrator's to set in one place.
	 *
	 * @param   object  $item
	 * @param   object  $reason
	 * @param   object  $entry
	 * @return  void
	 */
	protected function awardKarma(Moderatable $item, Reason $reason, Log $entry)
	{
		$rule = $reason->get('karma_rule');

		if (!$rule || !$item->authorId() || !class_exists('\\Hubzero\\Karma\\Karma'))
		{
			return;
		}

		Karma::award($item->authorId(), $rule, array(
			'actor'       => $this->userId,
			'source_type' => $item->itemType(),
			'source_id'   => $item->itemId(),
			'params'      => array('reason' => $reason->get('alias'))
		));
	}

	/**
	 * Record why, and refuse
	 *
	 * @param   string  $why
	 * @return  bool
	 */
	protected function refuse($why)
	{
		$this->reason = $why;

		return false;
	}
}
