<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Saml\Models;

use Hubzero\Database\Relational;

/**
 * Model for a SAML SSO session issued to a Service Provider
 *
 * Rows map the opaque SessionIndex sent to the SP back to the hub
 * session/user. They are marked ended rather than deleted so the table
 * doubles as the SSO audit trail; cleanup is admin-driven (DESIGN.md §5).
 */
class SamlSession extends Relational
{
	/**
	 * Session states
	 */
	const STATE_ENDED  = 0;
	const STATE_ACTIVE = 1;

	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'saml';

	/**
	 * The table to which the class pertains
	 *
	 * @var  string
	 */
	protected $table = '#__saml_sessions';

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
		'session_index' => 'notempty'
	);

	/**
	 * Has an AuthnRequest from this SP already been answered?
	 *
	 * Replay protection lives here rather than in the cache because hubs
	 * ship with caching disabled, which would leave a cache-based guard
	 * silently doing nothing. The audit row that records the answer is the
	 * same row that proves the request was used.
	 *
	 * @param   object  $sp         ServiceProvider
	 * @param   string  $requestId
	 * @return  boolean
	 */
	public static function wasRequestAnswered($sp, $requestId)
	{
		if (!$requestId)
		{
			return false;
		}

		return self::blank()
			->whereEquals('sp_id', (int) $sp->get('id'))
			->whereEquals('request_id', (string) $requestId)
			->total() > 0;
	}

	/**
	 * Record a new SSO session for a user/SP pair
	 *
	 * Deliberately not named start(): Relational::start() is the query
	 * offset setter that paginated() calls, and shadowing it breaks every
	 * paginated listing of this model.
	 *
	 * @param   object  $user       \Hubzero\User\User
	 * @param   object  $sp         ServiceProvider
	 * @param   string  $requestId  The AuthnRequest this answers
	 * @return  object  SamlSession
	 */
	public static function open($user, $sp, $requestId = '')
	{
		$row = self::blank();
		$row->set('session_index', '_' . bin2hex(random_bytes(20)));
		// NULL rather than '' so the unique (sp_id, request_id) index does
		// not collide across rows that answer no particular request
		$row->set('request_id', $requestId ? (string) $requestId : null);
		$row->set('hub_session_id', (string) \App::get('session')->getId());
		$row->set('user_id', (int) $user->get('id'));
		$row->set('sp_id', (int) $sp->get('id'));
		$row->set('state', self::STATE_ACTIVE);
		$row->set('created', \Date::toSql());

		// The unique index is the real guard: two requests racing the
		// wasRequestAnswered() check cannot both land a row.
		try
		{
			$row->save();
		}
		catch (\Exception $e)
		{
			return self::blank();
		}

		return $row;
	}

	/**
	 * Retrieves one row loaded by its session index
	 *
	 * @param   string  $index
	 * @return  mixed
	 */
	public static function oneByIndex($index)
	{
		return self::blank()
			->whereEquals('session_index', (string) $index)
			->row();
	}

	/**
	 * The most recent active session a user holds with an SP
	 *
	 * SessionIndex is optional in a LogoutRequest, so this resolves the
	 * subject-only form.
	 *
	 * @param   integer  $userId
	 * @param   integer  $spId
	 * @return  mixed
	 */
	public static function oneActiveForUser($userId, $spId)
	{
		return self::blank()
			->whereEquals('user_id', (int) $userId)
			->whereEquals('sp_id', (int) $spId)
			->whereEquals('state', self::STATE_ACTIVE)
			->order('created', 'desc')
			->row();
	}

	/**
	 * Is this session still active?
	 *
	 * @return  boolean
	 */
	public function isActive()
	{
		return (int) $this->get('state') == self::STATE_ACTIVE;
	}

	/**
	 * Mark the session ended (logout or expiry) — preserves the audit row
	 *
	 * @return  boolean
	 */
	public function end()
	{
		$this->set('state', self::STATE_ENDED);
		$this->set('ended', \Date::toSql());

		return $this->save();
	}

	/**
	 * The SP this session was issued for
	 *
	 * @return  object
	 */
	public function sp()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\ServiceProvider', 'sp_id');
	}

	/**
	 * The user the session belongs to
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'user_id');
	}

	/**
	 * Is #__session an authoritative record of live hub sessions?
	 *
	 * It is only written when the hub stores sessions in the database; under
	 * any other handler the table is empty and proves nothing.
	 *
	 * @return  boolean
	 */
	public static function sessionTableIsAuthoritative()
	{
		return \Config::get('session_handler') == 'database';
	}

	/**
	 * Rows eligible for the admin "delete expired" action.
	 *
	 * Always: rows already marked ended. Additionally, where the hub stores
	 * sessions in the database, rows still marked active whose hub session is
	 * gone — those ended without a logout request, so nothing will ever mark
	 * them, and their SessionIndex can no longer resolve to anything.
	 *
	 * A row backed by a live hub session is never included. Row age is never
	 * used to decide: the hub's session lifetime is an inactivity timeout
	 * rather than a hard cap, so age cannot tell a live session from a dead
	 * one.
	 *
	 * Automatic lifecycle still only ever marks rows ended; deletion happens
	 * solely when an admin asks for it.
	 *
	 * @return  object
	 */
	public static function allExpired()
	{
		$where = '`state` = ' . self::STATE_ENDED;

		if (self::sessionTableIsAuthoritative())
		{
			$where .= ' OR `hub_session_id` NOT IN (SELECT `session_id` FROM `#__session`)';
		}

		return self::blank()->whereRaw('(' . $where . ')');
	}

	/**
	 * End every active SSO session belonging to a user
	 *
	 * Hub logout deletes every #__session row for the user, not just the one
	 * in the browser (see plg_user_hubzero), so scoping this to a single hub
	 * session would strand the rest as permanently active and make later
	 * logout requests for them answer PartialLogout forever.
	 *
	 * @param   integer  $userId
	 * @return  integer  Number of sessions ended
	 */
	public static function endAllForUser($userId)
	{
		$rows = self::blank()
			->whereEquals('user_id', (int) $userId)
			->whereEquals('state', self::STATE_ACTIVE)
			->rows();

		$i = 0;

		foreach ($rows as $row)
		{
			if ($row->end())
			{
				$i++;
			}
		}

		return $i;
	}
}
