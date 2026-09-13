<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Karma;

use Hubzero\Config\Registry;
use Hubzero\Utility\Date;
use App;
use Event;

/**
 * Site-wide reputation
 *
 * Karma is a number about a person on a named scale, moved only through
 * rules so that caps and bounds always apply, and recorded in an append-only
 * ledger so that every movement can be explained and undone.
 *
 *     Karma::award($userId, 'comment.upmod', ['actor' => $modId, 'source' => $comment]);
 *     Karma::of($userId, 'story.comment');
 *     Karma::gate('com_story.comments_per_day', $userId);
 */
class Karma
{
	/**
	 * The scale used when a caller names none
	 *
	 * @var  string
	 */
	const DEFAULT_SCALE = 'global';

	/**
	 * An explicitly supplied database connection
	 *
	 * Mirrors Relational::setDefaultConnection(). The award path opens a
	 * transaction, so it has to use the same connection the models write
	 * through — which under test is a mock driver, not the container's.
	 *
	 * @var  object
	 */
	protected static $connection = null;

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
	 * Now, as an SQL datetime on the current connection
	 *
	 * @return  string
	 */
	protected static function now()
	{
		return with(new Date('now'))->toSql(false, self::connection());
	}

	/**
	 * Record a message, when there is anywhere to record it
	 *
	 * The library is used from CLI, web and test contexts, and the Log facade
	 * is not bound in all of them.
	 *
	 * @param   string  $level
	 * @param   string  $message
	 * @return  void
	 */
	protected static function log($level, $message)
	{
		if (class_exists('\Log'))
		{
			try
			{
				\Log::{$level}($message);
			}
			catch (\Exception $e)
			{
				// Nowhere to log that logging failed.
			}
		}
	}

	/**
	 * Read a user's karma on a scale
	 *
	 * @param   integer  $userId
	 * @param   string   $scale
	 * @return  float
	 */
	public static function of($userId, $scale = self::DEFAULT_SCALE)
	{
		$scale = self::scale($scale);

		if (!$scale)
		{
			return 0.0;
		}

		$balance = Balance::oneByUserAndScale($userId, $scale->get('id'));

		if (!$balance->get('id'))
		{
			return (float) $scale->get('initial');
		}

		return (float) $balance->get('karma');
	}

	/**
	 * Describe a user's karma as the viewer is permitted to see it
	 *
	 * Returns null when the viewer may see nothing at all, which is a
	 * different answer from a karma of zero and should be rendered as
	 * absence rather than as a value.
	 *
	 * @param   integer  $userId  Whose karma
	 * @param   string   $scale   Which scale
	 * @param   integer  $viewer  Who is asking; defaults to the subject
	 * @param   bool     $isAdmin Whether the viewer may always see the exact value
	 * @return  mixed    string|null
	 */
	public static function describe($userId, $scale = self::DEFAULT_SCALE, $viewer = null, $isAdmin = false)
	{
		$scale = self::scale($scale);

		if (!$scale)
		{
			return null;
		}

		$value  = self::of($userId, $scale->get('alias'));
		$viewer = is_null($viewer) ? $userId : $viewer;

		// An administrator always sees the number. This is deliberately not
		// configurable: a setting that hides data from the person
		// investigating an abuse report helps nobody.
		if ($isAdmin)
		{
			return (string) $value;
		}

		if ((int) $viewer === (int) $userId)
		{
			return ($scale->get('visibility_self') == Scale::SELF_EXACT)
				? (string) $value
				: $scale->adjective($value);
		}

		switch ($scale->get('visibility_public'))
		{
			case Scale::PUBLIC_EXACT:
				return (string) $value;

			case Scale::PUBLIC_ADJECTIVE:
				return $scale->adjective($value);

			case Scale::PUBLIC_OPT_IN:
				return self::hasOptedIn($userId, $scale)
					? $scale->adjective($value)
					: null;

			case Scale::PUBLIC_HIDDEN:
			default:
				return null;
		}
	}

