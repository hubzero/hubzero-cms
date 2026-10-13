<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

use Components\Massmail\Models\Mailing;

/**
 * Expands a mailing's recipient spec into per-recipient rows.
 *
 * Everything is done with INSERT ... SELECT so a 100k-user expansion is a
 * single DB operation (no PHP hydration), and the UNIQUE (mailing_id, email)
 * key + INSERT IGNORE dedupes automatically across overlapping selections.
 *
 * Hard exclusions ALWAYS applied (even when opt-out is overridden):
 *   - blocked accounts         (block = 1)
 *   - never-activated accounts (activation <= 0; negative = pending token)
 *   - accounts with no email    (email = '')
 *   - placeholder addresses     (third-party-auth accounts get "-<id>@invalid")
 */
class Recipients
{
	/**
	 * The non-negotiable "this address is mailable at all" predicate.
	 *
	 * @return  string
	 */
	protected static function hardFilter()
	{
		$sql = "u.block = 0 AND u.activation > 0 AND u.email <> '' AND u.email NOT LIKE '%@invalid'";

		// Optional: also require the account-approval workflow to have passed.
		// Off by default (honor the operator's blocked/activation/email spec).
		$params = \Component::params('com_massmail');
		if ((int) $params->get('require_approved', 0))
		{
			$sql .= " AND u.approved > 0";
		}
		return $sql;
	}

	/**
	 * The opt-out / confirmation predicate (skipped entirely when overriding).
	 *
	 * @param   boolean  $ignoreOptout
	 * @return  string
	 */
	protected static function preferenceFilter($ignoreOptout)
	{
		$params = \Component::params('com_massmail');
		$sql    = '';

		// The "Receive email updates" choice on the member profile is stored in
		// #__users.sendEmail (-1 = no preference, 0 = no, 1 = yes); it is what
		// com_members reads and what the unsubscribe link writes.
		if (!$ignoreOptout)
		{
			if ((int) $params->get('no_preference_sendable', 1))
			{
				// Exclude only explicit opt-outs; "-1" (no preference) is mailable.
				$sql .= " AND COALESCE(u.sendEmail, -1) <> 0";
			}
			else
			{
				// Only users who explicitly opted in.
				$sql .= " AND COALESCE(u.sendEmail, -1) = 1";
			}
		}

		if ((int) $params->get('require_email_confirmed', 0))
		{
			$sql .= " AND COALESCE(x.emailConfirmed, 0) > 0";
		}
		return $sql;
	}

	/**
	 * Expand a mailing's recipients into #__massmail_recipients.
	 *
	 * @param   Mailing  $mailing
	 * @return  integer  total recipients queued
	 */
	public static function expand(Mailing $mailing)
	{
		$db  = \App::get('db');
		$mid = (int) $mailing->get('id');

		$spec = self::normalizeSpec(json_decode((string) $mailing->get('recipient_spec'), true));

		$ignoreOptout = (int) $mailing->get('ignore_optout') ? true : false;
		$hard = self::hardFilter();
		$pref = self::preferenceFilter($ignoreOptout);

		// Every populated source is queued; the UNIQUE (mailing_id, email) key +
		// INSERT IGNORE dedupe overlaps across all of them automatically.

		// --- All active users -------------------------------------------------
		if (!empty($spec['all']))
		{
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE $hard $pref"
			);
			$db->execute();
		}