	/**
	 * Move a user's karma, through a rule
	 *
	 * The ledger row and the balance move together or not at all. Returns the
	 * ledger entry on success, or false when the award did not happen — an
	 * unknown rule, a veto, or a cap already reached. A missing rule is not an
	 * error: a component may emit events before anybody has decided what they
	 * are worth.
	 *
	 * @param   integer  $subjectId  Whose karma moves
	 * @param   string   $ruleAlias  Which rule applies
	 * @param   array    $options    actor, scale, source, params, expires
	 * @return  mixed    object|false
	 */
	public static function award($subjectId, $ruleAlias, array $options = array())
	{
		$subjectId = (int) $subjectId;

		if (!$subjectId)
		{
			return false;
		}

		$scale = null;

		if (isset($options['scale']))
		{
			$scale = self::scale($options['scale']);

			if (!$scale)
			{
				self::log('debug', 'Karma: unknown scale "' . $options['scale'] . '" for rule "' . $ruleAlias . '"');
				return false;
			}
		}

		$rule = Rule::oneByAlias($ruleAlias, $scale ? $scale->get('id') : null);

		if (!$rule || !$rule->get('id'))
		{
			self::log('debug', 'Karma: no active rule "' . $ruleAlias . '"');
			return false;
		}

		if (!$scale)
		{
			$scale = Scale::one($rule->get('scale_id'));
		}

		if (!$scale || !$scale->get('id'))
		{
			self::log('debug', 'Karma: rule "' . $ruleAlias . '" names a missing scale');
			return false;
		}

		list($sourceType, $sourceId) = self::source(isset($options['source']) ? $options['source'] : null, $options);

		$actorId = isset($options['actor']) ? (int) $options['actor'] : 0;

		// An actor below the rule's threshold contributes nothing. Their
		// action still happened; it simply does not move karma.
		if ($actorId && (int) $rule->get('requires_karma', 0) != 0)
		{
			if (self::of($actorId, $scale->get('alias')) < (float) $rule->get('requires_karma'))
			{
				return false;
			}
		}

		$context = array(
			'rule'        => $rule,
			'scale'       => $scale,
			'actor'       => $actorId,
			'source_type' => $sourceType,
			'source_id'   => $sourceId,
			'params'      => isset($options['params']) ? $options['params'] : array()
		);

		// A plugin may veto. This is where per-component abuse rules live.
		$responses = Event::trigger('karma.onKarmaBeforeAward', array($subjectId, $ruleAlias, $context));

		if (is_array($responses) && in_array(false, $responses, true))
		{
			return false;
		}

		$delta = (float) $rule->get('delta');

		if (!$delta)
		{
			return false;
		}

		if (self::capped($subjectId, $rule, $sourceType, $sourceId))
		{
			return false;
		}

		$db = self::connection();
		$db->transactionStart();

		try
		{
			$balance = Balance::oneOrNewForScale($subjectId, $scale);

			$before = (float) $balance->get('karma');
			$raw    = (float) $balance->get('raw') + $delta;
			$after  = $scale->clamp($raw);

			$entry = Ledger::blank()->set(array(
				'scale_id'    => (int) $scale->get('id'),
				'subject_id'  => $subjectId,
				'actor_id'    => $actorId,
				'delta'       => $delta,
				'applied'     => $after - $before,
				'rule'        => $rule->get('alias'),
				'source_type' => $sourceType,
				'source_id'   => $sourceId,
				'expires'     => isset($options['expires']) ? $options['expires'] : null,
				'state'       => Ledger::STATE_ACTIVE,
				'params'      => self::encode(isset($options['params']) ? $options['params'] : array())
			));

			if (!$entry->save())
			{
				throw new \RuntimeException('Karma: could not write ledger entry: ' . implode('; ', $entry->getErrors()));
			}

			$balance->set(array(
				'raw'          => $raw,
				'karma'        => $after,
				'last_event'   => self::now()
			));

			$counter = ($delta > 0) ? 'positive_count' : 'negative_count';
			$balance->set($counter, (int) $balance->get($counter, 0) + 1);

			if (!$balance->save())
			{
				throw new \RuntimeException('Karma: could not update balance: ' . implode('; ', $balance->getErrors()));
			}

			$db->transactionCommit();
		}
		catch (\Exception $e)
		{
			$db->transactionRollback();
			self::log('error', $e->getMessage());

			return false;
		}

		Event::trigger('karma.onKarmaAfterAward', array($entry, $balance));

		self::announceThresholds($subjectId, $scale, $before, $after);

		return $entry;
	}

	/**
	 * Reverse every active award made from one source
	 *
	 * Used when the action that earned the karma is undone — a moderation
	 * reversed, a submission unaccepted. The ledger rows are marked reversed
	 * rather than deleted, and compensating entries restore the balance, so
	 * the history still explains itself.
	 *
	 * @param   string   $sourceType
	 * @param   integer  $sourceId
	 * @param   string   $ruleAlias   Optional, to reverse only one rule
	 * @return  integer  Number of entries reversed
	 */
	public static function revoke($sourceType, $sourceId, $ruleAlias = null)
	{
		$query = Ledger::all()
			->whereEquals('source_type', (string) $sourceType)
			->whereEquals('source_id', (int) $sourceId)
			->whereEquals('state', Ledger::STATE_ACTIVE);

		if ($ruleAlias)
		{
			$query->whereEquals('rule', (string) $ruleAlias);
		}

		$entries = $query->rows();
		$count   = 0;

		foreach ($entries as $entry)
		{
			$scale = Scale::one($entry->get('scale_id'));

			if (!$scale->get('id'))
			{
				continue;
			}

			$db = self::connection();
			$db->transactionStart();

			try
			{
				$balance = Balance::oneOrNewForScale($entry->get('subject_id'), $scale);

				$raw   = (float) $balance->get('raw') - (float) $entry->get('delta');
				$after = $scale->clamp($raw);

				$balance->set(array(
					'raw'        => $raw,
					'karma'      => $after,
					'last_event' => self::now()
				));

				if (!$balance->save())
				{
					throw new \RuntimeException('Karma: could not update balance while revoking: ' . implode('; ', $balance->getErrors()));
				}

				$entry->set('state', Ledger::STATE_REVERSED);

				if (!$entry->save())
				{
					throw new \RuntimeException('Karma: could not mark entry reversed: ' . implode('; ', $entry->getErrors()));
				}

				$db->transactionCommit();

				$count++;
			}
			catch (\Exception $e)
			{
				$db->transactionRollback();
				self::log('error', $e->getMessage());
			}
		}

		return $count;
	}

	/**
	 * Place a user's karma in a gate's bands
	 *
	 * @param   string   $gateAlias
	 * @param   integer  $userId
	 * @return  mixed
	 */
	public static function gate($gateAlias, $userId)
	{
		$gate = Gate::oneByAlias($gateAlias);

		if (!$gate || !$gate->get('id'))
		{
			return null;
		}

		$scale = Scale::one($gate->get('scale_id'));

		return $gate->evaluate(self::of($userId, $scale->get('alias')));
	}

	/**
	 * Is a user at or above a threshold?
	 *
	 * @param   integer  $userId
	 * @param   float    $threshold
	 * @param   string   $scale
	 * @return  bool
	 */
	public static function atLeast($userId, $threshold, $scale = self::DEFAULT_SCALE)
	{
		return (self::of($userId, $scale) >= (float) $threshold);
	}

	/**
	 * What every gate currently allows this user
	 *
	 * Shown to the user themselves whatever a scale's visibility settings
	 * say: if karma restricts what somebody may do, they are told what they
	 * may do. A number without its consequences is not feedback.
	 *
	 * @param   integer  $userId
	 * @return  array
	 */
	public static function standing($userId)
	{
		$standing = array();

		foreach (Gate::all()->rows() as $gate)
		{
			$standing[$gate->get('alias')] = self::gate($gate->get('alias'), $userId);
		}

		return $standing;
	}