		// --- Members of one or more groups -----------------------------------
		$gids = isset($spec['groups']) ? self::resolveGroups($spec['groups']) : array();
		if ($gids)
		{
			$in = implode(',', $gids);
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 JOIN `#__xgroups_members` gm ON gm.uidNumber = u.id
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE gm.gidNumber IN ($in) AND $hard $pref"
			);
			$db->execute();
		}

		// --- Specific users ---------------------------------------------------
		$uids = isset($spec['users']) ? self::resolveUsers($spec['users']) : array();
		if ($uids)
		{
			$in = implode(',', $uids);
			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE u.id IN ($in) AND $hard $pref"
			);
			$db->execute();
		}

		// --- Raw addresses (admin-entered) -----------------------------------
		// An address that belongs to an account is still that account's: the
		// hard and opt-out filters apply to it exactly as if the user had been
		// picked by id, so typing an address can't reach someone the picker
		// would have excluded. Only addresses with no account are sent as typed.
		$emails = self::validEmails(isset($spec['emails']) ? $spec['emails'] : array());
		if ($emails)
		{
			$in = implode(',', array_map(array($db, 'quote'), $emails));

			$db->setQuery(
				"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
				 SELECT $mid, u.id, u.email, u.name, 0
				 FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE LOWER(u.email) IN ($in) AND $hard $pref"
			);
			$db->execute();

			$db->setQuery("SELECT LOWER(email) FROM `#__users` WHERE LOWER(email) IN ($in)");
			$accounts = array_flip((array) $db->loadColumn());

			foreach ($emails as $em)
			{
				if (isset($accounts[$em]))
				{
					continue;
				}
				$db->setQuery(
					"INSERT IGNORE INTO `#__massmail_recipients` (mailing_id, uid, email, name, status)
					 VALUES ($mid, 0, " . $db->quote($em) . ", '', 0)"
				);
				$db->execute();
			}
		}

		$db->setQuery("SELECT COUNT(*) FROM `#__massmail_recipients` WHERE mailing_id = $mid");
		return (int) $db->loadResult();
	}

	/**
	 * Lower-cased, deduped, syntactically valid addresses from a raw list.
	 *
	 * @param   array  $emails
	 * @return  array
	 */
	protected static function validEmails($emails)
	{
		$out = array();
		foreach ((array) $emails as $em)
		{
			$em = strtolower(trim((string) $em));
			if ($em !== '' && filter_var($em, FILTER_VALIDATE_EMAIL))
			{
				$out[$em] = true;
			}
		}
		return array_keys($out);
	}

	/**
	 * Count active users who WOULD be reached, without queuing them.
	 * Used to preview a mailing before launch.
	 *
	 * @param   array    $spec
	 * @param   boolean  $ignoreOptout
	 * @return  integer
	 */
	public static function previewCount($spec, $ignoreOptout = false)
	{
		$spec = self::normalizeSpec($spec);
		$db   = \App::get('db');
		$hard = self::hardFilter();
		$pref = self::preferenceFilter($ignoreOptout);

		// Condition that selects the account-based recipients (all, groups, and
		// specific users combined). Counted as DISTINCT emails so overlaps between
		// the sources collapse -- matching what expand() actually queues.
		$conds = array();
		if (!empty($spec['all']))
		{
			$conds[] = '1=1';
		}
		else
		{
			$gids = self::resolveGroups($spec['groups']);
			if ($gids)
			{
				$conds[] = 'u.id IN (SELECT uidNumber FROM `#__xgroups_members` WHERE gidNumber IN (' . implode(',', $gids) . '))';
			}
			$uids = self::resolveUsers($spec['users']);
			if ($uids)
			{
				$conds[] = 'u.id IN (' . implode(',', $uids) . ')';
			}
		}

		$cond      = $conds ? '(' . implode(' OR ', $conds) . ')' : '';
		$userCount = 0;
		if ($cond)
		{
			$db->setQuery(
				"SELECT COUNT(DISTINCT u.email) FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE $cond AND $hard $pref"
			);
			$userCount = (int) $db->loadResult();
		}

		// Raw emails, counted the way expand() queues them: an address with an
		// account counts only if that account passes the filters and wasn't
		// already covered by a selection; one without an account is sent as typed.
		$raw      = self::validEmails($spec['emails']);
		$rawCount = count($raw);
		if ($rawCount)
		{
			$in = implode(',', array_map(array($db, 'quote'), $raw));

			// Addresses with an account that the filters exclude: not sent.
			$db->setQuery(
				"SELECT COUNT(DISTINCT LOWER(u.email)) FROM `#__users` u
				 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
				 WHERE LOWER(u.email) IN ($in) AND NOT ($hard $pref)"
			);
			$rawCount -= (int) $db->loadResult();

			// Addresses already reached through a selection: not double counted.
			if ($cond)
			{
				$db->setQuery(
					"SELECT COUNT(DISTINCT LOWER(u.email)) FROM `#__users` u
					 LEFT JOIN `#__xprofiles` x ON x.uidNumber = u.id
					 WHERE $cond AND $hard $pref AND LOWER(u.email) IN ($in)"
				);
				$rawCount -= (int) $db->loadResult();
			}
			$rawCount = max(0, $rawCount);
		}

		return $userCount + $rawCount;
	}

	/**
	 * Normalize a recipient spec to the additive form
	 * {all, groups, users, emails}, migrating the legacy single-"type" spec.
	 *
	 * @param   array  $spec
	 * @return  array
	 */
	public static function normalizeSpec($spec)
	{
		if (!is_array($spec))
		{
			return array('all' => false, 'groups' => array(), 'users' => array(), 'emails' => array());
		}

		// Legacy: one selected type carried the meaning.
		if (isset($spec['type']))
		{
			$t = $spec['type'];
			return array(
				'all'    => ($t === 'all'),
				'groups' => ($t === 'group') ? (array) (isset($spec['groups']) ? $spec['groups'] : array()) : array(),
				'users'  => ($t === 'user')  ? (array) (isset($spec['users'])  ? $spec['users']  : array()) : array(),
				'emails' => ($t === 'email') ? (array) (isset($spec['emails']) ? $spec['emails'] : array()) : array(),
			);
		}

		return array(
			'all'    => !empty($spec['all']),
			'groups' => isset($spec['groups']) ? (array) $spec['groups'] : array(),
			'users'  => isset($spec['users'])  ? (array) $spec['users']  : array(),
			'emails' => isset($spec['emails']) ? (array) $spec['emails'] : array(),
		);
	}

	/**
	 * Resolve a list of group tokens -- numeric group IDs and/or cn aliases --
	 * to group IDs. Lets the picker accept human-friendly aliases.
	 *
	 * @param   array  $tokens
	 * @return  array  group IDs
	 */
	public static function resolveGroups($tokens)
	{
		$tokens = array_filter(array_map('trim', (array) $tokens), 'strlen');
		if (!$tokens)
		{
			return array();
		}

		$db    = \App::get('db');
		$ids   = array();
		$names = array();
		foreach ($tokens as $t)
		{
			if (ctype_digit((string) $t)) { $ids[] = (int) $t; }
			else { $names[] = $db->quote($t); }
		}

		$where = array();
		if ($ids)   { $where[] = 'gidNumber IN (' . implode(',', $ids) . ')'; }
		if ($names) { $where[] = 'cn IN (' . implode(',', $names) . ')'; }
		if (!$where)
		{
			return array();
		}

		$db->setQuery("SELECT gidNumber FROM `#__xgroups` WHERE " . implode(' OR ', $where));
		return array_map('intval', (array) $db->loadColumn());
	}

	/**
	 * Resolve a list of user tokens -- numeric user IDs and/or usernames -- to
	 * user IDs. Lets the picker accept usernames.
	 *
	 * @param   array  $tokens
	 * @return  array  user IDs
	 */
	public static function resolveUsers($tokens)
	{
		$tokens = array_filter(array_map('trim', (array) $tokens), 'strlen');
		if (!$tokens)
		{
			return array();
		}

		$db    = \App::get('db');
		$ids   = array();
		$names = array();
		foreach ($tokens as $t)
		{
			if (ctype_digit((string) $t)) { $ids[] = (int) $t; }
			else { $names[] = $db->quote($t); }
		}

		$where = array();
		if ($ids)   { $where[] = 'id IN (' . implode(',', $ids) . ')'; }
		if ($names) { $where[] = 'username IN (' . implode(',', $names) . ')'; }
		if (!$where)
		{
			return array();
		}

		$db->setQuery("SELECT id FROM `#__users` WHERE " . implode(' OR ', $where));
		return array_map('intval', (array) $db->loadColumn());
	}

	/**
	 * Describe a list of group tokens (IDs and/or cn aliases) for display as
	 * chips: one entry per resolved group with a friendly label and its cn alias
	 * as the token.
	 *
	 * @param   array  $tokens
	 * @return  array  [['token' => cn, 'label' => 'Title'], ...]
	 */
	public static function describeGroups($tokens)
	{
		$gids = self::resolveGroups($tokens);
		if (!$gids)
		{
			return array();
		}

		$db = \App::get('db');
		$db->setQuery("SELECT gidNumber, cn, description FROM `#__xgroups` WHERE gidNumber IN (" . implode(',', $gids) . ") ORDER BY cn ASC");
		$out = array();
		foreach ((array) $db->loadObjectList() as $g)
		{
			$out[] = array('token' => $g->cn, 'label' => (trim((string) $g->description) !== '' ? $g->description : $g->cn));
		}
		return $out;
	}

	/**
	 * Describe a list of user tokens (IDs and/or usernames) for display as chips:
	 * one entry per resolved user with a friendly label and its ID as the token.
	 *
	 * @param   array  $tokens
	 * @return  array  [['token' => id, 'label' => 'Name (username)'], ...]
	 */
	public static function describeUsers($tokens)
	{
		$ids = self::resolveUsers($tokens);
		if (!$ids)
		{
			return array();
		}

		$db = \App::get('db');
		$db->setQuery("SELECT id, username, name FROM `#__users` WHERE id IN (" . implode(',', $ids) . ") ORDER BY name ASC");
		$out = array();
		foreach ((array) $db->loadObjectList() as $u)
		{
			$name  = trim((string) $u->name);
			$label = $name !== '' ? ($name . ' (' . $u->username . ')') : $u->username;
			$out[] = array('token' => (int) $u->id, 'label' => $label);
		}
		return $out;
	}
}