	/**
	 * Resolve a scale from an alias, an id, or a model
	 *
	 * @param   mixed  $scale
	 * @return  mixed  object|null
	 */
	public static function scale($scale)
	{
		if ($scale instanceof Scale)
		{
			return $scale;
		}

		if (is_numeric($scale))
		{
			$model = Scale::one((int) $scale);

			return $model->get('id') ? $model : null;
		}

		$model = Scale::oneByAlias($scale);

		return ($model && $model->get('id')) ? $model : null;
	}

	/**
	 * Has this user published their karma on a scale set to opt in?
	 *
	 * Phase 1 has no member preferences to read, so this answers no. The
	 * preference arrives with com_karma, and only this method changes.
	 *
	 * @param   integer  $userId
	 * @param   object   $scale
	 * @return  bool
	 */
	protected static function hasOptedIn($userId, Scale $scale)
	{
		return false;
	}

	/**
	 * Would this award exceed one of the rule's caps?
	 *
	 * @param   integer  $subjectId
	 * @param   object   $rule
	 * @param   string   $sourceType
	 * @param   integer  $sourceId
	 * @return  bool
	 */
	protected static function capped($subjectId, Rule $rule, $sourceType, $sourceId)
	{
		if ($rule->hasSourceCap() && $sourceType)
		{
			$used = Ledger::all()
				->whereEquals('subject_id', (int) $subjectId)
				->whereEquals('rule', $rule->get('alias'))
				->whereEquals('source_type', (string) $sourceType)
				->whereEquals('source_id', (int) $sourceId)
				->whereEquals('state', Ledger::STATE_ACTIVE)
				->total();

			if ($used >= (int) $rule->get('per_source_cap'))
			{
				return true;
			}
		}

		if ($rule->hasDailyCap())
		{
			$since = with(new Date('-1 day'))->toSql(false, self::connection());

			$used = Ledger::all()
				->whereEquals('subject_id', (int) $subjectId)
				->whereEquals('rule', $rule->get('alias'))
				->whereEquals('state', Ledger::STATE_ACTIVE)
				->where('created', '>=', $since)
				->total();

			if ($used >= (int) $rule->get('daily_cap'))
			{
				return true;
			}
		}

		return false;
	}

	/**
	 * Announce any adjective band the balance has crossed
	 *
	 * @param   integer  $userId
	 * @param   object   $scale
	 * @param   float    $before
	 * @param   float    $after
	 * @return  void
	 */
	protected static function announceThresholds($userId, Scale $scale, $before, $after)
	{
		if ($before == $after)
		{
			return;
		}

		foreach (array_keys(Bands::parse($scale->get('adjectives'))) as $threshold)
		{
			$threshold = (float) $threshold;

			$wasBelow = ($before <= $threshold);
			$isBelow  = ($after <= $threshold);

			if ($wasBelow !== $isBelow)
			{
				Event::trigger('karma.onKarmaCrossThreshold', array(
					$userId, $scale->get('alias'), $before, $after, $threshold
				));
			}
		}
	}

	/**
	 * Work out what a source is
	 *
	 * Accepts anything exposing getType()/getId(), a Relational model, or an
	 * explicit source_type/source_id pair in the options.
	 *
	 * @param   mixed  $source
	 * @param   array  $options
	 * @return  array  [string, int]
	 */
	protected static function source($source, array $options)
	{
		if (isset($options['source_type']))
		{
			return array(
				(string) $options['source_type'],
				isset($options['source_id']) ? (int) $options['source_id'] : 0
			);
		}

		if (is_object($source))
		{
			if (method_exists($source, 'getType') && method_exists($source, 'getId'))
			{
				return array((string) $source->getType(), (int) $source->getId());
			}

			if (method_exists($source, 'getTableName'))
			{
				return array(
					trim(str_replace('#__', '', $source->getTableName())),
					(int) $source->get('id')
				);
			}
		}

		return array('', 0);
	}

	/**
	 * Encode context for storage
	 *
	 * @param   mixed  $params
	 * @return  string
	 */
	protected static function encode($params)
	{
		if ($params instanceof Registry)
		{
			return $params->toString();
		}

		if (is_array($params) || is_object($params))
		{
			return json_encode($params);
		}

		return (string) $params;
	}
}
